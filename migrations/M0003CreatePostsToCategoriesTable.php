<?php

namespace Database\Migrations;

use App\Core\Migration;
use App\Core\Database;

class M0003CreatePostsToCategoriesTable extends Migration
{
    /**
     * Run migration: Create posts_to_categories table.
     */
    public function up(): void
    {
        $db = Database::getConnection();
        $db->exec("
            CREATE TABLE `posts_to_categories` (
                `post_id` INT NOT NULL,
                `category_id` INT NOT NULL,
                PRIMARY KEY (`post_id`, `category_id`),
                CONSTRAINT `fk_ptc_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_ptc_category` FOREIGN KEY (`category_id`) REFERENCES `post_categories` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Rollback migration: Drop posts_to_categories table.
     */
    public function down(): void
    {
        $db = Database::getConnection();
        $db->exec("DROP TABLE IF EXISTS `posts_to_categories`");
    }
}
