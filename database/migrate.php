<?php
/**
 * GateDesk Database Migration Runner
 * Executes database/schema.sql on the configured MySQL server.
 */
require_once __DIR__ . '/../config.php';

echo "=== GateDesk Database Migration ===\n";
echo "Host: {$db_host}\n";
echo "Database: {$db_name}\n";
echo "User: {$db_user}\n";

try {
    // Connect without selecting DB first so we can create it
    $pdo = new PDO("mysql:host={$db_host}", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true
    ]);

    $sqlFile = __DIR__ . '/schema.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Schema file not found at: {$sqlFile}");
    }

    $sql = file_get_contents($sqlFile);
    echo "Executing schema.sql...\n";
    $pdo->exec($sql);

    echo "SUCCESS: Database '{$db_name}' and all tables created successfully!\n";

    // Verify created tables
    $pdo->exec("USE `{$db_name}`");
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "\nTotal tables verified: " . count($tables) . "\n";
    foreach ($tables as $i => $tbl) {
        echo "  " . ($i + 1) . ". {$tbl}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
