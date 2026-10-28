<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesAdminUploads;
use App\Models\Page;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends AdminController
{
    use HandlesAdminUploads;

    public function index(Request $request)
    {
        return view('admin.pages', [
            'rows' => Page::orderBy('slug')->get(),
            'edit' => Page::where('slug', (string) $request->query('slug', ''))->first(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request, null);

        Page::create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['slug'], null),
            'body' => $this->cleanBody($data['body'] ?? ''),
            'status' => $request->input('status') === 'draft' ? 'draft' : 'published',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.pages')->with('ok', 'पेज बनाया गया।');
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $data = $this->validate($request, $page->id);

        $newSlug = $this->uniqueSlug($data['slug'], $page->id);
        if ($newSlug !== $page->slug) {
            $page->slug = $newSlug;
        }

        $page->fill([
            'title' => $data['title'],
            'body' => $this->cleanBody($data['body'] ?? ''),
            'status' => $request->input('status') === 'draft' ? 'draft' : 'published',
            'updated_at' => now(),
        ]);
        $page->save();

        return redirect()->route('admin.pages', ['slug' => $newSlug])->with('ok', 'पेज अपडेट हुआ।');
    }

    public function destroy(string $slug): RedirectResponse
    {
        Page::where('slug', $slug)->delete();

        return redirect()->route('admin.pages')->with('ok', 'पेज हटाया गया।');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request, ?int $ignoreId): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:140'],
            'body' => ['nullable', 'string'],
        ], [], ['title' => 'शीर्षक', 'slug' => 'स्लग', 'body' => 'सामग्री']);
    }

    private function uniqueSlug(string $base, ?int $ignore): string
    {
        $slug = Site::slugify($base);
        if ($slug === '') {
            $slug = 'page-'.bin2hex(random_bytes(3));
        }
        $i = 2;
        while (true) {
            $row = Page::where('slug', $slug)->first();
            if (! $row || ($ignore && (int) $row->id === $ignore)) {
                return $slug;
            }
            $slug = $base.'-'.($i++);
        }
    }
}
