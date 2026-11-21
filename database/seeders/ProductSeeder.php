<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Safe Category resolver
        $getCat = function (string $name) {
            return Category::where('name', 'like', "%{$name}%")
                ->orWhere('slug', 'like', '%' . Str::slug($name) . '%')
                ->first() ?? Category::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'description' => $name]
                );
        };

        // Safe Unit resolver
        $getUnit = function (string $sym, string $defaultName = 'Pieces') {
            return Unit::where('symbol', $sym)
                ->orWhere('symbol', strtolower($sym))
                ->orWhere('name', 'like', "%{$defaultName}%")
                ->orWhere('name', 'like', "%{$sym}%")
                ->first() ?? Unit::firstOrCreate(
                    ['name' => $defaultName],
                    ['symbol' => $sym]
                );
        };

        $cats = [
            'beverages' => $getCat('Beverages & Soft Drinks'),
            'coffee' => $getCat('Ethiopian Coffee & Tea'),
            'grains' => $getCat('Grains, Teff & Flour'),
            'oil' => $getCat('Edible Oil & Essentials'),
            'dairy' => $getCat('Dairy & Bakery'),
            'snacks' => $getCat('Snacks & Confectionery'),
            'personal' => $getCat('Personal Care & Hygiene'),
            'cleaning' => $getCat('Cleaning & Household Goods'),
        ];

        $units = [
            'pcs' => $getUnit('pcs', 'Pieces'),
            'btl' => $getUnit('btl', 'Bottle'),
            'pk' => $getUnit('pk', 'Pack'),
            'kg' => $getUnit('kg', 'Kilogram'),
            'box' => $getUnit('box', 'Box'),
            'L' => $getUnit('ltr', 'Liter'),
        ];

        $products = [
            // -------------------------------------------------------------
            // 1. BEVERAGES & SOFT DRINKS (መጠጦች)
            // -------------------------------------------------------------
            [
                'name' => 'Ambo Mineral Sparkling Water 500ml',
                'sku' => '8992761001001',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 28.00,
                'selling_price' => 38.00,
                'quantity' => 120,
                'min_stock' => 24,
                'image' => 'products/ambo-mineral-water.jpg',
                'description' => 'Naturally carbonated sparkling mineral water bottled at the historical source in Ambo, Ethiopia.',
            ],
            [
                'name' => 'Yes Natural Spring Water 600ml',
                'sku' => '8992761001002',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 18.00,
                'selling_price' => 25.00,
                'quantity' => 140,
                'min_stock' => 30,
                'image' => 'products/yes-spring-water.jpg',
                'description' => 'Pure and refreshing natural spring bottled drinking water.',
            ],
            [
                'name' => 'Coca-Cola Classic 500ml PET',
                'sku' => '5449000000996',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 32.00,
                'selling_price' => 45.00,
                'quantity' => 96,
                'min_stock' => 24,
                'image' => 'products/coca-cola-bottle.jpg',
                'description' => 'The original Coca-Cola refreshing carbonated soft drink in 500ml plastic bottle.',
            ],
            [
                'name' => 'Fanta Orange Soda 500ml PET',
                'sku' => '5449000011527',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 32.00,
                'selling_price' => 45.00,
                'quantity' => 80,
                'min_stock' => 20,
                'image' => 'products/fanta-orange.jpg',
                'description' => 'Bright, bubbly and fruity orange soda drink.',
            ],
            [
                'name' => 'Sprite Lemon-Lime Soda 500ml PET',
                'sku' => '5449000014535',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 32.00,
                'selling_price' => 45.00,
                'quantity' => 75,
                'min_stock' => 18,
                'image' => 'products/sprite-lime.jpg',
                'description' => 'Crisp, refreshing clean lemon-lime caffeine-free carbonated soda.',
            ],
            [
                'name' => 'Rani Float Mango Fruit Juice 240ml',
                'sku' => '6281001230018',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 40.00,
                'selling_price' => 55.00,
                'quantity' => 60,
                'min_stock' => 12,
                'image' => 'products/rani-mango-juice.jpg',
                'description' => 'Delicious mango juice drink with real fruit pulp chunks.',
            ],
            [
                'name' => 'Ceres 100% Pure Fruit Juice Apple 1L',
                'sku' => '6001234500049',
                'category_id' => $cats['beverages']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 140.00,
                'selling_price' => 185.00,
                'quantity' => 45,
                'min_stock' => 10,
                'image' => 'products/ceres-apple-juice.jpg',
                'description' => '100% pure apple fruit juice blend with no added sugar or preservatives.',
            ],

            // -------------------------------------------------------------
            // 2. ETHIOPIAN COFFEE & TEA (ቡና እና ሻይ)
            // -------------------------------------------------------------
            [
                'name' => 'Tomoca Roasted Coffee Beans 500g',
                'sku' => '6001234500032',
                'category_id' => $cats['coffee']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 480.00,
                'selling_price' => 620.00,
                'quantity' => 50,
                'min_stock' => 10,
                'image' => 'products/tomoca-coffee-beans.jpg',
                'description' => 'Premium Italian-Ethiopian roast whole coffee beans from the legendary Tomoca Coffee Addis Ababa.',
            ],
            [
                'name' => 'Yirgacheffe Grade-1 Specialty Roast 250g',
                'sku' => '6001234500124',
                'category_id' => $cats['coffee']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 320.00,
                'selling_price' => 420.00,
                'quantity' => 40,
                'min_stock' => 8,
                'image' => 'products/yirgacheffe-coffee.jpg',
                'description' => 'Highland Arabica coffee with floral jasmine notes and vibrant citrus finish.',
            ],
            [
                'name' => 'Nescafé Gold Classic Instant Coffee 200g',
                'sku' => '9300675001234',
                'category_id' => $cats['coffee']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 650.00,
                'selling_price' => 820.00,
                'quantity' => 35,
                'min_stock' => 5,
                'image' => 'products/nescafe-gold-coffee.jpg',
                'description' => 'Rich and smooth soluble coffee granules crafted with mountain-grown Arabica beans.',
            ],
            [
                'name' => 'Wush Wush Highland Black Tea 100 Bags',
                'sku' => '6001234500131',
                'category_id' => $cats['coffee']->id,
                'unit_id' => $units['box']->id,
                'purchase_price' => 120.00,
                'selling_price' => 165.00,
                'quantity' => 65,
                'min_stock' => 15,
                'image' => 'products/wush-wush-black-tea.jpg',
                'description' => 'Hand-picked Ethiopian black tea cultivated in the lush highlands of Wush Wush, Kaffa.',
            ],
            [
                'name' => 'Al-Aroosa Pure Black Dust Tea 250g',
                'sku' => '6221001234567',
                'category_id' => $cats['coffee']->id,
                'unit_id' => $units['box']->id,
                'purchase_price' => 150.00,
                'selling_price' => 210.00,
                'quantity' => 55,
                'min_stock' => 12,
                'image' => 'products/al-aroosa-tea.jpg',
                'description' => 'Rich, strong black tea blend preferred for traditional breakfast tea preparation.',
            ],

            // -------------------------------------------------------------
            // 3. GRAINS, TEFF & FLOUR (እህል እና ዱቄት)
            // -------------------------------------------------------------
            [
                'name' => 'Magna Pure White Teff Grain 5kg (ማኛ ጤፍ)',
                'sku' => '6001234500018',
                'category_id' => $cats['grains']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 520.00,
                'selling_price' => 680.00,
                'quantity' => 30,
                'min_stock' => 5,
                'image' => 'products/white-teff-grain.jpg',
                'description' => 'Superfine white Magna teff grain direct from East Shewa harvest, ideal for traditional soft Injera.',
            ],
            [
                'name' => 'Sergegna Mixed Teff Flour 5kg (ሰርገኛ ጤፍ)',
                'sku' => '6001234500148',
                'category_id' => $cats['grains']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 480.00,
                'selling_price' => 620.00,
                'quantity' => 35,
                'min_stock' => 8,
                'image' => 'products/sergegna-teff-flour.jpg',
                'description' => 'Nutritious whole grain mixed red and white teff flour for wholesome authentic Injera.',
            ],
            [
                'name' => 'Habesha Long Grain Basmati Rice 2kg',
                'sku' => '6001234500155',
                'category_id' => $cats['grains']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 260.00,
                'selling_price' => 340.00,
                'quantity' => 50,
                'min_stock' => 10,
                'image' => 'products/basmati-rice.jpg',
                'description' => 'Extra long aromatic basmati grains, non-sticky and fluffy when steamed.',
            ],
            [
                'name' => 'Barilla Spaghetti Pasta No. 5 500g',
                'sku' => '8076800195057',
                'category_id' => $cats['grains']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 95.00,
                'selling_price' => 135.00,
                'quantity' => 70,
                'min_stock' => 15,
                'image' => 'products/barilla-spaghetti.jpg',
                'description' => 'Traditional Italian durum wheat semolina spaghetti, cooks to firm al dente texture in 9 minutes.',
            ],
            [
                'name' => 'Split Red Lentils (Yemisir Kik) 1kg',
                'sku' => '6001234500162',
                'category_id' => $cats['grains']->id,
                'unit_id' => $units['kg']->id,
                'purchase_price' => 140.00,
                'selling_price' => 190.00,
                'quantity' => 80,
                'min_stock' => 20,
                'image' => 'products/red-lentils-misir.jpg',
                'description' => 'Selected clean split red lentils, the staple base for rich and aromatic Ethiopian Misir Wot stew.',
            ],

            // -------------------------------------------------------------
            // 4. EDIBLE OIL & COOKING ESSENTIALS (ዘይት እና ቅመማ ቅመም)
            // -------------------------------------------------------------
            [
                'name' => 'Nour Sun Pure Sunflower Cooking Oil 1L',
                'sku' => '6001234500025',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 240.00,
                'selling_price' => 310.00,
                'quantity' => 60,
                'min_stock' => 15,
                'image' => 'products/sunflower-oil.jpg',
                'description' => '100% pure light sunflower vegetable oil with Vitamin E for everyday family cooking.',
            ],
            [
                'name' => 'Hayat Refined Vegetable Cooking Oil 3L',
                'sku' => '6001234500179',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 620.00,
                'selling_price' => 780.00,
                'quantity' => 45,
                'min_stock' => 10,
                'image' => 'products/hayat-cooking-oil.jpg',
                'description' => 'Cholesterol-free refined vegetable cooking oil in convenient easy-pour handle jug.',
            ],
            [
                'name' => 'Borges Extra Virgin Olive Oil 500ml',
                'sku' => '8410179010025',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 450.00,
                'selling_price' => 590.00,
                'quantity' => 25,
                'min_stock' => 5,
                'image' => 'products/borges-olive-oil.jpg',
                'description' => 'Cold-pressed authentic Mediterranean extra virgin olive oil in protective dark glass bottle.',
            ],
            [
                'name' => 'Afromix Refined Iodized Table Salt 1kg',
                'sku' => '6001234500186',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 25.00,
                'selling_price' => 35.00,
                'quantity' => 150,
                'min_stock' => 30,
                'image' => 'products/afromix-iodized-salt.jpg',
                'description' => 'Pure white iodized kitchen salt for everyday cooking and nutrition.',
            ],
            [
                'name' => 'Wonji Pure White Cane Sugar 2kg',
                'sku' => '6001234500087',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 190.00,
                'selling_price' => 245.00,
                'quantity' => 90,
                'min_stock' => 20,
                'image' => 'products/wonji-sugar.jpg',
                'description' => 'High quality crystal cane sugar from Wonji Shoa Sugar Factory.',
            ],
            [
                'name' => 'Ethiopian Red Pepper Berbere Blend 500g (በርበሬ)',
                'sku' => '4001234567891',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 160.00,
                'selling_price' => 220.00,
                'quantity' => 50,
                'min_stock' => 10,
                'image' => 'products/ethiopian-berbere.jpg',
                'description' => 'Authentic spiced red pepper blend with sun-dried chilies, garlic, korarima, and ginger.',
            ],
            [
                'name' => 'Shiro Mitten Seasoned Chickpea Flour 1kg (ሽሮ)',
                'sku' => '6001234500193',
                'category_id' => $cats['oil']->id,
                'unit_id' => $units['kg']->id,
                'purchase_price' => 180.00,
                'selling_price' => 240.00,
                'quantity' => 55,
                'min_stock' => 12,
                'image' => 'products/shiro-chickpea-flour.jpg',
                'description' => 'Pre-spiced roasted chickpea flour for preparing savory Ethiopian Tegabino Shiro Wot.',
            ],

            // -------------------------------------------------------------
            // 5. DAIRY & BAKERY (ወተት እና ዳቦ)
            // -------------------------------------------------------------
            [
                'name' => 'Shola Pasteurized Fresh Whole Milk 1L',
                'sku' => '6001234500209',
                'category_id' => $cats['dairy']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 75.00,
                'selling_price' => 95.00,
                'quantity' => 50,
                'min_stock' => 15,
                'image' => 'products/shola-fresh-milk.jpg',
                'description' => 'Fresh pasteurized whole cow milk delivered daily from Shola Dairy Addis Ababa.',
            ],
            [
                'name' => 'Anchor Full Cream Milk Powder 400g Tin',
                'sku' => '9414200115014',
                'category_id' => $cats['dairy']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 380.00,
                'selling_price' => 490.00,
                'quantity' => 40,
                'min_stock' => 10,
                'image' => 'products/anchor-milk-powder.jpg',
                'description' => 'Fortified instant full cream milk powder rich in Calcium, Protein and Vitamin D.',
            ],
            [
                'name' => 'Shola Pure Natural Table Butter 500g',
                'sku' => '6001234500117',
                'category_id' => $cats['dairy']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 380.00,
                'selling_price' => 480.00,
                'quantity' => 30,
                'min_stock' => 8,
                'image' => 'products/shola-pure-butter.jpg',
                'description' => 'Pure cream table butter from local pasteurized Ethiopian milk fat.',
            ],
            [
                'name' => 'Addis Bakery Whole Wheat Dabo Bread 400g',
                'sku' => '6001234500216',
                'category_id' => $cats['dairy']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 35.00,
                'selling_price' => 50.00,
                'quantity' => 45,
                'min_stock' => 10,
                'image' => 'products/addis-dabo-bread.jpg',
                'description' => 'Golden brown artisan whole grain loaf baked fresh with pure wheat flour.',
            ],

            // -------------------------------------------------------------
            // 6. SNACKS & CONFECTIONERY (ብስኩት እና ከረሜላ)
            // -------------------------------------------------------------
            [
                'name' => 'Oreo Chocolate Sandwich Cookies 133g',
                'sku' => '7622210825026',
                'category_id' => $cats['snacks']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 60.00,
                'selling_price' => 85.00,
                'quantity' => 90,
                'min_stock' => 20,
                'image' => 'products/oreo-cookies.jpg',
                'description' => 'Crisp cocoa sandwich biscuits with smooth vanilla creme filling.',
            ],
            [
                'name' => 'Pringles Sour Cream & Onion Crisps 165g',
                'sku' => '5053990101573',
                'category_id' => $cats['snacks']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 210.00,
                'selling_price' => 280.00,
                'quantity' => 45,
                'min_stock' => 10,
                'image' => 'products/pringles-potato-crisps.jpg',
                'description' => 'Iconic hyperbolic potato crisps seasoned with savory sour cream and sweet herbs.',
            ],
            [
                'name' => 'Cadbury Dairy Milk Chocolate Bar 90g',
                'sku' => '7622201452019',
                'category_id' => $cats['snacks']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 110.00,
                'selling_price' => 150.00,
                'quantity' => 60,
                'min_stock' => 15,
                'image' => 'products/cadbury-chocolate.jpg',
                'description' => 'Velvety smooth milk chocolate made with a glass and a half of fresh milk.',
            ],

            // -------------------------------------------------------------
            // 7. PERSONAL CARE & HYGIENE (የግል ንፅህና)
            // -------------------------------------------------------------
            [
                'name' => 'Lifebuoy Total Antibacterial Body Soap 100g',
                'sku' => '8901030381014',
                'category_id' => $cats['personal']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 45.00,
                'selling_price' => 65.00,
                'quantity' => 110,
                'min_stock' => 25,
                'image' => 'products/lifebuoy-soap.jpg',
                'description' => 'Advanced antibacterial formula protects against 99.9% of everyday germs.',
            ],
            [
                'name' => 'Dettol Original Antiseptic Bar Soap 120g',
                'sku' => '5000158066014',
                'category_id' => $cats['personal']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 55.00,
                'selling_price' => 80.00,
                'quantity' => 90,
                'min_stock' => 20,
                'image' => 'products/dettol-antiseptic-soap.jpg',
                'description' => 'Trusted pine fragrance germ protection with nourishing moisturizers.',
            ],
            [
                'name' => 'Colgate Total 12 Clean Mint Toothpaste 100ml',
                'sku' => '8718951134015',
                'category_id' => $cats['personal']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 130.00,
                'selling_price' => 175.00,
                'quantity' => 70,
                'min_stock' => 15,
                'image' => 'products/colgate-toothpaste.jpg',
                'description' => '12-hour antibacterial defense against plaque, tartar and gum irritation.',
            ],
            [
                'name' => 'Vaseline Pure Petroleum Jelly 250ml',
                'sku' => '8712561384018',
                'category_id' => $cats['personal']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 160.00,
                'selling_price' => 220.00,
                'quantity' => 50,
                'min_stock' => 10,
                'image' => 'products/vaseline-jelly.jpg',
                'description' => 'Triple-purified hypoallergenic skin protectant locks in natural moisture.',
            ],

            // -------------------------------------------------------------
            // 8. CLEANING & HOUSEHOLD GOODS (የቤት እቃዎች)
            // -------------------------------------------------------------
            [
                'name' => 'Ariel Complete Concentrated Laundry Powder 1kg',
                'sku' => '4015600854019',
                'category_id' => $cats['cleaning']->id,
                'unit_id' => $units['pk']->id,
                'purchase_price' => 210.00,
                'selling_price' => 280.00,
                'quantity' => 60,
                'min_stock' => 15,
                'image' => 'products/ariel-laundry-powder.jpg',
                'description' => 'High-efficiency enzymatic detergent removes tough stains in a single wash cycle.',
            ],
            [
                'name' => 'Sunlight Real Lemon Dishwashing Liquid 750ml',
                'sku' => '6001087359018',
                'category_id' => $cats['cleaning']->id,
                'unit_id' => $units['btl']->id,
                'purchase_price' => 120.00,
                'selling_price' => 165.00,
                'quantity' => 55,
                'min_stock' => 12,
                'image' => 'products/sunlight-dishwashing.jpg',
                'description' => 'Degreasing lemon formula cuts through stubborn baked-on oils effortlessly.',
            ],
            [
                'name' => 'Desta Laundry Bar Soap 200g',
                'sku' => '6001234500063',
                'category_id' => $cats['cleaning']->id,
                'unit_id' => $units['pcs']->id,
                'purchase_price' => 35.00,
                'selling_price' => 50.00,
                'quantity' => 120,
                'min_stock' => 25,
                'image' => 'products/desta-laundry-soap.jpg',
                'description' => 'Long-lasting multi-purpose laundry bar soap for washing clothes and linens.',
            ],
            [
                'name' => 'Softline 2-Ply Facial Tissues Box (200 pulls)',
                'sku' => '6001234500223',
                'category_id' => $cats['cleaning']->id,
                'unit_id' => $units['box']->id,
                'purchase_price' => 80.00,
                'selling_price' => 115.00,
                'quantity' => 75,
                'min_stock' => 15,
                'image' => 'products/softline-facial-tissues.jpg',
                'description' => 'Ultra soft 100% virgin pulp facial tissues in decorative dispensary box.',
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['sku' => $data['sku']],
                array_merge($data, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
