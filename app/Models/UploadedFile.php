<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadedFile extends Model
{
    protected $table = 'uploaded_files';

    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'content',
    ];

    /**
     * Store an uploaded file safely, handling serverless read-only filesystems
     * and persisting the file binary to the database.
     */
    public static function storeUploadedFile($file, string $directory, string $disk = 'public', ?string $customFilename = null): string
    {
        $directory = trim($directory, '/');
        $filename = $customFilename ?: $file->hashName();
        $relativePath = $directory . '/' . $filename;

        // Capture content and metadata before any move/store operations
        $content = '';
        $originalName = $filename;
        $mimeType = 'application/octet-stream';

        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType() ?: ($file->getMimeType() ?: 'application/octet-stream');
            try {
                $realPath = $file->getRealPath();
                if ($realPath && file_exists($realPath)) {
                    $content = file_get_contents($realPath);
                }
            } catch (\Throwable $e) {}
        }

        // 1. Try Laravel native storage store / storeAs
        $stored = false;
        try {
            if ($customFilename) {
                $file->storeAs($directory, $customFilename, $disk);
            } else {
                $file->store($directory, $disk);
            }
            $stored = true;
        } catch (\Throwable $e) {
            Log::warning('Native store failed: ' . $e->getMessage());
        }

        // 2. Always write to /tmp storage cache for current lambda if we have content
        if (!empty($content)) {
            try {
                $tmpDir = '/tmp/storage/app/' . $disk . '/' . $directory;
                if (!is_dir($tmpDir)) {
                    @mkdir($tmpDir, 0777, true);
                }
                @file_put_contents($tmpDir . '/' . $filename, $content);
            } catch (\Throwable $e) {
                Log::warning('Fallback tmp store failed: ' . $e->getMessage());
            }

            // 3. Persist binary to PostgreSQL database for serverless persistence
            try {
                self::updateOrCreate(
                    ['path' => $relativePath],
                    [
                        'disk' => $disk,
                        'original_name' => $originalName,
                        'mime_type' => $mimeType,
                        'size' => strlen($content),
                        'content' => base64_encode($content),
                    ]
                );
            } catch (\Throwable $e) {
                Log::warning('UploadedFile save to DB failed: ' . $e->getMessage());
            }
        }

        return $relativePath;
    }

    /**
     * Save or update binary content to database
     */
    public static function saveFile($file, string $path, string $disk = 'public')
    {
        try {
            $content = '';
            $originalName = basename($path);
            $mimeType = 'application/octet-stream';

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
                $realPath = $file->getRealPath();
                if ($realPath && file_exists($realPath)) {
                    $content = file_get_contents($realPath);
                }
            } elseif (is_string($file)) {
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    $mimeType = mime_content_type($file) ?: 'application/octet-stream';
                } else {
                    $content = $file;
                }
            }

            if (!empty($content)) {
                return self::updateOrCreate(
                    ['path' => $path],
                    [
                        'disk' => $disk,
                        'original_name' => $originalName,
                        'mime_type' => $mimeType,
                        'size' => strlen($content),
                        'content' => base64_encode($content),
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning('UploadedFile::saveFile warning: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Safely delete a file from disk, tmp, and database
     */
    public static function deleteFile(?string $path, string $disk = 'public')
    {
        if (!$path) {
            return;
        }

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (\Throwable $e) {}

        try {
            $tmpPath = '/tmp/storage/app/' . $disk . '/' . $path;
            if (file_exists($tmpPath)) {
                @unlink($tmpPath);
            }
        } catch (\Throwable $e) {}

        try {
            self::where('path', $path)->delete();
        } catch (\Throwable $e) {}
    }
}
