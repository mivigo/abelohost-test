<?php

namespace Database\Migrations;

use App\Core\Migration;
use App\Core\Database;

class M0001CreatePostCategoriesTable extends Migration
{
    /**
     * Run migration: Create post_categories table.
     */
    public function up(): void
    {
        $db = Database::getConnection();
        $db->exec("
            CREATE TABLE `post_categories` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Rollback migration: Drop post_categories table.
     */
    public function down(): void
    {
        $db = Database::getConnection();
        $db->exec("DROP TABLE IF EXISTS `post_categories`");
    }
}
