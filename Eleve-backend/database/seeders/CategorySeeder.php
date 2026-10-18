<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sneakers',
                'slug' => 'sneakers',
                'description' => 'Architectural low-tops and athletic silhouettes rendered in supple Italian calfskin and breathable canvas.',
                'image_url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Heels',
                'slug' => 'heels',
                'description' => 'Sculptural stilettos, refined kitten heels, and architectural block heels balancing poise and comfort.',
                'image_url' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Flats',
                'slug' => 'flats',
                'description' => 'Sensory glove flats, pointed-toe slingbacks, and minimalist Mary Janes designed for effortless daily cadence.',
                'image_url' => 'https://images.unsplash.com/photo-1560343090-f0409e92791a?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Loafers',
                'slug' => 'loafers',
                'description' => 'Classic penny loafers, modern lug-sole moccasins, and sleek Venetian slip-ons tailored to perfection.',
                'image_url' => 'https://images.unsplash.com/photo-1614252369475-531eba835eb1?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Boots',
                'slug' => 'boots',
                'description' => 'Hand-burnished Chelsea boots, square-toe ankle boots, and equestrian riding boots built to endure.',
                'image_url' => 'https://images.unsplash.com/photo-1520639888713-7851133b1ed0?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Sandals',
                'slug' => 'sandals',
                'description' => 'Strappy minimalist slides, asymmetrical mules, and cushioned resort silhouettes in warm earthy tones.',
                'image_url' => 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
