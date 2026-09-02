<?php

namespace Database\Seeders;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FoodCartSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        CartSetting::set('cart_name', 'CartFlow Street Kitchen');
        CartSetting::set('cart_tagline', 'Fresh & Fast Street Burgers');
        CartSetting::set('currency_symbol', '৳');
        CartSetting::set('phone', '+880 1711-889900');
        CartSetting::set('address', 'Dhanmondi Lake Walkway, Dhaka');
        CartSetting::set('allow_seller_expense', '1');
        CartSetting::set('receipt_footer', 'Thank you for your order! Follow us @CartFlowEats');

        // 2. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@cartflow.test'],
            [
                'name' => 'Farhan Owner',
                'role' => 'admin',
                'phone' => '+880 1711-000001',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $seller1 = User::firstOrCreate(
            ['email' => 'seller@cartflow.test'],
            [
                'name' => 'Rahim Cashier',
                'role' => 'seller',
                'phone' => '+880 1812-000002',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $seller2 = User::firstOrCreate(
            ['email' => 'staff@cartflow.test'],
            [
                'name' => 'Karim Staff',
                'role' => 'seller',
                'phone' => '+880 1913-000003',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Categories
        $categoriesData = [
            ['name' => 'Burgers', 'name_bn' => 'বার্গার', 'icon' => '🍔', 'sort_order' => 1],
            ['name' => 'Crispy Chicken', 'name_bn' => 'ক্রিস্পি চিকেন', 'icon' => '🍗', 'sort_order' => 2],
            ['name' => 'Fries & Sides', 'name_bn' => 'ফ্রেঞ্চ ফ্রাইস ও সাইডস', 'icon' => '🍟', 'sort_order' => 3],
            ['name' => 'Drinks & Shakes', 'name_bn' => 'ড্রিংকস ও শেকস', 'icon' => '🥤', 'sort_order' => 4],
            ['name' => 'Value Combos', 'name_bn' => 'কম্বো অফার', 'icon' => '🍱', 'sort_order' => 5],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['name']] = Category::updateOrCreate(['name' => $c['name']], $c);
        }

        // 4. Products
        $productsData = [
            // Burgers
            [
                'category_id' => $categories['Burgers']->id,
                'name' => 'Classic Beef Burger',
                'name_bn' => 'ক্লাসিক বিফ বার্গার',
                'description' => 'Juicy handmade beef patty, secret sauce, lettuce, tomato & fresh brioche bun',
                'price' => 180,
                'cost_price' => 105,
                'image_emoji' => '🍔',
                'current_stock' => 45,
                'track_inventory' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $categories['Burgers']->id,
                'name' => 'Smokey BBQ Beef Burger',
                'name_bn' => 'স্মোকি বারবিকিউ বিফ বার্গার',
                'description' => 'Beef patty with smoked BBQ sauce, caramelized onions and melted cheddar',
                'price' => 230,
                'cost_price' => 130,
                'image_emoji' => '🍔',
                'current_stock' => 35,
                'track_inventory' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $categories['Burgers']->id,
                'name' => 'Crispy Chicken Burger',
                'name_bn' => 'ক্রিস্পি চিকেন বার্গার',
                'description' => 'Golden fried chicken breast, spicy mayo & crunchy pickles',
                'price' => 160,
                'cost_price' => 90,
                'image_emoji' => '🍔',
                'current_stock' => 50,
                'track_inventory' => true,
                'sort_order' => 3,
            ],
            [
                'category_id' => $categories['Burgers']->id,
                'name' => 'Double Cheese Blast Burger',
                'name_bn' => 'ডাবল চিজ ব্লাস্ট বার্গার',
                'description' => 'Double beef patties, double cheddar slices and melted cheese sauce',
                'price' => 280,
                'cost_price' => 160,
                'image_emoji' => '🧀',
                'current_stock' => 25,
                'track_inventory' => true,
                'sort_order' => 4,
            ],
            [
                'category_id' => $categories['Burgers']->id,
                'name' => 'Naga Fire Burger',
                'name_bn' => 'নাগা ফায়ার বার্গার',
                'description' => 'Extra spicy authentic Naga pickle-infused chicken or beef patty',
                'price' => 200,
                'cost_price' => 110,
                'image_emoji' => '🌶️',
                'current_stock' => 30,
                'track_inventory' => true,
                'sort_order' => 5,
            ],

            // Crispy Chicken
            [
                'category_id' => $categories['Crispy Chicken']->id,
                'name' => 'Crispy Fried Chicken (2 Pcs)',
                'name_bn' => 'ক্রিস্পি ফ্রাইড চিকেন (২ পিস)',
                'description' => 'Secret 11-spice herb crispy fried chicken drumstick & thigh',
                'price' => 170,
                'cost_price' => 95,
                'image_emoji' => '🍗',
                'current_stock' => 40,
                'track_inventory' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $categories['Crispy Chicken']->id,
                'name' => 'Naga Hot Wings (4 Pcs)',
                'name_bn' => 'নাগা হট উইংস (৪ পিস)',
                'description' => 'Glazed in fiery hot chili sauce with garlic dip',
                'price' => 190,
                'cost_price' => 100,
                'image_emoji' => '🍗',
                'current_stock' => 30,
                'track_inventory' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $categories['Crispy Chicken']->id,
                'name' => 'Popcorn Chicken Bucket',
                'name_bn' => 'পপকর্ন চিকেন বাকেট',
                'description' => 'Bite-sized seasoned crunchy chicken bites',
                'price' => 150,
                'cost_price' => 80,
                'image_emoji' => '🍿',
                'current_stock' => 50,
                'track_inventory' => true,
                'sort_order' => 3,
            ],

            // Fries & Sides
            [
                'category_id' => $categories['Fries & Sides']->id,
                'name' => 'Salted French Fries',
                'name_bn' => 'সল্টেড ফ্রেঞ্চ ফ্রাইস',
                'description' => 'Crispy golden potato fries lightly salted',
                'price' => 90,
                'cost_price' => 40,
                'image_emoji' => '🍟',
                'current_stock' => 60,
                'track_inventory' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $categories['Fries & Sides']->id,
                'name' => 'Peri Peri Masala Fries',
                'name_bn' => 'পেরি পেরি মসলা ফ্রাইস',
                'description' => 'Tossed in special zesty peri peri seasoning',
                'price' => 120,
                'cost_price' => 50,
                'image_emoji' => '🍟',
                'current_stock' => 45,
                'track_inventory' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $categories['Fries & Sides']->id,
                'name' => 'Cheesy Loaded Fries',
                'name_bn' => 'চিজি লোডেড ফ্রাইস',
                'description' => 'Topped with warm melted cheese sauce and jalapeños',
                'price' => 180,
                'cost_price' => 85,
                'image_emoji' => '🍟',
                'current_stock' => 30,
                'track_inventory' => true,
                'sort_order' => 3,
            ],

            // Drinks & Shakes
            [
                'category_id' => $categories['Drinks & Shakes']->id,
                'name' => 'Cold Coffee Float',
                'name_bn' => 'কোল্ড কফি ফ্লোট',
                'description' => 'Rich blended iced coffee topped with a vanilla ice cream scoop',
                'price' => 110,
                'cost_price' => 45,
                'image_emoji' => '🧋',
                'current_stock' => 40,
                'track_inventory' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $categories['Drinks & Shakes']->id,
                'name' => 'Oreo Chocolate Shake',
                'name_bn' => 'ওরিও চকলেট শেক',
                'description' => 'Thick milkshake made with crushed Oreos and chocolate drizzle',
                'price' => 150,
                'cost_price' => 65,
                'image_emoji' => '🥤',
                'current_stock' => 35,
                'track_inventory' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $categories['Drinks & Shakes']->id,
                'name' => 'Blue Lagoon Mojito',
                'name_bn' => 'ব্লু লাগুন মোহিতো',
                'description' => 'Refreshing curacao, mint, lime & sparkling soda',
                'price' => 120,
                'cost_price' => 40,
                'image_emoji' => '🍹',
                'current_stock' => 40,
                'track_inventory' => true,
                'sort_order' => 3,
            ],
            [
                'category_id' => $categories['Drinks & Shakes']->id,
                'name' => 'Chilled Soft Drink (Can)',
                'name_bn' => 'কোল্ড ড্রিংকস (ক্যান)',
                'description' => 'Coca-Cola / Sprite / Mountain Dew 250ml can',
                'price' => 40,
                'cost_price' => 28,
                'image_emoji' => '🥤',
                'current_stock' => 80,
                'track_inventory' => true,
                'sort_order' => 4,
            ],

            // Combos
            [
                'category_id' => $categories['Value Combos']->id,
                'name' => 'Solo Feast Combo',
                'name_bn' => 'সোলো ফিস্ট কম্বো',
                'description' => 'Classic Beef Burger + Salted Fries + Soft Drink',
                'price' => 290,
                'cost_price' => 160,
                'image_emoji' => '🍱',
                'current_stock' => 30,
                'track_inventory' => false,
                'sort_order' => 1,
            ],
            [
                'category_id' => $categories['Value Combos']->id,
                'name' => 'Buddy Box (For 2)',
                'name_bn' => 'বাডি বক্স (২ জনের জন্য)',
                'description' => '2 Crispy Chicken Burgers + Loaded Fries + 2 Cold Coffees',
                'price' => 580,
                'cost_price' => 320,
                'image_emoji' => '🍱',
                'current_stock' => 20,
                'track_inventory' => false,
                'sort_order' => 2,
            ],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $products[] = Product::updateOrCreate(['name' => $p['name']], $p);
        }

        // 5. Create Business Days & Historical Sales (Past 3 days + Today)
        $paymentMethods = ['cash', 'cash', 'bkash', 'nagad'];
        $expenseCategories = ['raw_materials', 'utilities', 'transport', 'rent', 'maintenance'];

        for ($daysAgo = 3; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo)->toDateString();
            $businessDay = BusinessDay::firstOrCreate(
                ['date' => $date],
                [
                    'status' => $daysAgo === 0 ? 'open' : 'closed',
                    'opening_cash_float' => 1000,
                    'closing_cash_amount' => $daysAgo === 0 ? null : (1000 + rand(3500, 8000)),
                    'opened_at' => Carbon::parse($date.' 10:00:00'),
                    'closed_at' => $daysAgo === 0 ? null : Carbon::parse($date.' 22:30:00'),
                    'opened_by_id' => $admin->id,
                    'closed_by_id' => $daysAgo === 0 ? null : $admin->id,
                ]
            );

            // Seed 8-15 sales per day
            $salesCount = $daysAgo === 0 ? 8 : rand(10, 16);
            for ($s = 1; $s <= $salesCount; $s++) {
                $seller = ($s % 2 === 0) ? $seller1 : $seller2;
                $hour = rand(11, 21);
                $minute = rand(0, 59);
                $saleTime = Carbon::parse($date." {$hour}:{$minute}:00");

                // Pick 1-3 random items
                $selectedProducts = collect($products)->random(rand(1, 3));
                $totalAmount = 0;
                $totalCost = 0;
                $totalItemsCount = 0;
                $itemsData = [];

                foreach ($selectedProducts as $prod) {
                    $qty = rand(1, 2);
                    $subtotal = $prod->price * $qty;
                    $cost = $prod->cost_price * $qty;
                    $profit = $subtotal - $cost;

                    $totalAmount += $subtotal;
                    $totalCost += $cost;
                    $totalItemsCount += $qty;

                    $itemsData[] = [
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'unit_price' => $prod->price,
                        'unit_cost' => $prod->cost_price,
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                        'profit' => $profit,
                        'created_at' => $saleTime,
                        'updated_at' => $saleTime,
                    ];
                }

                $invNo = 'INV-'.Carbon::parse($date)->format('ymd').'-'.str_pad((string) $s, 4, '0', STR_PAD_LEFT);
                $sale = Sale::firstOrCreate(
                    ['invoice_no' => $invNo],
                    [
                        'user_id' => $seller->id,
                        'business_day_id' => $businessDay->id,
                        'total_amount' => $totalAmount,
                        'total_cost' => $totalCost,
                        'total_profit' => $totalAmount - $totalCost,
                        'total_items_count' => $totalItemsCount,
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'status' => 'completed',
                        'created_at' => $saleTime,
                        'updated_at' => $saleTime,
                    ]
                );

                if ($sale->wasRecentlyCreated) {
                    foreach ($itemsData as $item) {
                        $item['sale_id'] = $sale->id;
                        SaleItem::create($item);
                    }
                }
            }

            // Seed 1-2 expenses per day
            $dailyExpenses = [
                ['title' => 'Fresh Brioche Buns & Patties restock', 'category' => 'raw_materials', 'amount' => rand(800, 1500)],
                ['title' => 'Cooking Gas cylinder refill', 'category' => 'utilities', 'amount' => 1250],
                ['title' => 'Ice blocks & fresh lemons for drinks', 'category' => 'raw_materials', 'amount' => 350],
                ['title' => 'Daily cart parking space rent', 'category' => 'rent', 'amount' => 300],
            ];

            $pickedExpense = $dailyExpenses[array_rand($dailyExpenses)];
            Expense::create([
                'user_id' => ($daysAgo === 0) ? $seller1->id : $admin->id,
                'business_day_id' => $businessDay->id,
                'title' => $pickedExpense['title'],
                'category' => $pickedExpense['category'],
                'amount' => $pickedExpense['amount'],
                'expense_date' => $date,
                'notes' => 'Authorized daily business expense',
                'created_at' => Carbon::parse($date.' 14:00:00'),
                'updated_at' => Carbon::parse($date.' 14:00:00'),
            ]);
        }
    }
}
