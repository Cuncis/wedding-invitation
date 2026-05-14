<?php

namespace Database\Seeders;

use App\Models\AnimationPack;
use Illuminate\Database\Seeder;

class AnimationPackSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            [
                'name' => 'Free',
                'key' => AnimationPack::KEY_FREE,
                'description' => 'Animasi dasar fade-in. Cocok untuk undangan sederhana.',
                'features' => ['fade_in', 'css_transitions'],
                'price' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Standard',
                'key' => AnimationPack::KEY_STANDARD,
                'description' => 'Scroll reveal dan slide-in animations menggunakan GSAP ScrollTrigger.',
                'features' => ['scroll_reveal', 'text_slide_in', 'fade_up', 'gsap_scrolltrigger'],
                'price' => 29000,
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'key' => AnimationPack::KEY_PREMIUM,
                'description' => 'Cinematic experience: envelope opening, particle effects, dan transisi halaman.',
                'features' => [
                    'envelope_open',
                    'particle_flowers',
                    'cinematic_transitions',
                    'lottie_animations',
                    'gsap_scrolltrigger',
                    'parallax',
                ],
                'price' => 59000,
                'is_active' => true,
            ],
        ];

        foreach ($packs as $pack) {
            AnimationPack::firstOrCreate(['key' => $pack['key']], $pack);
        }
    }
}
