<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Support\Html;
use Illuminate\Http\UploadedFile;
use RuntimeException;

trait HandlesAdminUploads
{
    /**
     * Persist an uploaded file under uploads/<sub>/<Y>/<m> with a random name.
     *
     * @return string|null storage-relative path, or null when nothing was sent
     */
    protected function storeUpload(?UploadedFile $file, string $sub, int $maxBytes = 5 * 1024 * 1024): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        $map = [
            'jpg' => ['image/jpeg', 'image/pjpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg', 'image/jpg'],
            'png' => ['image/png', 'image/x-png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp', 'image/x-webp'],
            'pdf' => ['application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf'],
        ];

        $ext = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        if (! in_array($ext, $allowed, true) || ! isset($map[$ext]) || ! in_array($mime, $map[$ext], true)) {
            throw new RuntimeException('असमर्थित फ़ाइल प्रकार। · Unsupported file type.');
        }

        if ($file->getSize() > $maxBytes) {
            throw new RuntimeException('फ़ाइल बहुत बड़ी है। · File too large.');
        }

        $rel = 'uploads/'.$sub.'/'.date('Y/m').'/'.date('d').'-'.bin2hex(random_bytes(6)).'.'.$ext;
        $abs = public_path($rel);

        if (! is_dir(dirname($abs))) {
            mkdir(dirname($abs), 0755, true);
        }

        $file->move(dirname($abs), basename($abs));

        return $rel;
    }

    /** Delete an uploaded/local file, staying inside uploads/ or assets/. */
    protected function deleteUpload(?string $path): void
    {
        $path = trim((string) $path);
        if ($path === '' || preg_match('#^https?://#i', $path)) {
            return;
        }

        $abs = realpath(public_path($path));
        $uploads = realpath(public_path('uploads'));
        $assets = realpath(public_path('assets'));

        if ($abs && $uploads && str_starts_with($abs, $uploads)) {
            @unlink($abs);

            return;
        }
        if ($abs && $assets && str_starts_with($abs, $assets)) {
            @unlink($abs);
        }
    }

    /**
     * Allowlist sanitizer for rich-text bodies.
     *
     * Delegates to App\Support\Html, which walks the DOM so that formatting
     * the editor supports (tables, strikethrough, code) survives the round
     * trip and unsafe attributes/URLs are dropped structurally.
     */
    protected function cleanBody(?string $html): string
    {
        return Html::clean($html);
    }
}
