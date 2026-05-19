<?php

namespace App\Services;

class MusicService
{
    public const PROVIDER_YOUTUBE    = 'youtube';
    public const PROVIDER_SPOTIFY    = 'spotify';
    public const PROVIDER_SOUNDCLOUD = 'soundcloud';
    public const PROVIDER_UPLOAD     = 'upload';

    public const ALLOWED_PROVIDERS = [
        self::PROVIDER_YOUTUBE,
        self::PROVIDER_SPOTIFY,
        self::PROVIDER_SOUNDCLOUD,
        self::PROVIDER_UPLOAD,
    ];

    /**
     * Normalize a raw music settings array coming from the builder.
     * Extracts provider-specific identifiers (e.g. YouTube video_id) and
     * strips out anything we don't recognize. Returns null when the input
     * is empty / clearly invalid so callers can persist a clean nullable
     * value.
     *
     * @param  array<string, mixed>|null  $raw
     * @return array{
     *   provider:   string,
     *   url:        string|null,
     *   video_id:   string|null,
     *   title:      string|null,
     *   artist:     string|null,
     *   autoplay:   bool,
     *   loop:       bool,
     *   start_at:   int,
     * }|null
     */
    public function normalize(?array $raw): ?array
    {
        if (! $raw) {
            return null;
        }

        $provider = in_array($raw['provider'] ?? null, self::ALLOWED_PROVIDERS, true)
            ? $raw['provider']
            : self::PROVIDER_YOUTUBE;

        $url      = isset($raw['url']) && is_string($raw['url']) ? trim($raw['url']) : null;
        $videoId  = null;

        if ($url !== null && $url !== '') {
            $videoId = match ($provider) {
                self::PROVIDER_YOUTUBE => $this->extractYoutubeId($url),
                default                => null,
            };
        } else {
            $url = null;
        }

        // Couple may have pasted a bare ID instead of a URL.
        if ($provider === self::PROVIDER_YOUTUBE && $videoId === null && $url !== null) {
            if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
                $videoId = $url;
                $url     = "https://www.youtube.com/watch?v={$url}";
            }
        }

        // Empty across the board → treat as unset so we don't persist noise.
        if ($url === null && empty($raw['title']) && empty($raw['artist'])) {
            return null;
        }

        return [
            'provider' => $provider,
            'url'      => $url,
            'video_id' => $videoId,
            'title'    => isset($raw['title'])  ? trim((string) $raw['title'])  : null,
            'artist'   => isset($raw['artist']) ? trim((string) $raw['artist']) : null,
            'autoplay' => (bool) ($raw['autoplay'] ?? true),
            'loop'     => (bool) ($raw['loop']     ?? true),
            'start_at' => max(0, (int) ($raw['start_at'] ?? 0)),
        ];
    }

    /**
     * Extract the 11-character video id from any common YouTube URL shape:
     *   - https://youtu.be/VIDEO_ID
     *   - https://www.youtube.com/watch?v=VIDEO_ID
     *   - https://www.youtube.com/embed/VIDEO_ID
     *   - https://www.youtube.com/shorts/VIDEO_ID
     *   - https://music.youtube.com/watch?v=VIDEO_ID
     *   - https://m.youtube.com/watch?v=VIDEO_ID
     * Returns null if no valid id can be extracted.
     */
    public function extractYoutubeId(string $url): ?string
    {
        $patterns = [
            // youtu.be/ID
            '~youtu\.be/([A-Za-z0-9_-]{11})~i',
            // youtube.com/watch?v=ID  or  ...&v=ID
            '~[?&]v=([A-Za-z0-9_-]{11})~i',
            // youtube.com/embed/ID  or  /shorts/ID  or  /v/ID
            '~youtube\.com/(?:embed|shorts|v)/([A-Za-z0-9_-]{11})~i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }
}
