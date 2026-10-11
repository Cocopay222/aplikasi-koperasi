<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

putenv('VIEW_COMPILED_PATH=/tmp');
putenv('LOG_CHANNEL=stderr');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';

$storageDir = '/tmp/storage';
putenv("LARAVEL_STORAGE_PATH={$storageDir}");
$_ENV['LARAVEL_STORAGE_PATH'] = $storageDir;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storageDir;

$dirsToCreate = [
    $storageDir,
    $storageDir . '/app',
    $storageDir . '/app/public',
    $storageDir . '/app/public/lampiran_tiket',
    $storageDir . '/app/public/avatars',
    $storageDir . '/app/public/berita_acara',
    $storageDir . '/app/public/surat_balasan',
    $storageDir . '/app/public/ebooks',
    $storageDir . '/app/private',
    $storageDir . '/framework',
    $storageDir . '/framework/cache',
    $storageDir . '/framework/cache/data',
    $storageDir . '/framework/sessions',
    $storageDir . '/framework/views',
    $storageDir . '/logs',
];
foreach ($dirsToCreate as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

// Force Supabase connection for Vercel Runtime
$dbVars = [
    'DB_CONNECTION' => 'pgsql',
    'DB_HOST' => 'aws-0-ap-southeast-1.pooler.supabase.com',
    'DB_PORT' => '5432',
    'DB_DATABASE' => 'postgres',
    'DB_USERNAME' => 'postgres.otwraoiqocjjckmvylgf',
    'DB_PASSWORD' => 'Athafatthi11'
];
foreach ($dbVars as $key => $val) {
    putenv("$key=$val");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

try {
    // Direct /storage/ handler for high-performance and reliable serverless delivery
    $reqUri = $_SERVER['REQUEST_URI'] ?? '';
    $pathInfo = $_SERVER['PATH_INFO'] ?? '';
    $storageTarget = null;
    
    if (preg_match('#^/storage/(.+)$#', parse_url($reqUri, PHP_URL_PATH) ?? '', $matches)) {
        $storageTarget = urldecode($matches[1]);
    } elseif (preg_match('#^/storage/(.+)$#', $pathInfo, $matches)) {
        $storageTarget = urldecode($matches[1]);
    } elseif (!empty($_GET['storage_path'])) {
        $storageTarget = $_GET['storage_path'];
    }

    if ($storageTarget) {
        $path = ltrim($storageTarget, '/');
        
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $extMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
        ];

        // 1. Check /tmp
        $tmpFile = '/tmp/storage/app/public/' . $path;
        if (file_exists($tmpFile) && is_file($tmpFile)) {
            $mime = $extMimes[$ext] ?? (mime_content_type($tmpFile) ?: 'application/octet-stream');
            header("Content-Type: $mime");
            header("Content-Length: " . filesize($tmpFile));
            header("Cache-Control: public, max-age=31536000");
            readfile($tmpFile);
            exit;
        }

        // 2. Check local disk
        $localFile = dirname(__DIR__) . '/storage/app/public/' . $path;
        if (file_exists($localFile) && is_file($localFile)) {
            $mime = $extMimes[$ext] ?? (mime_content_type($localFile) ?: 'application/octet-stream');
            header("Content-Type: $mime");
            header("Content-Length: " . filesize($localFile));
            header("Cache-Control: public, max-age=31536000");
            readfile($localFile);
            exit;
        }

        // 3. Query Supabase database
        try {
            $pdo = new PDO("pgsql:host=aws-0-ap-southeast-1.pooler.supabase.com;port=5432;dbname=postgres", "postgres.otwraoiqocjjckmvylgf", "Athafatthi11");
            $stmt = $pdo->prepare("SELECT mime_type, original_name, content FROM uploaded_files WHERE path = ? OR path LIKE ? LIMIT 1");
            $stmt->execute([$path, "%$path"]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($file && !empty($file['content'])) {
                $binary = base64_decode($file['content']);
                try {
                    $dir = dirname($tmpFile);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777, true);
                    }
                    @file_put_contents($tmpFile, $binary);
                } catch (\Throwable $e) {}

                $mime = $extMimes[$ext] ?? ($file['mime_type'] ?: 'application/octet-stream');
                $filename = $file['original_name'] ?: basename($path);
                header("Content-Type: $mime");
                header("Content-Length: " . strlen($binary));
                header("Content-Disposition: inline; filename=\"$filename\"");
                header("Cache-Control: public, max-age=31536000");
                echo $binary;
                exit;
            }
        } catch (\Throwable $e) {}
    }
    if (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/db-test')) {
        echo "<h1>DB TEST</h1>";
        try {
            $pdo = new PDO("pgsql:host=aws-0-ap-southeast-1.pooler.supabase.com;port=5432;dbname=postgres", "postgres.otwraoiqocjjckmvylgf", "Athafatthi11");
            echo "✅ Connected to Supabase!<br>";
            
            echo "<h3>Tickets:</h3>";
            $stmt = $pdo->query("SELECT ticket_id, pelapor_id, lampiran, lampiran_support FROM tickets ORDER BY ticket_id DESC LIMIT 5");
            $tickets = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            echo "<pre>" . print_r($tickets, true) . "</pre>";

            echo "<h3>Uploaded Files:</h3>";
            $stmt2 = $pdo->query("SELECT id, disk, path, original_name, mime_type, size, created_at FROM uploaded_files ORDER BY id DESC LIMIT 10");
            $files = $stmt2 ? $stmt2->fetchAll(PDO::FETCH_ASSOC) : [];
            echo "<pre>" . print_r($files, true) . "</pre>";

            echo "<h3>Recap Query Test:</h3>";
            $stmt3 = $pdo->query("SELECT CAST(EXTRACT(MONTH FROM tanggal_input) AS INTEGER) as month, COUNT(*) as total FROM tickets WHERE EXTRACT(YEAR FROM tanggal_input) = 2026 GROUP BY CAST(EXTRACT(MONTH FROM tanggal_input) AS INTEGER)");
            $recap = $stmt3 ? $stmt3->fetchAll(PDO::FETCH_ASSOC) : [];
            echo "<pre>" . print_r($recap, true) . "</pre>";
        } catch (\PDOException $e) {
            echo "❌ PDO Error: " . $e->getMessage();
        }
        exit;
    }
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Fatal Error on Vercel:</h1>";
    echo "<pre>";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
