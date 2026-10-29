<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesAdminUploads;
use App\Models\Article;
use App\Models\Category;
use App\Models\KeyPoint;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends AdminController
{
    use HandlesAdminUploads;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $cat = (int) $request->query('cat', 0);

        $articles = Article::with('category')
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%")
                        ->orWhere('author_name', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, ['published', 'draft'], true), fn ($w) => $w->where('status', $status))
            ->when($cat > 0, fn ($w) => $w->where('category_id', $cat))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.articles', [
            'articles' => $articles,
            'cats' => Category::orderBy('sort_order')->orderBy('id')->get(),
            'status' => $status,
            'q' => $q,
            'user' => $this->admin(),
        ]);
    }

    public function create()
    {
        return $this->form(null);
    }

    public function edit(int|string $id)
    {
        $art = Article::find((int) $id);
        if (! $art) {
            return redirect()->route('admin.articles')->with('err', 'लेख नहीं मिला।');
        }

        return $this->form($art);
    }

    /** @return View */
    private function form(?Article $art)
    {
        $existing = $art
            ? KeyPoint::where('article_id', $art->id)->orderBy('sort_order')->orderBy('id')->pluck('point')->all()
            : [];

        return view('admin.article-form', [
            'art' => $art,
            'cats' => Category::orderBy('sort_order')->orderBy('id')->get(),
            'existing_points' => $existing,
            'kp_text' => implode("\n", $existing),
            'v' => fn (string $col) => $art ? $art->{$col} : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->persist($request, new Article, true);
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        return $this->persist($request, Article::findOrFail((int) $id), false);
    }

    /**
     * Shared create/update path; validation mirrors the legacy form.
     */
    private function persist(Request $request, Article $art, bool $isNew): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required_if:language,hi', 'nullable', 'string', 'max:255'],
            'title_en' => ['required_if:language,en', 'nullable', 'string', 'max:255'],
            'language' => ['required', 'string', 'in:hi,en'],
            'slug' => ['nullable', 'string', 'max:220'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'excerpt_en' => ['nullable', 'string', 'max:2000'],
            'body' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'author_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            'author_name' => ['nullable', 'string', 'max:120'],
            'author_role' => ['nullable', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:120'],
            'badge_label' => ['nullable', 'string', 'max:80'],
            'badge_color' => ['nullable', 'string', 'max:30'],
            'stat_text' => ['nullable', 'string', 'max:120'],
            'home_slot' => ['nullable', 'string', 'in:hero,spot,market,pol_lead,pol_list,sport_lead,sport_list,ent,real'],
            'cat_slot' => ['nullable', 'string', 'in:lead,grid,dossier'],
            'time_label' => ['nullable', 'string', 'max:40'],
            'read_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_desc' => ['nullable', 'string', 'max:255'],
            'key_points' => ['nullable', 'string', 'max:5000'],
            'published_at' => ['nullable', 'date'],
            'comment_count' => ['nullable', 'integer', 'min:0'],
        ], [
            'image.max' => 'चित्र 10MB से बड़ी नहीं होनी चाही। · Image must not exceed 10MB.',
            'author_image.max' => 'लेखक चित्र 10MB से बड़ी नहीं होनी चाही। · Author photo must not exceed 10MB.',
        ], [
            'title' => 'शीर्षक · Title (Hindi)',
            'title_en' => 'शीर्षक · Title (English)',
            'language' => 'भाषा · Language',
            'category_id' => 'श्रेणी · Category',
            'body' => 'मुख्य सामग्री · Body',
            'image' => 'चित्र · Image',
            'author_image' => 'लेखक चित्र · Author photo',
        ]);

        try {
            $imgPath = $art->image;
            if ($request->hasFile('image')) {
                $new = $this->storeUpload($request->file('image'), 'news', 10 * 1024 * 1024);
                if ($new) {
                    $this->deleteUpload($imgPath);
                    $imgPath = $new;
                }
            }
            if ($request->boolean('remove_image') && $imgPath) {
                $this->deleteUpload($imgPath);
                $imgPath = null;
            }

            $authorImg = $art->author_image;
            if ($request->hasFile('author_image')) {
                $new = $this->storeUpload($request->file('author_image'), 'authors', 10 * 1024 * 1024);
                if ($new) {
                    $this->deleteUpload($authorImg);
                    $authorImg = $new;
                }
            }

            $slug = Site::slugify(
                trim((string) ($validated['slug'] ?? '')) !== ''
                    ? $validated['slug']
                    : (trim((string) ($validated['title'] ?? '')) !== '' ? $validated['title'] : $validated['title_en'])
            );
            $slug = $this->uniqueSlug($slug, $art->id);

            $bool = fn (string $k): int => $request->boolean($k) ? 1 : 0;

            $art->fill([
                'title' => $validated['title'] ?? '',
                'title_en' => $validated['title_en'] ?? null,
                'language' => $validated['language'],
                'slug' => $slug,
                'category_id' => (int) $validated['category_id'],
                'excerpt' => $validated['excerpt'] ?? null,
                'excerpt_en' => $validated['excerpt_en'] ?? null,
                'body' => $this->cleanBody($validated['body'] ?? ''),
                'body_en' => $this->cleanBody($validated['body_en'] ?? ''),
                'image' => $imgPath,
                'image_alt' => $validated['image_alt'] ?? null,
                'image_caption' => $validated['image_caption'] ?? null,
                'author_name' => $validated['author_name'] ?? null,
                'author_role' => $validated['author_role'] ?? null,
                'author_image' => $authorImg,
                'location' => $validated['location'] ?? null,
                'badge_label' => $validated['badge_label'] ?? null,
                'badge_color' => $validated['badge_color'] ?? 'bg-primary',
                'stat_text' => $validated['stat_text'] ?? null,
                'is_breaking' => $bool('is_breaking'),
                'is_featured' => $bool('is_featured'),
                'is_opinion' => $bool('is_opinion'),
                'is_visual' => $bool('is_visual'),
                'home_slot' => $validated['home_slot'] ?? null,
                'home_order' => (int) $request->input('home_order', 0),
                'cat_slot' => $validated['cat_slot'] ?? null,
                'cat_order' => (int) $request->input('cat_order', 0),
                'time_label' => $validated['time_label'] ?? null,
                'read_minutes' => (int) ($validated['read_minutes'] ?? 4),
                'comment_count' => (int) ($validated['comment_count'] ?? 0),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_desc' => $validated['meta_desc'] ?? null,
                'published_at' => $request->filled('published_at')
                    ? $request->date('published_at')
                    : ($art->published_at ?? ($request->input('status') === 'published' ? now() : null)),
                'status' => in_array($request->input('status'), ['published', 'draft'], true)
                    ? (string) $request->input('status')
                    : 'draft',
            ]);

            if ($isNew) {
                $art->id = null;
                $art->exists = false;
                $art->created_at = now();
            }
            if ($art->status === 'published' && ! $art->published_at) {
                $art->published_at = now();
            }
            $art->updated_at = now();
            $art->save();

            $this->syncKeyPoints($art->id, $validated['key_points'] ?? '');

            return redirect()
                ->route('admin.articles.edit', $art->id)
                ->with('ok', 'लेख सहेजा गया।');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('err', $e->getMessage());
        }
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $art = Article::findOrFail((int) $id);
        $this->deleteUpload($art->image);
        $this->deleteUpload($art->author_image);
        KeyPoint::where('article_id', $id)->delete();
        $art->delete();

        return redirect()->route('admin.articles')->with('ok', 'लेख हटा दिया गया।');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = array_map('intval', (array) $request->input('ids', []));
        $count = 0;
        foreach ($ids as $id) {
            $art = Article::find((int) $id);
            if ($art) {
                $this->deleteUpload($art->image);
                $this->deleteUpload($art->author_image);
                $art->delete();
                $count++;
            }
        }

        return redirect()->route('admin.articles')->with('ok', $count.' लेख हटाए गए।');
    }

    /** Replace key points with the submitted set. Supports plain newlines or HTML paragraphs/list-items. */
    private function syncKeyPoints(int $articleId, string $raw): void
    {
        // Normalize HTML block endings to newlines before splitting
        $normalized = preg_replace('/<\/(p|li|div|h[1-6])>/i', "\n", $raw);
        $normalized = preg_replace('/<br\s*\/?>/i', "\n", (string) $normalized);
        $lines = preg_split('/\r\n|\r|\n/', (string) $normalized) ?: [];
        $order = 0;
        $keep = [];

        foreach ($lines as $line) {
            $point = trim(strip_tags(trim($line), '<strong><b><em><i><u><a><mark>'));
            if ($point === '') {
                continue;
            }
            $keep[] = ['article_id' => $articleId, 'point' => $point, 'sort_order' => $order++];
        }

        KeyPoint::where('article_id', $articleId)->delete();
        foreach ($keep as $k) {
            KeyPoint::create($k);
        }
    }

    private function uniqueSlug(string $base, ?int $ignore): string
    {
        if ($base === '') {
            $base = 'article-'.bin2hex(random_bytes(4));
        }
        $slug = $base;
        $i = 2;
        while (true) {
            $row = Article::where('slug', $slug)->first();
            if (! $row || ($ignore && (int) $row->id === $ignore)) {
                return $slug;
            }
            $slug = $base.'-'.($i++);
        }
    }
}
