<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Supplier;
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
use Illuminate\Support\Facades\DB;
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

        // 2. Units
        $units = [
            'pcs' => Unit::firstOrCreate(['symbol' => 'pcs'], ['name' => 'Pieces']),
            'btl' => Unit::firstOrCreate(['symbol' => 'btl'], ['name' => 'Bottle']),
            'pk'  => Unit::firstOrCreate(['symbol' => 'pk'],  ['name' => 'Pack']),
            'box' => Unit::firstOrCreate(['symbol' => 'box'], ['name' => 'Box']),
            'kg'  => Unit::firstOrCreate(['symbol' => 'kg'],  ['name' => 'Kilogram']),
        ];

        // 3. Categories
        $categories = [
            'beverages' => Category::firstOrCreate(
                ['slug' => 'beverages-drinks'],
                ['name' => 'Beverages & Drinks', 'description' => 'Cold drinks, juices, energy drinks, tea, and coffee']
            ),
            'snacks' => Category::firstOrCreate(
                ['slug' => 'snacks-confectionery'],
                ['name' => 'Snacks & Confectionery', 'description' => 'Chips, chocolates, biscuits, and candy']
            ),
            'groceries' => Category::firstOrCreate(
                ['slug' => 'packaged-foods-groceries'],
                ['name' => 'Packaged Foods & Groceries', 'description' => 'Instant noodles, cooking oil, rice, sauces']
            ),
            'personal_care' => Category::firstOrCreate(
                ['slug' => 'personal-care-hygiene'],
                ['name' => 'Personal Care & Hygiene', 'description' => 'Soaps, shampoos, sanitizers, and dental care']
            ),
            'household' => Category::firstOrCreate(
                ['slug' => 'household-supplies'],
                ['name' => 'Household Supplies', 'description' => 'Cleaning liquids, kitchenware, and laundry items']
            ),
        ];

        // 4. Customers
        $walkIn = Customer::firstOrCreate(
            ['phone' => '0000000000'],
            ['name' => 'Walk-in Customer', 'email' => 'walkin@store.com', 'address' => 'Store Counter']
        );
        $customerAmina = Customer::firstOrCreate(
            ['phone' => '+251911223344'],
            ['name' => 'Amina Mohammed', 'email' => 'amina@gmail.com', 'address' => 'Bole Subcity, House 102']
        );
        $customerJohn = Customer::firstOrCreate(
            ['phone' => '+251922334455'],
            ['name' => 'John Doe', 'email' => 'john.doe@gmail.com', 'address' => 'CMC Village, Block 4']
        );

        // 5. Products with Standard Retail Barcodes
        $productsData = [
            // Standard Retail Products (High & Normal Stock)
            [
                'sku' => '8992761001001',
                'name' => 'Mineral Spring Water 600ml',
                'category' => $categories['beverages'],
                'unit' => $units['btl'],
                'cost' => 0.40,
                'price' => 1.00,
                'qty' => 95,
                'min' => 10,
                'created_at' => Carbon::now()->subDays(60),
            ],
            [
                'sku' => '012345678905',
                'name' => 'Crispy Potato Chips BBQ 150g',
                'category' => $categories['snacks'],
                'unit' => $units['pk'],
                'cost' => 1.20,
                'price' => 2.50,
                'qty' => 45,
                'min' => 5,
                'created_at' => Carbon::now()->subDays(45),
            ],
            [
                'sku' => '7622210449283',
                'name' => 'Cadbury Dairy Milk Chocolate 100g',
                'category' => $categories['snacks'],
                'unit' => $units['pcs'],
                'cost' => 1.00,
                'price' => 2.00,
                'qty' => 38,
                'min' => 8,
                'created_at' => Carbon::now()->subDays(30),
            ],
            [
                'sku' => '12345678',
                'name' => 'Red Bull Energy Drink 250ml',
                'category' => $categories['beverages'],
                'unit' => $units['pcs'],
                'cost' => 1.80,
                'price' => 3.50,
                'qty' => 30,
                'min' => 5,
                'created_at' => Carbon::now()->subDays(30),
            ],
            [
                'sku' => '5449000000996',
                'name' => 'Coca-Cola Original 330ml Can',
                'category' => $categories['beverages'],
                'unit' => $units['pcs'],
                'cost' => 0.50,
                'price' => 1.25,
                'qty' => 75,
                'min' => 15,
                'created_at' => Carbon::now()->subDays(40),
            ],
            [
                'sku' => '8801043001234',
                'name' => 'Samyang Spicy Ramen Noodles 140g',
                'category' => $categories['groceries'],
                'unit' => $units['pk'],
                'cost' => 0.80,
                'price' => 1.80,
                'qty' => 60,
                'min' => 10,
                'created_at' => Carbon::now()->subDays(20),
            ],
            [
                'sku' => '6901234567890',
                'name' => 'Dettol Antibacterial Liquid Handwash 250ml',
                'category' => $categories['personal_care'],
                'unit' => $units['btl'],
                'cost' => 1.40,
                'price' => 2.80,
                'qty' => 40,
                'min' => 6,
                'created_at' => Carbon::now()->subDays(25),
            ],
            [
                'sku' => '5012345678900',
                'name' => 'Multi-Surface Disinfectant Cleaner 1L',
                'category' => $categories['household'],
                'unit' => $units['btl'],
                'cost' => 2.20,
                'price' => 4.50,
                'qty' => 25,
                'min' => 5,
                'created_at' => Carbon::now()->subDays(35),
            ],
            [
                'sku' => '9300675001234',
                'name' => 'Nescafe Gold Classic Coffee 200g',
                'category' => $categories['beverages'],
                'unit' => $units['pcs'],
                'cost' => 4.50,
                'price' => 7.90,
                'qty' => 20,
                'min' => 4,
                'created_at' => Carbon::now()->subDays(15),
            ],

            // Low Stock Items (Triggers Low Stock Alert on Dashboard & /products/low-in-stock)
            [
                'sku' => '4001234567891',
                'name' => 'Twinings Pure Green Tea 25 Bags (LOW STOCK)',
                'category' => $categories['beverages'],
                'unit' => $units['box'],
                'cost' => 2.10,
                'price' => 3.90,
                'qty' => 2,
                'min' => 10, // Stock (2) <= Min (10)
                'created_at' => Carbon::now()->subDays(50),
            ],
            [
                'sku' => '8712345678902',
                'name' => 'Borges Extra Virgin Olive Oil 500ml (LOW STOCK)',
                'category' => $categories['groceries'],
                'unit' => $units['btl'],
                'cost' => 5.20,
                'price' => 9.50,
                'qty' => 3,
                'min' => 8, // Stock (3) <= Min (8)
                'created_at' => Carbon::now()->subDays(40),
            ],

            // Slow-Moving / Dead Stock Items (Triggers Stagnant Alert on /reports/slow-moving)
            [
                'sku' => '9900112233445',
                'name' => 'Stainless Steel Japanese Chef Knife Set (SLOW-MOVING)',
                'category' => $categories['household'],
                'unit' => $units['box'],
                'cost' => 28.00,
                'price' => 52.00,
                'qty' => 12,
                'min' => 2,
                'created_at' => Carbon::now()->subDays(105), // Created >90 days ago, 0 sales
            ],
            [
                'sku' => '9900112233552',
                'name' => 'Ceramic Chocolate Fondue Pot Deluxe (SLOW-MOVING)',
                'category' => $categories['household'],
                'unit' => $units['box'],
                'cost' => 15.00,
                'price' => 28.00,
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
                    'description' => 'Demo item for stock & POS barcode verification.',
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
                'notes' => 'Bottles dropped and broken during aisle restocking.',
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

        // 7. Seed Sales Transactions (over the last 14 days, yesterday, and today)
        if (Sale::count() < 5) {
            $salesSchedule = [
                // 12 days ago
                [
                    'days_ago' => 12,
                    'cashier' => $admin,
                    'customer' => $customerAmina,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '8992761001001', 'qty' => 3],
                        ['sku' => '012345678905', 'qty' => 2],
                    ],
                ],
                // 10 days ago
                [
                    'days_ago' => 10,
                    'cashier' => $staff,
                    'customer' => $walkIn,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '7622210449283', 'qty' => 2],
                        ['sku' => '12345678', 'qty' => 1],
                    ],
                ],
                // 7 days ago
                [
                    'days_ago' => 7,
                    'cashier' => $admin,
                    'customer' => $customerJohn,
                    'method' => PaymentMethod::TRANSFER,
                    'items' => [
                        ['sku' => '9300675001234', 'qty' => 1],
                        ['sku' => '5449000000996', 'qty' => 4],
                    ],
                ],
                // 5 days ago
                [
                    'days_ago' => 5,
                    'cashier' => $staff,
                    'customer' => $walkIn,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '8801043001234', 'qty' => 5],
                        ['sku' => '8992761001001', 'qty' => 2],
                    ],
                ],
                // 3 days ago
                [
                    'days_ago' => 3,
                    'cashier' => $staff,
                    'customer' => $customerAmina,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '6901234567890', 'qty' => 2],
                        ['sku' => '5012345678900', 'qty' => 1],
                    ],
                ],
                // Yesterday
                [
                    'days_ago' => 1,
                    'cashier' => $admin,
                    'customer' => $walkIn,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '012345678905', 'qty' => 3],
                        ['sku' => '7622210449283', 'qty' => 2],
                        ['sku' => '12345678', 'qty' => 2],
                    ],
                ],
                // Today (Morning)
                [
                    'days_ago' => 0,
                    'cashier' => $staff,
                    'customer' => $customerJohn,
                    'method' => PaymentMethod::CASH,
                    'items' => [
                        ['sku' => '5449000000996', 'qty' => 3],
                        ['sku' => '8992761001001', 'qty' => 4],
                    ],
                ],
                // Today (Afternoon)
                [
                    'days_ago' => 0,
                    'cashier' => $admin,
                    'customer' => $walkIn,
                    'method' => PaymentMethod::TRANSFER,
                    'items' => [
                        ['sku' => '9300675001234', 'qty' => 2],
                        ['sku' => '8801043001234', 'qty' => 3],
                    ],
                ],
            ];

            $saleCounter = 1001;
            foreach ($salesSchedule as $plan) {
                $saleDate = Carbon::now()->subDays($plan['days_ago'])->setHour(rand(10, 18))->setMinute(rand(5, 55));
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
                $cashReceived = ($plan['method'] === PaymentMethod::CASH) ? ceil($total / 5) * 5 : $total;
                $change = round($cashReceived - $total, 2);

                $sale = Sale::create([
                    'invoice_number' => $invNumber,
                    'customer_id' => $plan['customer']->id,
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
                    'notes' => 'Sample retail transaction.',
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
                        'notes' => 'Sold via POS.',
                        'created_at' => $saleDate,
                    ]);

                    $prodModel->update(['quantity' => $after]);
                }
            }
        }

        // 8. Operational Expenses (for P&L Statement and Finance Dashboard)
        $expenseCatUtilities = FinanceCategory::firstOrCreate(
            ['slug' => 'electricity-utilities'],
            ['name' => 'Electricity & Utilities', 'type' => FinanceCategoryType::Expense, 'description' => 'Power, water, and cooling.']
        );
        $expenseCatRent = FinanceCategory::firstOrCreate(
            ['slug' => 'store-rent'],
            ['name' => 'Store Rent', 'type' => FinanceCategoryType::Expense, 'description' => 'Monthly facility lease.']
        );
        $expenseCatInternet = FinanceCategory::firstOrCreate(
            ['slug' => 'internet-pos-communications'],
            ['name' => 'Internet & POS Communications', 'type' => FinanceCategoryType::Expense, 'description' => 'Broadband and POS sync cellular data.']
        );
        $expenseCatPackaging = FinanceCategory::firstOrCreate(
            ['slug' => 'packaging-bag-supplies'],
            ['name' => 'Packaging & Bag Supplies', 'type' => FinanceCategoryType::Expense, 'description' => 'Shopping bags, thermal rolls, and packaging.']
        );

        $expenses = [
            [
                'cat' => $expenseCatRent,
                'amount' => 450.00,
                'desc' => 'Monthly retail space rent',
                'date' => Carbon::now()->startOfMonth()->addDays(1),
            ],
            [
                'cat' => $expenseCatUtilities,
                'amount' => 95.00,
                'desc' => 'Electric bill & commercial refrigerator cooling',
                'date' => Carbon::now()->subDays(10),
            ],
            [
                'cat' => $expenseCatInternet,
                'amount' => 35.00,
                'desc' => 'Fiber internet for POS and cameras',
                'date' => Carbon::now()->subDays(8),
            ],
            [
                'cat' => $expenseCatPackaging,
                'amount' => 25.00,
                'desc' => 'Receipt paper rolls and shopping bags',
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
