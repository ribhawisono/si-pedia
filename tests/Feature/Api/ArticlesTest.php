<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlesTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(User $user): array
    {
        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'secret123',
        ])->json('token');

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_index_returns_only_active_articles(): void
    {
        Article::factory()->active()->count(2)->create();
        Article::factory()->draft()->create();

        $this->getJson('/api/v1/articles')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_show_returns_article_with_related(): void
    {
        $article = Article::factory()->active()->create();

        $this->getJson("/api/v1/articles/{$article->slug}")
            ->assertOk()
            ->assertJsonStructure(['data', 'related']);
    }

    public function test_show_returns_404_for_inactive_article(): void
    {
        $article = Article::factory()->draft()->create();

        $this->getJson("/api/v1/articles/{$article->slug}")->assertStatus(404);
    }

    public function test_guest_cannot_post_comment(): void
    {
        $article = Article::factory()->active()->create();

        $this->postJson("/api/v1/articles/{$article->slug}/comments", [
            'content' => 'Halo',
        ])->assertStatus(401);
    }

    public function test_authenticated_user_can_post_comment(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $article = Article::factory()->active()->create();

        $this->withHeaders($this->authHeader($user))
            ->postJson("/api/v1/articles/{$article->slug}/comments", ['content' => 'Bagus artikelnya'])
            ->assertStatus(201)
            ->assertJsonPath('data.content', 'Bagus artikelnya');

        $this->assertDatabaseHas('comments', ['article_id' => $article->id, 'status' => 'pending']);
    }

    public function test_bookmark_toggle_creates_then_removes(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $article = Article::factory()->active()->create();
        $headers = $this->authHeader($user);

        $this->withHeaders($headers)
            ->postJson("/api/v1/articles/{$article->slug}/bookmark")
            ->assertStatus(201)
            ->assertJson(['bookmarked' => true]);

        $this->withHeaders($headers)
            ->postJson("/api/v1/articles/{$article->slug}/bookmark")
            ->assertOk()
            ->assertJson(['bookmarked' => false]);
    }

    public function test_bookmarks_index_requires_auth(): void
    {
        $this->getJson('/api/v1/bookmarks')->assertStatus(401);
    }
}
