<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);
$config = require __DIR__ . '/db-config.php';
$connection = @new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    '',
    $config['port']
);

if ($connection->connect_error) {
    fwrite(STDERR, "Could not connect to MySQL: {$connection->connect_error}\n");
    exit(1);
}

$database = str_replace('`', '``', $config['database']);
if (!$connection->query("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
    fwrite(STDERR, "Could not create the database: {$connection->error}\n");
    exit(1);
}

if (!$connection->select_db($config['database'])) {
    fwrite(STDERR, "Could not select the database: {$connection->error}\n");
    exit(1);
}

$tableResult = $connection->query(
    "SELECT COUNT(*) AS table_count FROM information_schema.tables WHERE table_schema = '" .
    $connection->real_escape_string($config['database']) . "'"
);
if (!$tableResult) {
    fwrite(STDERR, "Could not inspect the database: {$connection->error}\n");
    exit(1);
}

$tableCount = (int) $tableResult->fetch_assoc()['table_count'];
if ($tableCount === 0) {
    $dump = file_get_contents(__DIR__ . '/drx.sql');
    if ($dump === false || !$connection->multi_query($dump)) {
        fwrite(STDERR, "Could not import drx.sql: {$connection->error}\n");
        exit(1);
    }

    do {
        if ($result = $connection->store_result()) {
            $result->free();
        }
        if ($connection->more_results() && !$connection->next_result()) {
            fwrite(STDERR, "Could not finish importing drx.sql: {$connection->error}\n");
            exit(1);
        }
    } while ($connection->more_results());

    echo "Imported drx.sql into database '{$config['database']}'.\n";
} else {
    $appointmentTable = $connection->query("SHOW TABLES LIKE 'appointment'");
    if (!$appointmentTable || $appointmentTable->num_rows === 0) {
        fwrite(STDERR, "Database '{$config['database']}' already contains tables but no appointment table; import was skipped to preserve existing data.\n");
        exit(1);
    }

    $statusColumn = $connection->query("SHOW COLUMNS FROM appointment LIKE 'statue'");
    if (!$statusColumn) {
        fwrite(STDERR, "Could not inspect appointment table: {$connection->error}\n");
        exit(1);
    }
    if ($statusColumn->num_rows === 0 && !$connection->query(
        'ALTER TABLE appointment ADD statue TINYINT(1) NOT NULL DEFAULT 0'
    )) {
        fwrite(STDERR, "Could not add the appointment status column: {$connection->error}\n");
        exit(1);
    }

    echo "Existing database preserved; checked appointment schema.\n";
}
