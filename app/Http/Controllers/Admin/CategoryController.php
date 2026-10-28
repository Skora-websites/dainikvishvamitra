<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    public function index()
    {
        return view('admin.categories', [
            'rows' => Category::orderBy('sort_order')->orderBy('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);

        Category::create([
            'name_hi' => $data['name_hi'],
            'slug' => $this->uniqueSlug($data['slug'], null),
            'description' => $data['description'] ?? null,
            'accent_color' => $data['accent_color'] ?? 'bg-primary',
            'pills' => $data['pills'] ?? null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'show_in_nav' => $request->boolean('show_in_nav'),
            'show_in_footer' => $request->boolean('show_in_footer'),
            'status' => $request->input('status') === 'hidden' ? 'hidden' : 'active',
        ]);

        return redirect()->route('admin.categories')->with('ok', 'श्रेणी जोड़ी गई।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $cat = Category::findOrFail((int) $id);
        $data = $this->validate($request);

        $cat->fill([
            'name_hi' => $data['name_hi'],
            'slug' => $this->uniqueSlug($data['slug'], $id),
            'description' => $data['description'] ?? null,
            'accent_color' => $data['accent_color'] ?? 'bg-primary',
            'pills' => $data['pills'] ?? null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'show_in_nav' => $request->boolean('show_in_nav'),
            'show_in_footer' => $request->boolean('show_in_footer'),
            'status' => $request->input('status') === 'hidden' ? 'hidden' : 'active',
        ]);
        $cat->save();

        return redirect()->route('admin.categories')->with('ok', 'श्रेणी अपडेट हुई।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $cat = Category::findOrFail((int) $id);

        // articles.fk_art_cat is RESTRICT: deleting a category still in use
        // must produce a readable message, not a raw SQLSTATE 23000 page.
        if (Article::where('category_id', $id)->exists()) {
            return redirect()->route('admin.categories')
                ->with('err', 'इस श्रेणी में समाचार हैं — पहले उन्हें किसी अन्य श्रेणी में ले जाएँ।');
        }

        $cat->delete();

        return redirect()->route('admin.categories')->with('ok', 'श्रेणी हटा दी गई।');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request): array
    {
        return $request->validate([
            'name_hi' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:255'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'pills' => ['nullable', 'string', 'max:1000'],
        ], [], ['name_hi' => 'नाम', 'slug' => 'स्लग']);
    }

    private function uniqueSlug(string $base, ?int $ignore): string
    {
        $slug = Site::slugify($base);
        if ($slug === '') {
            $slug = 'cat-'.bin2hex(random_bytes(3));
        }
        $baseSlug = $slug;
        $i = 2;
        while (true) {
            $row = Category::where('slug', $slug)->first();
            if (! $row || ($ignore && (int) $row->id === $ignore)) {
                return $slug;
            }
            $slug = $baseSlug.'-'.($i++);
        }
    }
}
