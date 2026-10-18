<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $productsData = [
            // SNEAKERS
            [
                'category_slug' => 'sneakers',
                'name' => 'The Aeris Minimalist Low-Top',
                'description' => 'A masterclass in restraint. Handcrafted from full-grain Italian calf leather with an ultra-lightweight margom rubber sole. Subtle debossed ÉLEVÉ monogram at the heel collar.',
                'base_price' => 6850.00,
                'primary_image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                    ['name' => 'Ivory Blanc', 'hex' => '#F7F5F0'],
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                ],
                'sizes' => ['6', '7', '8', '9', '10', '11'],
            ],
            [
                'category_slug' => 'sneakers',
                'name' => 'The Monolith Retro Court',
                'description' => 'Vintage athletic inspiration redefined with architectural precision. Premium perforated nappa leather paired with suede overlays and plush calfskin lining.',
                'base_price' => 7450.00,
                'primary_image' => 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => false,
                'is_bestseller' => true,
                'colors' => [
                    ['name' => 'Ivory Blanc', 'hex' => '#F7F5F0'],
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                ],
                'sizes' => ['7', '8', '9', '10', '11'],
            ],

            // HEELS
            [
                'category_slug' => 'heels',
                'name' => 'The Céleste Sculpted Stiletto',
                'description' => 'A dramatic 85mm architectural stiletto pump with a tapered razor-sharp toe and ergonomic memory foam insole. Engineered for poised balance and statuesque elegance.',
                'base_price' => 8950.00,
                'primary_image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1535043934128-cf0b28d52f95?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                    ['name' => 'Ivory Blanc', 'hex' => '#F7F5F0'],
                    ['name' => 'Espresso', 'hex' => '#3E2723'],
                ],
                'sizes' => ['5', '6', '7', '8', '9', '10'],
            ],
            [
                'category_slug' => 'heels',
                'name' => 'The Lune Slingback Kitten Heel',
                'description' => 'Effortless Parisian flair. A 45mm sculpted micro-heel, elasticated slingback strap, and soft lambskin construction deliver sophistication without strain.',
                'base_price' => 7800.00,
                'primary_image' => 'https://images.unsplash.com/photo-1515347619252-60a4bf4fff4f?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1515347619252-60a4bf4fff4f?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => false,
                'is_bestseller' => false,
                'colors' => [
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                ],
                'sizes' => ['6', '7', '8', '9', '10'],
            ],

            // FLATS
            [
                'category_slug' => 'flats',
                'name' => 'The Paloma Glove Ballet Flat',
                'description' => 'Inspired by classical ballet dance wear. Crafted from glove-soft lambskin with an elasticated gathered top line and flex-cushioned leather sole for cloud-like stride.',
                'base_price' => 5750.00,
                'primary_image' => 'https://images.unsplash.com/photo-1560343090-f0409e92791a?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1560343090-f0409e92791a?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1596704017254-9b121068fb31?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => true,
                'is_bestseller' => false,
                'colors' => [
                    ['name' => 'Ivory Blanc', 'hex' => '#F7F5F0'],
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                ],
                'sizes' => ['5', '6', '7', '8', '9', '10'],
            ],
            [
                'category_slug' => 'flats',
                'name' => 'The Aven Square-Toe Mary Jane',
                'description' => 'Modernized heritage. Distinctive squared chisel toe, slender instep strap with custom brushed brass buckle, and vegetable-tanned leather sole.',
                'base_price' => 6350.00,
                'primary_image' => 'https://images.unsplash.com/photo-1533867617858-e7b97e060509?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1533867617858-e7b97e060509?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => false,
                'is_bestseller' => true,
                'colors' => [
                    ['name' => 'Espresso', 'hex' => '#3E2723'],
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                ],
                'sizes' => ['6', '7', '8', '9', '10'],
            ],

            // LOAFERS
            [
                'category_slug' => 'loafers',
                'name' => 'The Regent Lug-Sole Loafer',
                'description' => 'Substantial yet featherlight. Premium semi-cordovan calfskin constructed with a Goodyear welt onto a custom lightweight tooth lug sole. Finished with hand-stitched beefroll moccasin detail.',
                'base_price' => 8650.00,
                'primary_image' => 'https://images.unsplash.com/photo-1533867617858-e7b97e060509?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1533867617858-e7b97e060509?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1582898956396-8575034c5fa3?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                    ['name' => 'Espresso', 'hex' => '#3E2723'],
                ],
                'sizes' => ['7', '8', '9', '10', '11', '12'],
            ],
            [
                'category_slug' => 'loafers',
                'name' => 'The Venetian Unlined Slip-On',
                'description' => 'Pure unadorned luxury. Butter-soft deerskin leather left unlined for supreme flexibility and instant tailored fit.',
                'base_price' => 7950.00,
                'primary_image' => 'https://images.unsplash.com/photo-1582898956396-8575034c5fa3?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1582898956396-8575034c5fa3?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => false,
                'is_bestseller' => false,
                'colors' => [
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                    ['name' => 'Espresso', 'hex' => '#3E2723'],
                ],
                'sizes' => ['7', '8', '9', '10', '11'],
            ],

            // BOOTS
            [
                'category_slug' => 'boots',
                'name' => 'The Solstice Chelsea Boot',
                'description' => 'A perennial silhouette elevated. Cut from a single wholecut piece of hand-waxed calf leather with concealed elastic side gussets and stacked leather Cuban heel.',
                'base_price' => 11800.00,
                'primary_image' => 'https://images.unsplash.com/photo-1520639888713-7851133b1ed0?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1520639888713-7851133b1ed0?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => true,
                'is_bestseller' => true,
                'colors' => [
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                    ['name' => 'Espresso', 'hex' => '#3E2723'],
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                ],
                'sizes' => ['7', '8', '9', '10', '11', '12'],
            ],

            // SANDALS
            [
                'category_slug' => 'sandals',
                'name' => 'The Meridian Padded Slide',
                'description' => 'Sensuous volumes and architectural comfort. Padded tubular leather straps over an anatomical cork and latex footbed lined in suede.',
                'base_price' => 4680.00,
                'primary_image' => 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?auto=format&fit=crop&w=1000&q=85',
                'images' => [
                    'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?auto=format&fit=crop&w=1000&q=85',
                    'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=1000&q=85',
                ],
                'status' => 'active',
                'is_featured' => true,
                'is_bestseller' => false,
                'colors' => [
                    ['name' => 'Sand Dune', 'hex' => '#D8D0C4'],
                    ['name' => 'Ivory Blanc', 'hex' => '#F7F5F0'],
                    ['name' => 'Obsidian Black', 'hex' => '#171717'],
                ],
                'sizes' => ['6', '7', '8', '9', '10', '11'],
            ],
        ];

        foreach ($productsData as $pData) {
            $cat = $categories->get($pData['category_slug']);
            if (!$cat) continue;

            $slug = Str::slug($pData['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $cat->id,
                    'name' => $pData['name'],
                    'description' => $pData['description'],
                    'base_price' => $pData['base_price'],
                    'primary_image' => $pData['primary_image'],
                    'images' => $pData['images'],
                    'status' => $pData['status'],
                    'is_featured' => $pData['is_featured'],
                    'is_bestseller' => $pData['is_bestseller'],
                ]
            );

            // Generate variants
            foreach ($pData['colors'] as $color) {
                foreach ($pData['sizes'] as $size) {
                    $colorPrefix = strtoupper(substr(str_replace(' ', '', $color['name']), 0, 3));
                    $sku = 'ELE-' . strtoupper(substr($cat->slug, 0, 3)) . '-' . $colorPrefix . '-SZ' . $size . '-' . substr($product->id, 0, 3);

                    // Varied realistic stock quantities (some lower to test low stock warnings)
                    $stock = match ($size) {
                        '6', '11' => rand(2, 6), // smaller/larger sizes have lower stock
                        '9' => rand(1, 4),      // popular size testing low stock
                        default => rand(8, 25),
                    };

                    ProductVariant::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size' => (string) $size,
                            'color' => $color['name'],
                        ],
                        [
                            'color_hex' => $color['hex'],
                            'sku' => $sku,
                            'stock_quantity' => $stock,
                            'price_adjustment' => 0.00,
                        ]
                    );
                }
            }
        }
    }
}
