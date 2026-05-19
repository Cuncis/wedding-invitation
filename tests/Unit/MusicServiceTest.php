<?php

namespace Tests\Unit;

use App\Services\MusicService;
use PHPUnit\Framework\TestCase;

class MusicServiceTest extends TestCase
{
    private MusicService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = new MusicService();
    }

    public function test_it_extracts_youtube_id_from_short_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', $this->svc->extractYoutubeId('https://youtu.be/dQw4w9WgXcQ'));
    }

    public function test_it_extracts_youtube_id_from_watch_url_with_timestamp(): void
    {
        $this->assertSame('dQw4w9WgXcQ', $this->svc->extractYoutubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=42s'));
    }

    public function test_it_extracts_youtube_id_from_embed_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', $this->svc->extractYoutubeId('https://www.youtube.com/embed/dQw4w9WgXcQ'));
    }

    public function test_it_extracts_youtube_id_from_shorts_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', $this->svc->extractYoutubeId('https://www.youtube.com/shorts/dQw4w9WgXcQ'));
    }

    public function test_it_extracts_youtube_id_from_music_subdomain(): void
    {
        $this->assertSame('dQw4w9WgXcQ', $this->svc->extractYoutubeId('https://music.youtube.com/watch?v=dQw4w9WgXcQ'));
    }

    public function test_it_returns_null_for_invalid_url(): void
    {
        $this->assertNull($this->svc->extractYoutubeId('not a real url'));
        $this->assertNull($this->svc->extractYoutubeId('https://example.com/watch?v=tooshort'));
    }

    public function test_normalize_accepts_bare_video_id_as_url(): void
    {
        $result = $this->svc->normalize([
            'provider' => 'youtube',
            'url'      => 'dQw4w9WgXcQ',
            'title'    => 'Perfect',
        ]);

        $this->assertNotNull($result);
        $this->assertSame('dQw4w9WgXcQ', $result['video_id']);
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $result['url']);
        $this->assertSame('Perfect', $result['title']);
        $this->assertTrue($result['autoplay']);
        $this->assertTrue($result['loop']);
    }

    public function test_normalize_returns_null_when_empty(): void
    {
        $this->assertNull($this->svc->normalize(null));
        $this->assertNull($this->svc->normalize([]));
        $this->assertNull($this->svc->normalize(['provider' => 'youtube', 'url' => '']));
    }

    public function test_normalize_falls_back_to_youtube_for_unknown_provider(): void
    {
        $result = $this->svc->normalize([
            'provider' => 'pirate-bay',
            'url'      => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        $this->assertSame('youtube', $result['provider']);
        $this->assertSame('dQw4w9WgXcQ', $result['video_id']);
    }

    public function test_normalize_clamps_start_at_to_non_negative(): void
    {
        $result = $this->svc->normalize([
            'provider' => 'youtube',
            'url'      => 'https://youtu.be/dQw4w9WgXcQ',
            'start_at' => -50,
        ]);

        $this->assertSame(0, $result['start_at']);
    }
}
