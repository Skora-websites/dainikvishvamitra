<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVideoTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string,mixed> */
    private function adminSession(): array
    {
        return ['admin' => ['id' => 1, 'name' => 'Test', 'role' => 'admin']];
    }

    public function test_youtube_link_is_normalized(): void
    {
        $response = $this->withSession($this->adminSession())
            ->post(route('admin.videos.store'), [
                'title' => 'Test video',
                'youtube_id' => 'https://youtu.be/dQw4w9WgXcQ',
            ]);

        $response->assertRedirect(route('admin.videos'));
        $this->assertDatabaseHas('videos', [
            'title' => 'Test video',
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);
    }

    public function test_invalid_youtube_link_is_rejected(): void
    {
        $response = $this->withSession($this->adminSession())
            ->post(route('admin.videos.store'), [
                'title' => 'Bad link',
                'youtube_id' => 'https://vimeo.com/123456',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('videos', ['title' => 'Bad link']);
    }
}
