<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Enums\UserRole;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Enums\StockMovementType;
use App\Enums\StockAdjustmentType;
use App\Enums\StockAdjustmentReason;
use App\Enums\FinanceCategoryType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Baseline Users Exist
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Store Administrator',
                'email' => 'admin@admin.com',
                'role' => UserRole::ADMIN,
                'password' => Hash::make('password'),
            ]
        );
        $admin->update(['role' => UserRole::ADMIN]);

        $staff = User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Cashier Staff',
                'email' => 'staff@store.com',
                'role' => UserRole::STAFF,
                'password' => Hash::make('password'),
            ]
        );

        // 2. Ethiopian Units of Measurement (Safe resolver avoiding unique constraint collisions)
        $findOrCreateUnit = function ($symbol, $name) {
            return Unit::where('symbol', $symbol)
                ->orWhere('name', $name)
                ->first() ?? Unit::create(['symbol' => $symbol, 'name' => $name]);
        };

        $units = [
            'pcs' => $findOrCreateUnit('pcs', 'Pieces'),
            'btl' => $findOrCreateUnit('btl', 'Bottle'),
            'pk'  => $findOrCreateUnit('pk', 'Pack'),
            'box' => $findOrCreateUnit('box', 'Box / Carton'),
            'kg'  => $findOrCreateUnit('kg', 'Kilogram'),
            'qnt' => $findOrCreateUnit('qnt', 'Quintal (100kg)'),
            'L'   => $findOrCreateUnit('L', 'Liter'),
            'doz' => $findOrCreateUnit('doz', 'Dozen'),
        ];

        // 3. Ethiopian Shopping Center Categories
        $categories = [
            'grains' => Category::firstOrCreate(
                ['slug' => 'grains-teff-flour'],
                ['name' => 'Grains, Teff & Flour', 'description' => 'White and sergegna teff, wheat flour, and pulses (እህል እና ዱቄት)']
            ),
            'oil' => Category::firstOrCreate(
                ['slug' => 'cooking-oil-essentials'],
                ['name' => 'Edible Oil & Essentials', 'description' => 'Cooking oils, salt, sugar, and Ethiopian spices (ዘይት እና ቅመማ ቅመም)']
            ),
            'coffee' => Category::firstOrCreate(
                ['slug' => 'coffee-tea'],
                ['name' => 'Ethiopian Coffee & Tea', 'description' => 'Harar, Yirgacheffe, Sidama coffee beans, and highland tea (ቡና እና ሻይ)']
            ),
            'beverages' => Category::firstOrCreate(
                ['slug' => 'beverages-drinks'],
                ['name' => 'Beverages & Soft Drinks', 'description' => 'Ambo mineral water, soft drinks, juices, and malt (መጠጦች)']
            ),
            'dairy' => Category::firstOrCreate(
                ['slug' => 'dairy-bakery'],
                ['name' => 'Dairy & Bakery', 'description' => 'Fresh milk, Shola butter, bread, and biscuits (ወተት እና ዳቦ)']
            ),
            'personal_care' => Category::firstOrCreate(
                ['slug' => 'personal-care-hygiene'],
                ['name' => 'Personal Care & Hygiene', 'description' => 'Desta soap, laundry bars, shampoos, and dental care (የግል ንፅህና)']
            ),
            'household' => Category::firstOrCreate(
                ['slug' => 'cleaning-household'],
                ['name' => 'Cleaning & Household Goods', 'description' => 'Detergents, thermos flasks, and kitchen essentials (የቤት እቃዎች)']
            ),
        ];

        // 4. Default Walk-in Customer (for POS counter sales)
        $walkIn = Customer::firstOrCreate(
            ['phone' => '0000000000'],
            ['name' => 'Walk-in Retail Customer', 'email' => 'walkin@store.com', 'address' => 'Store Counter']
        );

        // 5. Authentic Ethiopian Shopping Center Products with Real Barcodes (ETB Prices)
        $productsData = [
            // Teff & Grains
            [
                'sku' => '6001234500018',
                'name' => 'Magna White Teff (50kg Bag)',
                'category' => $categories['grains'],
                'unit' => $units['qnt'],
                'cost' => 5000.00,
                'price' => 5800.00,
                'qty' => 40,
                'min' => 5,
                'created_at' => Carbon::now()->subDays(60),
            ],
            // Cooking Oil
            [
                'sku' => '6001234500025',
                'name' => 'Chef Sun Sunflower Cooking Oil 5L',
                'category' => $categories['oil'],
                'unit' => $units['btl'],
                'cost' => 1050.00,
                'price' => 1250.00,
                'qty' => 50,
                'min' => 10,
                'created_at' => Carbon::now()->subDays(45),
            ],
            // Ethiopian Coffee
            [
                'sku' => '6001234500032',
                'name' => 'Harar Roasted Coffee Beans Grade-1 500g',
                'category' => $categories['coffee'],
                'unit' => $units['pk'],
                'cost' => 520.00,
                'price' => 650.00,
                'qty' => 60,
                'min' => 10,
                'created_at' => Carbon::now()->subDays(35),
            ],
            // Ambo Water
            [
                'sku' => '8992761001001',
                'name' => 'Ambo Mineral Water 500ml Glass Bottle',
                'category' => $categories['beverages'],
                'unit' => $units['btl'],
                'cost' => 25.00,
                'price' => 35.00,
                'qty' => 120,
                'min' => 24,
                'created_at' => Carbon::now()->subDays(50),
            ],
            // Habesha Beer
            [
                'sku' => '6001234500056',
                'name' => 'Habesha Cold Gold Beer 330ml',
                'category' => $categories['beverages'],
                'unit' => $units['btl'],
                'cost' => 50.00,
                'price' => 65.00,
                'qty' => 96,
                'min' => 24,
                'created_at' => Carbon::now()->subDays(40),
            ],
            // Desta Laundry Soap
            [
                'sku' => '6001234500063',
                'name' => 'Desta Laundry Bar Soap 200g',
                'category' => $categories['personal_care'],
                'unit' => $units['pcs'],
                'cost' => 32.00,
                'price' => 45.00,
                'qty' => 150,
                'min' => 30,
                'created_at' => Carbon::now()->subDays(40),
            ],
            // Faffa Baby Food
            [
                'sku' => '6001234500070',
                'name' => 'Faffa Enriched Weaning Food 500g',
                'category' => $categories['dairy'],
                'unit' => $units['box'],
                'cost' => 190.00,
                'price' => 240.00,
                'qty' => 45,
                'min' => 10,
                'created_at' => Carbon::now()->subDays(30),
            ],
            // Wonji Sugar
            [
                'sku' => '6001234500087',
                'name' => 'Wonji Pure Refined Sugar 5kg Pack',
                'category' => $categories['oil'],
                'unit' => $units['pk'],
                'cost' => 440.00,
                'price' => 520.00,
                'qty' => 70,
                'min' => 15,
                'created_at' => Carbon::now()->subDays(25),
            ],
            // Coca-Cola
            [
                'sku' => '5449000000996',
                'name' => 'Coca-Cola Ethiopia 300ml RGB',
                'category' => $categories['beverages'],
                'unit' => $units['btl'],
                'cost' => 22.00,
                'price' => 30.00,
                'qty' => 100,
                'min' => 24,
                'created_at' => Carbon::now()->subDays(40),
            ],
            // Shola Butter
            [
                'sku' => '6001234500117',
                'name' => 'Shola Pure Table Butter 500g',
                'category' => $categories['dairy'],
                'unit' => $units['pk'],
                'cost' => 450.00,
                'price' => 550.00,
                'qty' => 30,
                'min' => 5,
                'created_at' => Carbon::now()->subDays(20),
            ],

            // Low Stock Items (Triggers Low Stock Alert on Dashboard & /products/low-in-stock)
            [
                'sku' => '4001234567891',
                'name' => 'Ethiopian Red Pepper Berbere 1kg (LOW STOCK)',
                'category' => $categories['oil'],
                'unit' => $units['kg'],
                'cost' => 300.00,
                'price' => 380.00,
                'qty' => 2,
                'min' => 10, // Stock (2) <= Min (10)
                'created_at' => Carbon::now()->subDays(50),
            ],
            [
                'sku' => '8712345678902',
                'name' => 'Abyssinia Pure Cooking Oil 1L (LOW STOCK)',
                'category' => $categories['oil'],
                'unit' => $units['btl'],
                'cost' => 220.00,
                'price' => 280.00,
                'qty' => 3,
                'min' => 12, // Stock (3) <= Min (12)
                'created_at' => Carbon::now()->subDays(40),
            ],

            // Slow-Moving / Dead Stock Items (Triggers Stagnant Alert on /reports/slow-moving)
            [
                'sku' => '9900112233445',
                'name' => 'Stainless Steel Coffee Flask Thermos 2L (SLOW-MOVING)',
                'category' => $categories['household'],
                'unit' => $units['pcs'],
                'cost' => 1750.00,
                'price' => 2200.00,
                'qty' => 12,
                'min' => 2,
                'created_at' => Carbon::now()->subDays(105), // Created >90 days ago, 0 sales
            ],
            [
                'sku' => '9900112233552',
                'name' => 'Ceramic Traditional Jebena Set 6 Cups (SLOW-MOVING)',
                'category' => $categories['household'],
                'unit' => $units['box'],
                'cost' => 850.00,
                'price' => 1300.00,
                'qty' => 18,
                'min' => 3,
                'created_at' => Carbon::now()->subDays(120), // Created >90 days ago, 0 sales
            ],
        ];

        $productModels = [];
        foreach ($productsData as $data) {
            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $data['category']->id,
                    'unit_id' => $data['unit']->id,
                    'name' => $data['name'],
                    'purchase_price' => $data['cost'],
                    'selling_price' => $data['price'],
                    'quantity' => $data['qty'],
                    'min_stock' => $data['min'],
                    'is_active' => true,
                    'description' => 'Ethiopian retail item for supermarket stock & POS barcode verification.',
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['created_at'],
                ]
            );

            // Record initial opening stock in Stock Movements if none exists
            if (!StockMovement::where('product_id', $product->id)->where('movement_type', StockMovementType::OPENING_STOCK)->exists()) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'movement_type' => StockMovementType::OPENING_STOCK,
                    'quantity' => $data['qty'],
                    'quantity_before' => 0,
                    'quantity_after' => $data['qty'],
                    'reference_type' => null,
                    'reference_id' => null,
                    'reference_number' => 'INIT-' . $product->sku,
                    'user_id' => $admin->id,
                    'notes' => 'Initial inventory opening balance.',
                    'created_at' => $data['created_at'],
                ]);
            }

            $productModels[$data['sku']] = $product;
        }

        // 6. Sample Stock Adjustments (for Audit Log & Stock Adjustment table)
        if (StockAdjustment::count() === 0 && isset($productModels['8992761001001'])) {
            $water = $productModels['8992761001001'];
            $adj1 = StockAdjustment::create([
                'adjustment_number' => 'ADJ.' . date('ymd') . '.0001',
                'product_id' => $water->id,
                'type' => StockAdjustmentType::REDUCTION,
                'reason' => StockAdjustmentReason::DAMAGE,
                'quantity' => 2,
                'cost_price_at_time' => $water->purchase_price,
                'user_id' => $admin->id,
                'notes' => 'Glass bottles cracked during aisle restocking.',
                'created_at' => Carbon::now()->subDays(3),
            ]);

            StockMovement::create([
                'product_id' => $water->id,
                'movement_type' => StockMovementType::ADJUSTMENT_OUT,
                'quantity' => -2,
                'quantity_before' => $water->quantity,
                'quantity_after' => $water->quantity - 2,
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adj1->id,
                'reference_number' => $adj1->adjustment_number,
                'user_id' => $admin->id,
                'notes' => 'Damaged bottles written off.',
                'created_at' => Carbon::now()->subDays(3),
            ]);
            $water->decrement('quantity', 2);
        }

        // 7. Seed Sales Transactions in Ethiopian Birr (ETB)
        if (Sale::count() < 5) {
            $salesSchedule = [
                // 12 days ago
                [
                    'days_ago' => 12,
                    'cashier' => $admin,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '8992761001001', 'qty' => 6], // Ambo water
                        ['sku' => '6001234500063', 'qty' => 4], // Desta soap
                    ],
                ],
                // 10 days ago
                [
                    'days_ago' => 10,
                    'cashier' => $staff,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '6001234500032', 'qty' => 1], // Harar coffee
                        ['sku' => '6001234500087', 'qty' => 1], // Sugar 5kg
                    ],
                ],
                // 7 days ago
                [
                    'days_ago' => 7,
                    'cashier' => $admin,
                    'method' => PaymentMethod::TRANSFER, // CBE / Telebirr transfer
                    'items' => [
                        ['sku' => '6001234500018', 'qty' => 1], // Magna Teff
                        ['sku' => '6001234500025', 'qty' => 1], // Cooking Oil 5L
                    ],
                ],
                // 5 days ago
                [
                    'days_ago' => 5,
                    'cashier' => $staff,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '6001234500056', 'qty' => 6], // Habesha beer
                        ['sku' => '5449000000996', 'qty' => 4], // Coca-Cola
                    ],
                ],
                // 3 days ago
                [
                    'days_ago' => 3,
                    'cashier' => $staff,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '6001234500070', 'qty' => 2], // Faffa baby food
                        ['sku' => '6001234500117', 'qty' => 1], // Shola butter
                    ],
                ],
                // Yesterday
                [
                    'days_ago' => 1,
                    'cashier' => $admin,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '6001234500025', 'qty' => 2], // Cooking oil
                        ['sku' => '6001234500087', 'qty' => 2], // Sugar
                        ['sku' => '8992761001001', 'qty' => 4], // Ambo water
                    ],
                ],
                // Today (Morning)
                [
                    'days_ago' => 0,
                    'cashier' => $staff,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '5449000000996', 'qty' => 6], // Coca-Cola
                        ['sku' => '8992761001001', 'qty' => 6], // Ambo water
                    ],
                ],
                // Today (Afternoon)
                [
                    'days_ago' => 0,
                    'cashier' => $admin,
                    'method' => PaymentMethod::TRANSFER, // Telebirr / CBE
                    'items' => [
                        ['sku' => '6001234500018', 'qty' => 2], // Teff 2 bags
                        ['sku' => '6001234500032', 'qty' => 2], // Harar coffee
                    ],
                ],
            ];

            $saleCounter = 1001;
            foreach ($salesSchedule as $plan) {
                $saleDate = Carbon::now()->subDays($plan['days_ago'])->setHour(rand(9, 18))->setMinute(rand(5, 55));
                $invNumber = 'INV-' . $saleDate->format('ymd') . '-' . sprintf('%04d', $saleCounter++);

                $subtotal = 0;
                $itemsToInsert = [];

                foreach ($plan['items'] as $itemData) {
                    $prod = $productModels[$itemData['sku']] ?? null;
                    if (!$prod) continue;

                    $qty = $itemData['qty'];
                    $costPrice = (float) $prod->purchase_price;
                    $unitPrice = (float) $prod->selling_price;
                    $lineTotal = round($qty * $unitPrice, 2);
                    $subtotal += $lineTotal;

                    $itemsToInsert[] = [
                        'product_id' => $prod->id,
                        'product_model' => $prod,
                        'quantity' => $qty,
                        'cost_price' => $costPrice,
                        'unit_price' => $unitPrice,
                        'discount' => 0,
                        'final_price' => $unitPrice,
                        'subtotal' => $lineTotal,
                    ];
                }

                $total = $subtotal;
                $cashReceived = ($plan['method'] === PaymentMethod::CASH) ? ceil($total / 10) * 10 : $total;
                $change = round($cashReceived - $total, 2);

                $sale = Sale::create([
                    'invoice_number' => $invNumber,
                    'customer_id' => $walkIn->id,
                    'created_by' => $plan['cashier']->id,
                    'sale_date' => $saleDate,
                    'status' => SaleStatus::COMPLETED,
                    'subtotal' => $subtotal,
                    'global_discount' => 0,
                    'total_discount' => 0,
                    'total' => $total,
                    'cash_received' => $cashReceived,
                    'change' => $change,
                    'payment_method' => $plan['method'],
                    'notes' => 'Walk-in retail POS transaction.',
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);

                foreach ($itemsToInsert as $ins) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $ins['product_id'],
                        'quantity' => $ins['quantity'],
                        'cost_price' => $ins['cost_price'],
                        'unit_price' => $ins['unit_price'],
                        'discount' => 0,
                        'final_price' => $ins['final_price'],
                        'subtotal' => $ins['subtotal'],
                        'created_at' => $saleDate,
                        'updated_at' => $saleDate,
                    ]);

                    $prodModel = $ins['product_model'];
                    $qtySold = $ins['quantity'];
                    $before = $prodModel->quantity;
                    $after = max(0, $before - $qtySold);

                    StockMovement::create([
                        'product_id' => $prodModel->id,
                        'movement_type' => StockMovementType::SALE,
                        'quantity' => -$qtySold,
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'reference_number' => $sale->invoice_number,
                        'user_id' => $plan['cashier']->id,
                        'notes' => 'Sold via retail POS terminal.',
                        'created_at' => $saleDate,
                    ]);

                    $prodModel->update(['quantity' => $after]);
                }
            }
        }

        // 8. Operational Expenses in Ethiopian Birr (ETB)
        $expenseCatRent = FinanceCategory::firstOrCreate(
            ['slug' => 'store-rent'],
            ['name' => 'Store Space Rent', 'type' => FinanceCategoryType::Expense, 'description' => 'Monthly shopping center retail space rental.']
        );
        $expenseCatGenerator = FinanceCategory::firstOrCreate(
            ['slug' => 'generator-fuel'],
            ['name' => 'Generator Fuel (Diesel)', 'type' => FinanceCategoryType::Expense, 'description' => 'Fuel for backup generator during power outages.']
        );
        $expenseCatUtilities = FinanceCategory::firstOrCreate(
            ['slug' => 'electricity-water'],
            ['name' => 'Electricity & Water Utilities', 'type' => FinanceCategoryType::Expense, 'description' => 'Ethiopian Electric Utility & water utility bills.']
        );
        $expenseCatLunch = FinanceCategory::firstOrCreate(
            ['slug' => 'cashier-lunch-allowance'],
            ['name' => 'Cashier Lunch & Allowance', 'type' => FinanceCategoryType::Expense, 'description' => 'Daily staff lunch and tea allowance.']
        );

        $expenses = [
            [
                'cat' => $expenseCatRent,
                'amount' => 35000.00,
                'desc' => 'Monthly retail space rent (Bole)',
                'date' => Carbon::now()->startOfMonth()->addDays(1),
            ],
            [
                'cat' => $expenseCatGenerator,
                'amount' => 6500.00,
                'desc' => 'Diesel fuel purchase for store backup generator',
                'date' => Carbon::now()->subDays(12),
            ],
            [
                'cat' => $expenseCatUtilities,
                'amount' => 2800.00,
                'desc' => 'Monthly electricity & municipal water bill',
                'date' => Carbon::now()->subDays(8),
            ],
            [
                'cat' => $expenseCatLunch,
                'amount' => 3500.00,
                'desc' => 'Weekly staff lunch and refreshment fund',
                'date' => Carbon::now()->subDays(4),
            ],
        ];

        foreach ($expenses as $exp) {
            FinanceTransaction::firstOrCreate(
                [
                    'finance_category_id' => $exp['cat']->id,
                    'amount' => $exp['amount'],
                    'transaction_date' => $exp['date']->toDateString(),
                ],
                [
                    'code' => 'EXP-' . $exp['date']->format('ymd') . '-' . Str::random(4),
                    'description' => $exp['desc'],
                    'created_by' => $admin->id,
                    'created_at' => $exp['date'],
                    'updated_at' => $exp['date'],
                ]
            );
        }
    }
}
