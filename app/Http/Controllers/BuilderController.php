<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\AnimationPack;
use App\Models\Invitation;
use App\Models\Theme;
use App\Services\MusicService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class BuilderController extends Controller
{
    public function __construct(private readonly MusicService $musicService) {}

    /**
     * GET /builder/{invitation}/edit — show the Vue builder SPA.
     */
    public function edit(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->loadMissing('config');

        $themes         = Theme::active()->orderBy('sort_order')->get();
        $addons         = Addon::active()->orderBy('sort_order')->get();
        $animationPacks = AnimationPack::active()->get();

        return view('builder.edit', compact(
            'invitation',
            'themes',
            'addons',
            'animationPacks',
        ));
    }

    /**
     * PUT /builder/{invitation}/config — auto-save from Vue (debounced).
     */
    public function updateConfig(Request $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $allowed = [
            'theme_id',
            'animation_pack_id',
            'addon_ids',
            'sections',
            'colors',
            'typography',
            'content',
            'music',
            'maps',
        ];

        $data = $request->only($allowed);

        // Normalize per-addon settings server-side so the preview can rely
        // on clean data (e.g. YouTube video_id extracted from any URL shape).
        if (array_key_exists('music', $data)) {
            $data['music'] = $this->musicService->normalize($data['music']);
        }

        $invitation->config()->updateOrCreate(
            ['invitation_id' => $invitation->id],
            $data,
        );

        return response()->json(['status' => 'saved']);
    }

    /**
     * POST /builder/{invitation}/gallery/upload — upload and compress a gallery photo.
     */
    public function uploadGalleryImage(Request $request, Invitation $invitation): JsonResponse
    {
        try {
            $this->authorize('update', $invitation);

            $request->validate([
                'photo' => 'required|image|max:20480', // 20MB max
            ]);

            $file = $request->file('photo');
            $filename = Str::uuid() . '.jpg';
            $path = 'gallery/' . $invitation->id . '/' . $filename;

            // Compress image: max 1200px wide, 80% quality, output as webp
            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->decode($file->getPathname());
            $image->orient(); // fix EXIF rotation
            if ($image->width() > 1200) {
                $image->resize(1200, null);
            }
            $encoded = $image->encode(new \Intervention\Image\Drivers\Gd\Encoders\JpegEncoder(80));

            // Always use R2 for gallery uploads
            $diskName = 'r2';
            Storage::disk($diskName)->put($path, (string) $encoded, ['visibility' => 'public']);

            // Build URL from R2_PUBLIC_URL
            $base = rtrim(env('R2_PUBLIC_URL', ''), '/');
            $url = $base ? $base . '/' . $path : null;

            return response()->json(['url' => $url]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /builder/{invitation}/preview — iframe preview (no auth check for UX).
     */
    public function preview(Invitation $invitation): View
    {
        $this->authorize('update', $invitation);

        $invitation->loadMissing('config.theme', 'config.animationPack');

        $addonIds  = $invitation->config?->addon_ids ?? [];
        $addonKeys = $addonIds
            ? Addon::active()->whereIn('id', $addonIds)->pluck('key')->toArray()
            : [];

        $animationKey = $invitation->config?->animationPack?->key ?? AnimationPack::KEY_FREE;

        // Music settings are only meaningful when the music_player addon is enabled.
        $music = in_array('music_player', $addonKeys, true)
            ? ($invitation->config?->music ?? null)
            : null;

        // Mock wishes (ucapan & doa restu) — preview only. Real feature comes later.
        $wishes = collect([
            ['name' => 'Andi Pratama',   'message' => 'Selamat menempuh hidup baru! Semoga menjadi keluarga sakinah, mawaddah, warahmah.', 'attending' => 'hadir',       'created_at' => now()->subHours(2)],
            ['name' => 'Sari Wulandari', 'message' => 'Barakallahu lakuma wa baraka alaykuma. Selamat ya kalian berdua!',                       'attending' => 'hadir',       'created_at' => now()->subHours(5)],
            ['name' => 'Budi Santoso',   'message' => 'Maaf belum bisa hadir, doa terbaik selalu untuk kalian berdua.',                          'attending' => 'tidak_hadir', 'created_at' => now()->subDay()],
            ['name' => 'Dewi Lestari',   'message' => 'Selamat ya! Akhirnya hari yang ditunggu-tunggu tiba juga. \xF0\x9F\xA5\xB0',              'attending' => 'hadir',       'created_at' => now()->subDays(2)],
        ]);

        // Maps settings are only meaningful when the maps addon is enabled.
        $mapsAddon = Addon::where('key', 'maps')->first();
        $mapsConfig = null;
        if ($mapsAddon) {
            $addonIds = $invitation->config?->addon_ids ?? [];
            if (in_array($mapsAddon->id, $addonIds, true)) {
                $mapsConfig = $invitation->config?->maps ?? null;
            }
        }

        return view('builder.preview', compact('invitation', 'addonKeys', 'animationKey', 'wishes', 'music', 'mapsConfig'));
    }
}
