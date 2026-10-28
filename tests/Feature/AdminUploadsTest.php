<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Epaper;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminUploadsTest extends TestCase
{
    use RefreshDatabase;

    private static string $pngBytes;

    private static string $jpgBytes;

    private static string $pdfBytes;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$pngBytes = (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        self::$jpgBytes = (string) base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        self::$pdfBytes = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000010 00000 n\n0000000053 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n168\n%%EOF";
    }

    /** @return array<string,mixed> */
    private function adminSession(): array
    {
        return ['admin' => ['id' => 1, 'name' => 'Admin User', 'role' => 'admin']];
    }

    public function test_editor_image_upload_succeeds(): void
    {
        $file = UploadedFile::fake()->createWithContent('inline.png', self::$pngBytes, 'image/png');

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.uploads.image'), [
                'upload' => $file,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);

        $url = $response->json('url');
        $this->assertStringContainsString('uploads/news/', $url);

        // Cleanup
        $path = parse_url($url, PHP_URL_PATH);
        if ($path && file_exists(public_path(ltrim($path, '/')))) {
            @unlink(public_path(ltrim($path, '/')));
        }
    }

    public function test_editor_images_index_returns_list_of_stored_images(): void
    {
        $response = $this->withSession($this->adminSession())
            ->get(route('admin.uploads.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'images' => [
                '*' => ['url', 'filename', 'size', 'formatted_size', 'time', 'formatted_time'],
            ],
        ]);
    }

    public function test_inline_editor_image_roundtrip_and_public_viewing(): void
    {
        $cat = Category::create([
            'name_hi' => 'विशेष समाचार',
            'name_en' => 'Special News',
            'slug' => 'special-news',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // 1. Upload inline image via editor endpoint
        $file = UploadedFile::fake()->createWithContent('special-inline.png', self::$pngBytes, 'image/png');
        $uploadRes = $this->withSession($this->adminSession())
            ->post(route('admin.uploads.image'), ['upload' => $file]);

        $uploadRes->assertStatus(200);
        $imageUrl = $uploadRes->json('url');
        $this->assertNotEmpty($imageUrl);

        // 2. Insert into article body like the editor does
        $bodyWithFigure = '<p>मुख्य समाचार विवरण।</p><figure class="my-4"><img src="'.e($imageUrl).'" alt="संवाददाता चित्र" loading="lazy" class="rounded"/><figcaption>फोटो: विशेष संवाददाता</figcaption></figure><p>अंतिम पैरा।</p>';

        $saveRes = $this->withSession($this->adminSession())
            ->post(route('admin.articles.store'), [
                'title' => 'इनलाइन चित्र समाचार रिपोर्ट',
                'category_id' => $cat->id,
                'status' => 'published',
                'language' => 'hi',
                'body' => $bodyWithFigure,
            ]);

        $saveRes->assertRedirect();

        $article = Article::where('title', 'इनलाइन चित्र समाचार रिपोर्ट')->first();
        $this->assertNotNull($article);

        // 3. Verify database body retains the <img> and <figure>
        $this->assertStringContainsString($imageUrl, $article->body);
        $this->assertStringContainsString('<figure', $article->body);
        $this->assertStringContainsString('<figcaption>फोटो: विशेष संवाददाता</figcaption>', $article->body);

        // 4. Verify public article viewing displays the inline image
        $publicRes = $this->get(url('article/'.$article->slug));
        $publicRes->assertStatus(200);
        $publicRes->assertSee($imageUrl, false);
        $publicRes->assertSee('फोटो: विशेष संवाददाता');

        // Cleanup uploaded file
        $path = parse_url($imageUrl, PHP_URL_PATH);
        if ($path && file_exists(public_path(ltrim($path, '/')))) {
            @unlink(public_path(ltrim($path, '/')));
        }
    }

    public function test_article_featured_and_author_image_upload(): void
    {
        $cat = Category::create([
            'name_hi' => 'ताज़ा खबरें',
            'name_en' => 'Latest News',
            'slug' => 'latest-news',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $img = UploadedFile::fake()->createWithContent('featured.png', self::$pngBytes, 'image/png');
        $authorImg = UploadedFile::fake()->createWithContent('author.png', self::$pngBytes, 'image/png');

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.articles.store'), [
                'title' => 'परीक्षण समाचार शीर्षक',
                'category_id' => $cat->id,
                'author_name' => 'विशेष संवाददाता',
                'language' => 'hi',
                'status' => 'published',
                'body' => '<p>परीक्षण सामग्री</p>',
                'image' => $img,
                'author_image' => $authorImg,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'title' => 'परीक्षण समाचार शीर्षक',
            'category_id' => $cat->id,
        ]);

        $article = Article::where('title', 'परीक्षण समाचार शीर्षक')->first();
        $this->assertNotNull($article);
        $this->assertStringStartsWith('uploads/news/', (string) $article->image);
        $this->assertStringStartsWith('uploads/authors/', (string) $article->author_image);

        // Cleanup
        if ($article->image && file_exists(public_path($article->image))) {
            @unlink(public_path($article->image));
        }
        if ($article->author_image && file_exists(public_path($article->author_image))) {
            @unlink(public_path($article->author_image));
        }
    }

    public function test_video_thumbnail_upload(): void
    {
        $thumb = UploadedFile::fake()->createWithContent('video_thumb.png', self::$pngBytes, 'image/png');

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.videos.store'), [
                'title' => 'विशेष वीडियो रिपोर्ट',
                'youtube_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'thumbnail' => $thumb,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.videos'));

        $video = Video::where('title', 'विशेष वीडियो रिपोर्ट')->first();
        $this->assertNotNull($video);
        $this->assertStringStartsWith('uploads/news/', (string) $video->thumbnail);

        // Cleanup
        if ($video->thumbnail && file_exists(public_path($video->thumbnail))) {
            @unlink(public_path($video->thumbnail));
        }
    }

    public function test_epaper_pdf_and_cover_upload_and_viewing(): void
    {
        $pdf = UploadedFile::fake()->createWithContent('kolkata-edition.pdf', self::$pdfBytes, 'application/pdf');
        $cover = UploadedFile::fake()->createWithContent('kolkata-cover.png', self::$pngBytes, 'image/png');

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.papers.store'), [
                'title' => 'कोलकाता संस्करण - २८ अक्टूबर २०२६',
                'edition_date' => '2026-10-28',
                'pdf' => $pdf,
                'cover_image' => $cover,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.epapers'));

        $epaper = Epaper::where('title', 'कोलकाता संस्करण - २८ अक्टूबर २०२६')->first();
        $this->assertNotNull($epaper);
        $this->assertStringStartsWith('uploads/epaper/', (string) $epaper->pdf_path);
        $this->assertStringStartsWith('uploads/epaper/', (string) $epaper->cover_image);
        $this->assertTrue($epaper->is_active);

        // Test public epaper archive listing
        $listResponse = $this->get(route('epaper'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('कोलकाता संस्करण - २८ अक्टूबर २०२६');
        $listResponse->assertSee(route('epaper.show', $epaper->id));

        // Test interactive e-paper reader page
        $readerResponse = $this->get(route('epaper.show', $epaper->id));
        $readerResponse->assertStatus(200);
        $readerResponse->assertSee('कोलकाता संस्करण - २८ अक्टूबर २०२६');
        $readerResponse->assertSee('vendor/pdfjs/pdf.min.js');
        $readerResponse->assertSee('pdf-canvas');
        $readerResponse->assertSee('zoom-in');

        // Cleanup
        if ($epaper->pdf_path && file_exists(public_path($epaper->pdf_path))) {
            @unlink(public_path($epaper->pdf_path));
        }
        if ($epaper->cover_image && file_exists(public_path($epaper->cover_image))) {
            @unlink(public_path($epaper->cover_image));
        }
    }
}
