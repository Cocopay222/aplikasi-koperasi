<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

putenv('VIEW_COMPILED_PATH=/tmp');
putenv('LOG_CHANNEL=stderr');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['LOG_CHANNEL'] = 'stderr';

require __DIR__ . '/../public/index.php';
