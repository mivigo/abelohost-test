<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;
use Database\Seeders\DatabaseSeeder;

Env::load(__DIR__ . '/.env');

try {
    $db = Database::getConnection();
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Cleaning existing database tables...\n";
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE `posts_to_categories`;");
$db->exec("TRUNCATE TABLE `posts`;");
$db->exec("TRUNCATE TABLE `post_categories`;");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "Seeding database...\n";
try {
    (new DatabaseSeeder())->run();
    echo "Database seeded successfully!\n";
} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
