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

    /**
     * Get 3 similar posts that share at least one category with this post.
     */
    public function getSimilarPosts(int $limit = 3): array
    {
        $db = Database::getConnection();
        
        $categories = $this->categories();
        if (empty($categories)) {
            return [];
        }

        $categoryIds = array_map(fn($cat) => (int)$cat->id, $categories);
        
        $placeholders = [];
        foreach ($categoryIds as $index => $catId) {
            $placeholders[] = ":cat_{$index}";
        }

        $inClause = implode(', ', $placeholders);
        $sql = "SELECT DISTINCT p.* FROM posts p
                JOIN posts_to_categories ptc ON p.id = ptc.post_id
                WHERE ptc.category_id IN ({$inClause})
                  AND p.id != :post_id
                  AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        foreach ($categoryIds as $index => $catId) {
            $stmt->bindValue(":cat_{$index}", $catId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = self::hydrate($row);
        }
        return $posts;
    }
}
