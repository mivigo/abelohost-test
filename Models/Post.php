<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Post extends Model
{
    protected static string $table = 'posts';

    /**
     * Get categories belonging to this post.
     */
    public function categories(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT c.* FROM post_categories c
                JOIN posts_to_categories ptc ON c.id = ptc.category_id
                WHERE ptc.post_id = :post_id AND c.deleted_at IS NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute(['post_id' => $this->id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = Category::hydrate($row);
        }
        return $categories;
    }

    /**
     * Link this post to one or more categories.
     */
    public function syncCategories(array $categoryIds): void
    {
        $db = Database::getConnection();
        
        // Remove existing links
        $stmt = $db->prepare("DELETE FROM posts_to_categories WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $this->id]);

        // Add new links
        if (!empty($categoryIds)) {
            $insertStmt = $db->prepare("INSERT INTO posts_to_categories (post_id, category_id) VALUES (:post_id, :category_id)");
            foreach ($categoryIds as $catId) {
                $insertStmt->execute([
                    'post_id' => $this->id,
                    'category_id' => $catId
                ]);
            }
        }
    }
}
