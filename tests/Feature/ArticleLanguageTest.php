<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleLanguageTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string,mixed> */
    private function adminSession(): array
    {
        return ['admin' => ['id' => 1, 'name' => 'Test', 'role' => 'admin']];
    }

    /**
     * @param  array<string,mixed>  $overrides
     */
    private function makeArticle(array $overrides = []): Article
    {
        $category = Category::firstOrCreate(
            ['slug' => 'national'],
            ['name_hi' => 'राष्ट्रीय']
        );

        return Article::create(array_merge([
            'category_id' => $category->id,
            'title' => 'हिंदी शीर्षक',
            'title_en' => 'English headline',
            'slug' => 'bilingual-story',
            'excerpt' => 'हिंदी सारांश',
            'excerpt_en' => 'English summary',
            'body' => '<p>हिंदी मुख्य सामग्री</p>',
            'body_en' => '<p>English main content</p>',
            'language' => 'hi',
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }

    public function test_default_visitor_sees_hindi_content(): void
    {
        $this->makeArticle();

        $response = $this->get('/article/bilingual-story');

        $response->assertStatus(200)
            ->assertSee('हिंदी शीर्षक')
            ->assertSee('हिंदी सारांश')
            ->assertSee('हिंदी मुख्य सामग्री')
            ->assertDontSee('English headline');
    }

    public function test_english_visitor_sees_english_content(): void
    {
        $this->makeArticle();

        $response = $this->withSession(['locale' => 'en'])
            ->get('/article/bilingual-story');

        $response->assertStatus(200)
            ->assertSee('English headline')
            ->assertSee('English summary')
            ->assertSee('English main content')
            ->assertDontSee('हिंदी शीर्षक');
    }

    public function test_english_visitor_falls_back_to_hindi_when_translation_missing(): void
    {
        $this->makeArticle([
            'title_en' => null,
            'excerpt_en' => null,
            'body_en' => null,
        ]);

        $response = $this->withSession(['locale' => 'en'])
            ->get('/article/bilingual-story');

        $response->assertStatus(200)
            ->assertSee('हिंदी शीर्षक')
            ->assertSee('हिंदी मुख्य सामग्री')
            ->assertDontSee('English headline');
    }

    public function test_homepage_lists_content_in_selected_language(): void
    {
        $this->makeArticle();

        $hindi = $this->get('/');
        $hindi->assertStatus(200)
            ->assertSee('हिंदी शीर्षक');

        $english = $this->withSession(['locale' => 'en'])->get('/');
        $english->assertStatus(200)
            ->assertSee('English headline');
    }

    public function test_language_switch_route_persists_choice_in_session(): void
    {
        $response = $this->from('/')->get('/language/en');

        $response->assertRedirect('/');
        $this->assertEquals('en', session('locale'));

        $response = $this->from('/')->get('/language/hi');
        $this->assertEquals('hi', session('locale'));

        $this->get('/language/fr')->assertNotFound();
    }

    public function test_admin_can_save_english_primary_article_without_hindi_fields(): void
    {
        $category = Category::create(['name_hi' => 'व्यापार', 'slug' => 'business']);

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.articles.store'), [
                'language' => 'en',
                'category_id' => $category->id,
                'title_en' => 'Market rally continues',
                'excerpt_en' => 'Stocks climbed for a fifth session',
                'body_en' => '<p>Sensex rose 245 points.</p>',
                'status' => 'published',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('articles', [
            'language' => 'en',
            'title_en' => 'Market rally continues',
            'title' => '',
            'status' => 'published',
        ]);

        $this->withSession(['locale' => 'en'])
            ->get('/article/market-rally-continues')
            ->assertStatus(200)
            ->assertSee('Market rally continues')
            ->assertSee('Stocks climbed for a fifth session');
    }

    public function test_english_article_requires_english_title(): void
    {
        $category = Category::create(['name_hi' => 'खेल', 'slug' => 'sports']);

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.articles.store'), [
                'language' => 'en',
                'category_id' => $category->id,
                'title' => 'हिंदी में ही लिखा गया',
                'body_en' => '<p>Story text.</p>',
                'status' => 'published',
            ]);

        $response->assertSessionHasErrors('title_en');
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_admin_edit_form_shows_both_language_fields_as_raw_values(): void
    {
        $art = $this->makeArticle();

        $response = $this->withSession($this->adminSession() + ['locale' => 'en'])
            ->get(route('admin.articles.edit', $art->id));

        $response->assertStatus(200)
            ->assertSee('Title (Hindi)')
            ->assertSee('Title (English)')
            ->assertSee('Body (English)')
            ->assertSee('हिंदी शीर्षक', false)
            ->assertSee('English headline', false);
    }

    public function test_search_matches_english_text_and_displays_active_language(): void
    {
        $this->makeArticle();
        $this->makeArticle(['slug' => 'market-move', 'title' => 'बाज़ार में तेज़ी', 'title_en' => 'Sensex jumps']);

        $this->get('/search?q=Sensex')
            ->assertStatus(200)
            ->assertSee('बाज़ार में तेज़ी')
            ->assertDontSee('Sensex jumps');

        $this->withSession(['locale' => 'en'])->get('/search?q=Sensex')
            ->assertStatus(200)
            ->assertSee('Sensex jumps');
    }
}
