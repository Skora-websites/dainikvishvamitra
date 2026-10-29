<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleThumbnailTest extends TestCase
{
    use RefreshDatabase;

    private function makePublishedArticle(array $overrides = []): Article
    {
        $category = Category::firstOrCreate(
            ['slug' => 'national'],
            ['name_hi' => 'राष्ट्रीय', 'slug' => 'national']
        );

        return Article::create(array_merge([
            'category_id' => $category->id,
            'title' => 'हिंदी शीर्षक',
            'slug' => 'thumbnail-story',
            'language' => 'hi',
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }

    /**
     * Regression guard: the homepage hero, spotlights and sidebars used to render
     * src="{{ asset($hero->image) }}", which for an article with no image produced
     * a bare site-root URL ("src=\"http://host/\""). Templates must now go through
     * the image_url accessor so NULL images fall back to the shared placeholder.
     */
    public function test_homepage_image_src_is_never_the_site_root(): void
    {
        $this->makePublishedArticle(['home_slot' => 'hero']);
        $heroImg = 'uploads/news/2026/10/27-abc.jpg';
        // Provide an explicit excerpt so the view's fallback `excerpt()` helper
        // (an unrelated, pre-existing latent call) is never triggered in tests.
        $this->makePublishedArticle([
            'slug' => 'with-image',
            'home_slot' => 'spot',
            'image' => $heroImg,
            'excerpt' => 'मान्य खबर सारांश।',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        // The article carrying a real image must surface its URL intact.
        $response->assertSee('uploads/news/2026/10/27-abc.jpg');
        // No <img> may point at the bare site root (the asset(null) bug):
        // src="http://host/" with no path segment after the slash.
        $body = $response->getContent();
        $this->assertStringNotContainsString('src="http://localhost:8000/"', $body);
        $this->assertStringNotContainsString('src="https://localhost:8000/"', $body);
    }

    public function test_article_image_url_falls_back_to_placeholder_when_image_is_null(): void
    {
        $art = $this->makePublishedArticle();

        // NULL image must resolve to the shared placeholder, not the bare
        // site root (the old asset(null) produced a trailing-slash URL).
        $this->assertStringContainsString(
            'assets/img/hero-cec.jpg',
            $art->image_url
        );
        $this->assertNotSame('http://localhost:8000/', $art->image_url);
    }

    public function test_article_image_url_uses_relative_path_when_set(): void
    {
        $art = $this->makePublishedArticle([
            'image' => 'uploads/news/2026/10/27-demo.jpg',
        ]);

        $this->assertSame(
            'http://localhost:8000/uploads/news/2026/10/27-demo.jpg',
            $art->image_url
        );
    }

    /**
     * The article page must expose its thumbnail to social sharers: og:image
     * and twitter:image carry the article's own image URL, and og:image:alt
     * describes it (defaults to the meta title).
     */
    public function test_article_page_exposes_og_and_twitter_image_when_image_is_set(): void
    {
        $this->makePublishedArticle([
            'image' => 'uploads/news/2026/10/27-abc.jpg',
            'meta_title' => 'चित्र के साथ लेख',
        ]);

        $response = $this->get('/article/thumbnail-story');

        $response->assertStatus(200);
        $html = $response->getContent();
        $this->assertStringContainsString('property="og:image" content="http://localhost:8000/uploads/news/2026/10/27-abc.jpg"', $html);
        $this->assertStringContainsString('name="twitter:image" content="http://localhost:8000/uploads/news/2026/10/27-abc.jpg"', $html);
        $this->assertStringContainsString('property="og:image:alt" content="चित्र के साथ लेख"', $html);
    }

    /**
     * Without its own image the article gets NO share image: the shared
     * placeholder depicts a different story and must never stand in for it.
     */
    public function test_article_page_has_no_og_image_when_image_is_null(): void
    {
        $this->makePublishedArticle();

        $response = $this->get('/article/thumbnail-story');

        $response->assertStatus(200);
        // The larger card scaffolding is always present; the image tags are not.
        $this->assertStringContainsString('twitter:card', $response->getContent());
        $this->assertStringNotContainsString('og:image', $response->getContent());
        $response->assertDontSee('property="og:image"', false);
    }
}
