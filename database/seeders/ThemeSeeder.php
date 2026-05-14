<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Elegant Rose',
                'slug' => 'elegant-rose',
                'description' => 'Tema floral romantis dengan nuansa pink rose dan tipografi serif yang elegan.',
                'preview_image' => 'themes/elegant-rose.jpg',
                'base_price' => 99000,
                'default_colors' => [
                    'primary' => '#c8756a',
                    'secondary' => '#f5e6e0',
                    'accent' => '#8b4a42',
                    'text' => '#3d2820',
                ],
                'default_fonts' => [
                    'heading' => 'Playfair Display',
                    'body' => 'Lato',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Modern Minimalist',
                'slug' => 'modern-minimalist',
                'description' => 'Desain bersih dengan banyak white space dan tipografi modern sans-serif.',
                'preview_image' => 'themes/modern-minimalist.jpg',
                'base_price' => 119000,
                'default_colors' => [
                    'primary' => '#1a1a1a',
                    'secondary' => '#fafafa',
                    'accent' => '#d4af37',
                    'text' => '#2c2c2c',
                ],
                'default_fonts' => [
                    'heading' => 'Fraunces',
                    'body' => 'Inter',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Floral Garden',
                'slug' => 'floral-garden',
                'description' => 'Tema kebun bunga dengan ilustrasi botanical dan palet hijau alami.',
                'preview_image' => 'themes/floral-garden.jpg',
                'base_price' => 109000,
                'default_colors' => [
                    'primary' => '#5a7a4e',
                    'secondary' => '#f4f1ea',
                    'accent' => '#c9a96e',
                    'text' => '#2d3a2a',
                ],
                'default_fonts' => [
                    'heading' => 'Cormorant Garamond',
                    'body' => 'Nunito',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($themes as $theme) {
            Theme::firstOrCreate(['slug' => $theme['slug']], $theme);
        }
    }
}
