<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'name' => 'Music Player',
                'key' => 'music_player',
                'description' => 'Pemutar musik latar dengan tombol play/pause untuk undangan.',
                'icon' => '🎵',
                'price' => 29000,
                'category' => Addon::CATEGORY_MEDIA,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Photo Gallery',
                'key' => 'photo_gallery',
                'description' => 'Galeri foto dengan lightbox untuk menampilkan momen pre-wedding.',
                'icon' => '📸',
                'price' => 39000,
                'category' => Addon::CATEGORY_MEDIA,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Google Maps',
                'key' => 'maps',
                'description' => 'Peta lokasi acara dengan tombol navigasi langsung ke Google Maps.',
                'icon' => '📍',
                'price' => 19000,
                'category' => Addon::CATEGORY_INTERACTIVE,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Countdown Timer',
                'key' => 'countdown',
                'description' => 'Hitung mundur menuju hari pernikahan secara real-time.',
                'icon' => '⏰',
                'price' => 19000,
                'category' => Addon::CATEGORY_INTERACTIVE,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'RSVP Form',
                'key' => 'rsvp_form',
                'description' => 'Form konfirmasi kehadiran tamu dengan dashboard untuk melihat respon.',
                'icon' => '✉️',
                'price' => 0,
                'category' => Addon::CATEGORY_INTERACTIVE,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Digital Gift',
                'key' => 'digital_gift',
                'description' => 'Halaman amplop digital dengan informasi rekening bank dan e-wallet.',
                'icon' => '🎁',
                'price' => 25000,
                'category' => Addon::CATEGORY_SOCIAL,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Love Story',
                'key' => 'love_story',
                'description' => 'Timeline kisah cinta pasangan dari pertemuan hingga lamaran.',
                'icon' => '💕',
                'price' => 29000,
                'category' => Addon::CATEGORY_SOCIAL,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Live Streaming',
                'key' => 'live_stream',
                'description' => 'Embed link live streaming acara untuk tamu yang tidak bisa hadir.',
                'icon' => '📺',
                'price' => 35000,
                'category' => Addon::CATEGORY_UTILITY,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::firstOrCreate(['key' => $addon['key']], $addon);
        }
    }
}
