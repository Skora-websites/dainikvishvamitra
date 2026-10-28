<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesAdminUploads;
use App\Models\Epaper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EpaperController extends AdminController
{
    use HandlesAdminUploads;

    public function index()
    {
        return view('admin.epapers', [
            'rows' => Epaper::orderByDesc('edition_date')->orderByDesc('id')->get(),
            'user' => $this->admin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request, null);

        try {
            $pdf = $this->storeUpload($request->file('pdf'), 'epaper', 100 * 1024 * 1024);
            $cover = $this->storeUpload($request->file('cover_image'), 'epaper', 10 * 1024 * 1024);
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('err', $e->getMessage());
        }

        Epaper::create([
            'title' => $data['title'],
            'edition_date' => $data['edition_date'],
            'pdf_path' => $pdf,
            'cover_image' => $cover,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.epapers')->with('ok', 'ई-पेपर जोड़ा गया। · E-paper added.');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $row = Epaper::findOrFail((int) $id);
        $data = $this->validate($request, $id);

        try {
            $pdf = $this->storeUpload($request->file('pdf'), 'epaper', 100 * 1024 * 1024);
            if ($pdf) {
                $this->deleteUpload($row->pdf_path);
                $row->pdf_path = $pdf;
            }

            $cover = $this->storeUpload($request->file('cover_image'), 'epaper', 10 * 1024 * 1024);
            if ($cover) {
                $this->deleteUpload($row->cover_image);
                $row->cover_image = $cover;
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('err', $e->getMessage());
        }

        $row->fill([
            'title' => $data['title'],
            'edition_date' => $data['edition_date'],
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()->route('admin.epapers')->with('ok', 'ई-पेपर अपडेट हुआ। · E-paper updated.');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $row = Epaper::findOrFail((int) $id);
        $this->deleteUpload($row->pdf_path);
        $this->deleteUpload($row->cover_image);
        $row->delete();

        return redirect()->route('admin.epapers')->with('ok', 'ई-पेपर हटाया गया। · E-paper deleted.');
    }

    /** @return array<string,mixed> */
    private function validate(Request $request, ?int $id): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'edition_date' => ['required', 'date'],
            'pdf' => [$id ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:102400'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ], [
            'pdf.required' => 'ई-पेपर PDF आवश्यक है। · E-paper PDF is required.',
            'pdf.mimes' => 'केवल PDF फ़ाइल मान्य है। · Only PDF files are allowed.',
            'pdf.max' => 'PDF 100MB से बड़ी नहीं होनी चाही। · PDF must not exceed 100MB.',
            'cover_image.max' => 'मुखपृष्ठ चित्र 10MB से बड़ी नहीं होनी चाही। · Cover image must not exceed 10MB.',
        ], [
            'title' => 'शीर्षक · Title',
            'edition_date' => 'तिथि · Date',
            'pdf' => 'PDF फ़ाइल · PDF file',
            'cover_image' => 'मुखपृष्ठ चित्र · Cover image',
        ]);
    }
}
