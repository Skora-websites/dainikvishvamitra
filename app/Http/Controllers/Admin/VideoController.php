<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesAdminUploads;
use App\Models\Video;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoController extends AdminController
{
    use HandlesAdminUploads;

    public function index()
    {
        return view('admin.videos', [
            'rows' => Video::orderBy('sort_order')->orderByDesc('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);

        try {
            $thumbnail = $this->storeUpload($request->file('thumbnail'), 'news', 10 * 1024 * 1024) ?? $data['thumbnail'] ?? null;
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('err', $e->getMessage());
        }

        $ytId = Site::youtubeId((string) $data['youtube_id']);
        if ($ytId === null) {
            return back()->withInput()->with('err', 'मान्य YouTube लिंक या आईडी नहीं। · Not a valid YouTube link or ID.');
        }

        Video::create([
            'title' => $data['title'],
            'youtube_id' => $ytId,
            'thumbnail' => $thumbnail,
            'duration_text' => $data['duration_text'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
            'published_at' => $request->filled('published_at') ? $request->date('published_at') : now(),
        ]);

        return redirect()->route('admin.videos')->with('ok', 'वीडियो जोड़ा गया। · Video added.');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $row = Video::findOrFail((int) $id);
        $data = $this->validate($request);

        try {
            $newThumb = $this->storeUpload($request->file('thumbnail'), 'news', 10 * 1024 * 1024);
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('err', $e->getMessage());
        }

        if ($newThumb) {
            $this->deleteUpload($row->thumbnail);
            $row->thumbnail = $newThumb;
        } elseif (! empty($data['thumbnail'])) {
            $row->thumbnail = $data['thumbnail'];
        }

        $ytId = Site::youtubeId((string) $data['youtube_id']);
        if ($ytId === null) {
            return back()->withInput()->with('err', 'मान्य YouTube लिंक या आईडी नहीं। · Not a valid YouTube link or ID.');
        }

        $row->fill([
            'title' => $data['title'],
            'youtube_id' => $ytId,
            'duration_text' => $data['duration_text'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->filled('published_at')) {
            $row->published_at = $request->date('published_at');
        }
        $row->save();

        return redirect()->route('admin.videos')->with('ok', 'वीडियो अपडेट हुआ। · Video updated.');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $row = Video::findOrFail((int) $id);
        $this->deleteUpload($row->thumbnail);
        $row->delete();

        return redirect()->route('admin.videos')->with('ok', 'वीडियो हटाया गया। · Video deleted.');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'youtube_id' => ['required', 'string', 'max:200'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            'duration_text' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'thumbnail.max' => 'थंबनेल 10MB से बड़ी नहीं होनी चाही। · Thumbnail must not exceed 10MB.',
        ], [
            'title' => 'शीर्षक · Title',
            'youtube_id' => 'YouTube लिंक/आईडी · YouTube link/ID',
            'thumbnail' => 'थंबनेल · Thumbnail',
            'duration_text' => 'अवधि · Duration',
            'description' => 'विवरण · Description',
        ]);
    }
}
