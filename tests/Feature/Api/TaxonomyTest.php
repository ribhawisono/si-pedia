<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Category;
use App\Models\Lecturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_index(): void
    {
        Category::factory()->count(3)->create();

        $this->getJson('/api/v1/categories')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_tags_index_and_articles_by_tag(): void
    {
        $article = Article::factory()->active()->create();
        $tag = $article->tags()->create(['name' => 'Laravel', 'slug' => 'laravel']);

        $this->getJson('/api/v1/tags')->assertOk()->assertJsonCount(1, 'data');

        $this->getJson("/api/v1/tags/{$tag->slug}/articles")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_lecturers_index(): void
    {
        Lecturer::factory()->count(2)->create(['status' => 'active']);

        $this->getJson('/api/v1/lecturers')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_search_requires_min_two_chars(): void
    {
        $this->getJson('/api/v1/search?q=a')->assertStatus(422);
    }

    public function test_search_returns_matching_article(): void
    {
        Article::factory()->active()->create(['title' => 'Belajar Laravel Dasar']);

        $this->getJson('/api/v1/search?q=Laravel')
            ->assertOk()
            ->assertJsonStructure(['query', 'articles', 'lecturers', 'categories', 'tags']);
    }

    public function test_analytics_popular_and_monthly(): void
    {
        Article::factory()->active()->count(3)->create();

        $this->getJson('/api/v1/analytics/popular')
            ->assertOk()
            ->assertJsonStructure(['most_viewed', 'category_stats', 'stats']);

        $this->getJson('/api/v1/analytics/monthly')
            ->assertOk()
            ->assertJsonStructure(['year', 'data']);
    }

    public function test_health_check(): void
    {
        $this->getJson('/api/v1')->assertOk()->assertJson(['status' => 'ok']);
    }
}
