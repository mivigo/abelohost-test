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
}
