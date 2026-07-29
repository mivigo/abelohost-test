<?php

namespace Database\Migrations;

use App\Core\Migration;
use App\Core\Database;

class M0002CreatePostsTable extends Migration
{
    /**
     * Run migration: Create posts table.
     */
    public function up(): void
    {
        $db = Database::getConnection();
        $db->exec("
            CREATE TABLE `posts` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `img_path` VARCHAR(255) NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `text` TEXT NOT NULL,
                `views` INT DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Rollback migration: Drop posts table.
     */
    public function down(): void
    {
        $db = Database::getConnection();
        $db->exec("DROP TABLE IF EXISTS `posts`");
    }
}
