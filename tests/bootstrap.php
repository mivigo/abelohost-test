<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use Database\Migrations\M0001CreatePostCategoriesTable;
use Database\Migrations\M0002CreatePostsTable;
use Database\Migrations\M0003CreatePostsToCategoriesTable;

echo "Bootstrapping tests...\n";

// Ensure we connect to the test database specified in phpunit.xml env
$db = Database::getConnection();

echo "Resetting test database tables...\n";
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("DROP TABLE IF EXISTS `migrations`;");
$db->exec("DROP TABLE IF EXISTS `posts_to_categories`;");
$db->exec("DROP TABLE IF EXISTS `posts`;");
$db->exec("DROP TABLE IF EXISTS `post_categories`;");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "Creating migrations table...\n";
$db->exec("
    CREATE TABLE `migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL,
        `batch` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "Running migrations for test database...\n";
(new M0001CreatePostCategoriesTable())->up();
(new M0002CreatePostsTable())->up();
(new M0003CreatePostsToCategoriesTable())->up();

echo "Database migrations applied. Test environment ready!\n\n";
