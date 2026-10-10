<?php
$message = "Sedang mencoba membersihkan cache...<br>";

// 1. Coba hapus file di bootstrap/cache
$cachePath = __DIR__ . '/../bootstrap/cache';
if (is_dir($cachePath)) {
    $files = glob($cachePath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $message .= "Berhasil menghapus: " . basename($file) . "<br>";
        }
    }
} else {
    $message .= "Folder bootstrap/cache tidak ditemukan.<br>";
}

// 2. Coba hapus file di storage/framework/views
$viewsPath = __DIR__ . '/../storage/framework/views';
if (is_dir($viewsPath)) {
    $files = glob($viewsPath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    $message .= "Berhasil menghapus semua view cache di storage/framework/views.<br>";
} else {
    $message .= "Folder storage/framework/views tidak ditemukan.<br>";
}

echo "<h3>$message</h3>";
echo "<b>Selesai! Silakan refresh halaman web Anda (Ctrl+F5).</b>";
