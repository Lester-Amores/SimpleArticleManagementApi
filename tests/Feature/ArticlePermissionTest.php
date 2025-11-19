<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticlePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;
    protected User $otherUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->author = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
        ]);
    }

    /**
     * Test author can update their own article
     */
    public function test_author_can_update_own_article(): void
    {
        $article = Article::create([
            'title' => 'Original Title',
            'slug' => Str::slug('Original Title'),
            'content' => 'Original content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->author)->putJson("/api/articles/{$article->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'content' => 'Updated content',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);
    }

    /**
     * Test other user cannot update article they didn't create
     */
    public function test_other_user_cannot_update_article(): void
    {
        $article = Article::create([
            'title' => 'Original Title',
            'slug' => Str::slug('Original Title'),
            'content' => 'Original content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->otherUser)->putJson("/api/articles/{$article->id}", [
            'title' => 'Hacked Title',
            'content' => 'Hacked content',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized. You can only update your own articles.',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Original Title',
            'content' => 'Original content',
        ]);
    }

    /**
     * Test author can delete their own article
     */
    public function test_author_can_delete_own_article(): void
    {
        $article = Article::create([
            'title' => 'Article to Delete',
            'slug' => Str::slug('Article to Delete'),
            'content' => 'Content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->author)->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Article deleted successfully',
            ]);

        $this->assertSoftDeleted('articles', [
            'id' => $article->id,
        ]);
    }

    /**
     * Test other user cannot delete article they didn't create
     */
    public function test_other_user_cannot_delete_article(): void
    {
        $article = Article::create([
            'title' => 'Article to Protect',
            'slug' => Str::slug('Article to Protect'),
            'content' => 'Content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->otherUser)->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized. You can only delete your own articles.',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test unauthenticated user cannot update article
     */
    public function test_unauthenticated_user_cannot_update_article(): void
    {
        $article = Article::create([
            'title' => 'Original Title',
            'slug' => Str::slug('Original Title'),
            'content' => 'Content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);

        $response = $this->putJson("/api/articles/{$article->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test unauthenticated user cannot delete article
     */
    public function test_unauthenticated_user_cannot_delete_article(): void
    {
        $article = Article::create([
            'title' => 'Article',
            'slug' => Str::slug('Article'),
            'content' => 'Content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);

        $response = $this->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(401);
    }

    /**
     * Test author can update article categories
     */
    public function test_author_can_update_article_categories(): void
    {
        $category2 = Category::create([
            'name' => 'Science',
            'slug' => 'science',
        ]);

        $article = Article::create([
            'title' => 'Test Article',
            'slug' => Str::slug('Test Article'),
            'content' => 'Content',
            'author_id' => $this->author->id,
            'status' => 'published',
        ]);
        $article->categories()->attach($this->category->id);

        $response = $this->actingAs($this->author)->putJson("/api/articles/{$article->id}", [
            'category_ids' => [$category2->id],
        ]);

        $response->assertStatus(200);
        
        $article->refresh();
        $this->assertCount(1, $article->categories);
        $this->assertTrue($article->categories->contains($category2));
        $this->assertFalse($article->categories->contains($this->category));
    }
}

