<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Category extends Model
{
    protected static string $table = 'post_categories';

    /**
     * Get posts belonging to this category.
     */
    public function posts(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT p.* FROM posts p
                JOIN posts_to_categories ptc ON p.id = ptc.post_id
                WHERE ptc.category_id = :category_id AND p.deleted_at IS NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute(['category_id' => $this->id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = Post::hydrate($row);
        }
        return $posts;
    }

    /**
     * Get categories that contain at least one non-deleted post.
     */
    public static function getActiveCategories(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT DISTINCT c.* FROM post_categories c
                JOIN posts_to_categories ptc ON c.id = ptc.category_id
                JOIN posts p ON ptc.post_id = p.id
                WHERE c.deleted_at IS NULL AND p.deleted_at IS NULL
                ORDER BY c.name ASC";
        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = self::hydrate($row);
        }
        return $categories;
    }

    /**
     * Get the latest posts in this category.
     */
    public function getLatestPosts(int $limit = 3): array
    {
        $db = Database::getConnection();
        $sql = "SELECT p.* FROM posts p
                JOIN posts_to_categories ptc ON p.id = ptc.post_id
                WHERE ptc.category_id = :category_id AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT :limit";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = Post::hydrate($row);
        }
        return $posts;
    }
}
