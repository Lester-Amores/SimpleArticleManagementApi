<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and categories for testing
        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
        ]);
    }

    /**
     * Test authenticated user can create an article
     */
    public function test_authenticated_user_can_create_article(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
            'status' => 'published',
            'category_ids' => [$this->category->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'slug',
                'content',
                'status',
                'author_id',
                'author' => [
                    'id',
                    'name',
                ],
                'categories',
            ])
            ->assertJson([
                'title' => 'Test Article',
                'content' => 'This is a test article content.',
                'status' => 'published',
                'author_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'author_id' => $this->user->id,
            'status' => 'published',
        ]);
    }

    /**
     * Test article slug is auto-generated from title
     */
    public function test_article_slug_is_auto_generated(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'My Test Article Title',
            'content' => 'Content here',
        ]);

        $response->assertStatus(201);
        
        $article = Article::where('title', 'My Test Article Title')->first();
        $this->assertEquals('my-test-article-title', $article->slug);
    }

    /**
     * Test article creation assigns current user as author
     */
    public function test_article_creation_assigns_current_user_as_author(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'Test Article',
            'content' => 'Content',
        ]);

        $response->assertStatus(201);
        
        $article = Article::where('title', 'Test Article')->first();
        $this->assertEquals($this->user->id, $article->author_id);
    }

    /**
     * Test article creation with categories
     */
    public function test_article_creation_with_categories(): void
    {
        $category2 = Category::create([
            'name' => 'Science',
            'slug' => 'science',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'Test Article',
            'content' => 'Content',
            'category_ids' => [$this->category->id, $category2->id],
        ]);

        $response->assertStatus(201);
        
        $article = Article::where('title', 'Test Article')->first();
        $this->assertCount(2, $article->categories);
        $this->assertTrue($article->categories->contains($this->category));
        $this->assertTrue($article->categories->contains($category2));
    }

    /**
     * Test unauthenticated user cannot create article
     */
    public function test_unauthenticated_user_cannot_create_article(): void
    {
        $response = $this->postJson('/api/articles', [
            'title' => 'Test Article',
            'content' => 'Content',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test article creation fails with invalid data
     */
    public function test_article_creation_fails_with_invalid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            // Missing title and content
            'status' => 'invalid-status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'status']);
    }

    /**
     * Test article defaults to draft status if not specified
     */
    public function test_article_defaults_to_draft_status(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'title' => 'Draft Article',
            'content' => 'Content',
        ]);

        $response->assertStatus(201);
        
        $article = Article::where('title', 'Draft Article')->first();
        $this->assertEquals('draft', $article->status);
    }
}

