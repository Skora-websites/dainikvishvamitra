<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesAdminUploads;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Image upload endpoint for the admin rich-text editor.
 *
 * Accepts a multipart `upload` field and returns { url: "..." } on success,
 * or { error: "..." } with a 4xx status on failure.
 */
class UploadController extends AdminController
{
    use HandlesAdminUploads;

    /**
     * List recently uploaded images from storage for the editor media manager.
     */
    public function index(Request $request): JsonResponse
    {
        $dir = public_path('uploads');
        $images = [];

        if (is_dir($dir)) {
            $files = File::allFiles($dir);
            // Sort newest first
            usort($files, static fn ($a, $b) => $b->getMTime() <=> $a->getMTime());

            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    continue;
                }
                $rel = str_replace(public_path().'/', '', $file->getPathname());
                $images[] = [
                    'url' => url($rel),
                    'rel_path' => $rel,
                    'filename' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'formatted_size' => round($file->getSize() / 1024, 1).' KB',
                    'time' => $file->getMTime(),
                    'formatted_time' => date('d M Y, h:i A', $file->getMTime()),
                ];
                if (count($images) >= 60) {
                    break;
                }
            }
        }

        return response()->json([
            'images' => $images,
        ]);
    }

    public function image(Request $request): JsonResponse
    {
        // VerifyCsrfToken already gates this route; the adapter sends the token
        // as the standard _token field alongside the file.

        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ], [
            'upload.max' => 'चित्र 10MB से बड़ी नहीं होनी चाही। · Image must not exceed 10MB.',
        ], ['upload' => 'चित्र · Image']);

        try {
            $url = $this->storeUpload($request->file('upload'), 'news', 10 * 1024 * 1024);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        if (! $url) {
            return response()->json(['error' => 'अपलोड विफल। · Upload failed.'], 422);
        }

        return response()->json([
            'url' => url($url),
        ]);
    }
}
