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
    if (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/db-test')) {
        echo "<h1>DB TEST</h1>";
        try {
            $pdo = new PDO("pgsql:host=aws-0-ap-southeast-1.pooler.supabase.com;port=5432;dbname=postgres", "postgres.otwraoiqocjjckmvylgf", "Athafatthi11");
            echo "✅ Connected to Supabase!<br>";
            $stmt = $pdo->query("SELECT email FROM users");
            if ($stmt) {
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "✅ Users table exists and has " . count($users) . " row(s).<br>";
                echo "<pre>" . print_r($users, true) . "</pre>";
            } else {
                echo "❌ Users table query failed.";
            }
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
