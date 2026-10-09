<?php
mysqli_report(MYSQLI_REPORT_OFF);

$config = require __DIR__ . '/db-config.php';
$connect = @new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database'],
    $config['port']
);
if ($connect->connect_error) {
    error_log('Database connection failed: ' . $connect->connect_error);
    http_response_code(500);
    exit('Could not connect to the database. Check that MySQL is running and database.php is configured correctly.');
}

if (!$connect->set_charset('utf8mb4')) {
    error_log('Database charset setup failed: ' . $connect->error);
    http_response_code(500);
    exit('Could not configure the database connection.');
}

return $connect;
