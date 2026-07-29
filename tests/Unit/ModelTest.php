<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Category;
use App\Models\Post;
use App\Core\Database;

class ModelTest extends TestCase
{
    /**
     * Clean test database structures before running each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $db = Database::getConnection();
        $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $db->exec("TRUNCATE TABLE `posts_to_categories`;");
        $db->exec("TRUNCATE TABLE `posts`;");
        $db->exec("TRUNCATE TABLE `post_categories`;");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }

    /**
     * Test Category save, database insertion, and retrieval.
     */
    public function testCategorySaveAndFind(): void
    {
        $category = new Category([
            'name' => 'Test Tech',
            'description' => 'Test Tech Description'
        ]);

        $this->assertTrue($category->save());
        $this->assertNotNull($category->id);

        $found = Category::find($category->id);
        $this->assertNotNull($found);
        $this->assertEquals('Test Tech', $found->name);
        $this->assertEquals('Test Tech Description', $found->description);
    }

    /**
     * Test Category soft deletes.
     */
    public function testCategorySoftDelete(): void
    {
        $category = new Category(['name' => 'Delete Me']);
        $category->save();

        $this->assertNotNull(Category::find($category->id));

        $category->delete();

        // Standard lookup should skip soft-deleted entries
        $this->assertNull(Category::find($category->id));
        $this->assertEquals(0, Category::query()->count());
    }

    /**
     * Test active record relationships and similar articles selection.
     */
    public function testPostRelationsAndSimilar(): void
    {
        // Create test categories
        $cat1 = new Category(['name' => 'Cat 1']);
        $cat1->save();
        $cat2 = new Category(['name' => 'Cat 2']);
        $cat2->save();

        // Create test posts
        $post1 = new Post([
            'name' => 'Post 1',
            'text' => 'Full text 1',
            'description' => 'Desc 1',
            'views' => 100
        ]);
        $post1->save();
        $post1->syncCategories([$cat1->id, $cat2->id]);

        $post2 = new Post([
            'name' => 'Post 2',
            'text' => 'Full text 2',
            'description' => 'Desc 2',
            'views' => 200
        ]);
        $post2->save();
        $post2->syncCategories([$cat1->id]);

        // Verify many-to-many relationship mappings
        $categoriesOfPost1 = $post1->categories();
        $this->assertCount(2, $categoriesOfPost1);

        $postsOfCat1 = $cat1->posts();
        $this->assertCount(2, $postsOfCat1);
        $postsOfCat2 = $cat2->posts();
        $this->assertCount(1, $postsOfCat2);

        // Verify similar posts logic (Post 2 shares Cat 1 with Post 1)
        $similarOfPost1 = $post1->getSimilarPosts();
        $this->assertCount(1, $similarOfPost1);
        $this->assertEquals('Post 2', $similarOfPost1[0]->name);
    }
}
