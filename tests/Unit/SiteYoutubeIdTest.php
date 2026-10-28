<?php

namespace Tests\Unit;

use App\Support\Site;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SiteYoutubeIdTest extends TestCase
{
    public static function youtubeCases(): array
    {
        return [
            'bare id' => ['dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'watch link' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'youtu.be with timestamp' => ['https://youtu.be/dQw4w9WgXcQ?t=42', 'dQw4w9WgXcQ'],
            'shorts' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'nocookie embed' => ['https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'live' => ['https://youtube.com/live/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'vimeo -> null' => ['https://vimeo.com/123456', null],
            'not a link -> null' => ['not-a-link', null],
            'empty -> null' => ['', null],
        ];
    }

    #[DataProvider('youtubeCases')]
    public function test_youtube_id(string $input, ?string $expected): void
    {
        $this->assertSame($expected, Site::youtubeId($input));
    }
}
