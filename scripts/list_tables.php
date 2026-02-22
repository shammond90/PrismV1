<?php
// Nuke all orphaned InnoDB entries then drop/recreate the database
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=prismv1', 'root', '');

    // Get ALL table names MariaDB might think exist (from information_schema + known names)
    $knownTables = [];

    // From information_schema
    $stmt = $pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = ?');
    $stmt->execute(['prismv1']);
    foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $r) {
        $knownTables[] = $r[0];
    }

    // Add all tables that our migrations would create (comprehensive list)
    $migrationTables = [
        'users', 'password_reset_tokens', 'sessions',
        'cache', 'cache_locks',
        'jobs', 'job_batches', 'failed_jobs',
        'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
        'contacts', 'phone_numbers', 'email_addresses', 'addresses',
        'companies', 'employments', 'venues', 'buildings', 'spaces',
        'shows', 'company_show', 'contact_show',
        'show_catalogues', 'seasons',
        'productions', 'company_production', 'contact_production',
        'migrations',
    ];

    $allTables = array_unique(array_merge($knownTables, $migrationTables));
    echo "Attempting to DROP " . count($allTables) . " potential tables...\n";

    // Disable FK checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($allTables as $t) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS `$t`");
        } catch (PDOException $e) {
            echo "  Warning dropping $t: " . $e->getMessage() . "\n";
        }
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "Done dropping.\n";

    // Now drop and recreate the database itself
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec('DROP DATABASE IF EXISTS prismv1');
    $pdo->exec('CREATE DATABASE prismv1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "Database recreated.\n";

    // Verify clean
    $pdo->exec('USE prismv1');
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?');
    $stmt->execute(['prismv1']);
    echo "Tables: " . $stmt->fetchColumn() . "\n";

    // Final sanity: try creating users
    $pdo->exec("CREATE TABLE users (id INT PRIMARY KEY)");
    echo "CREATE TABLE users: OK\n";
    $pdo->exec("DROP TABLE users");
    echo "Ready for migrations!\n";

} catch (PDOException $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}
