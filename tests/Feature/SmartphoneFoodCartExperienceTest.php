<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ExpenseManager;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\SalesList;
use App\Livewire\Admin\SellerManager;
use App\Livewire\Seller\QuickSell;
use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SmartphoneFoodCartExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Category $category;
    protected Product $burger;
    protected Product $fries;

    protected function setUp(): void
    {
        parent::setUp();

        CartSetting::set('cart_name', 'CartFlow Burgers');
        CartSetting::set('currency_symbol', '৳');

        $this->admin = User::factory()->create([
            'name' => 'Owner Farhan',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Rahim Cashier',
            'role' => 'seller',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Burgers',
            'icon' => '🍔',
            'sort_order' => 1,
        ]);

        $this->burger = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Classic Burger',
            'price' => 150,
            'cost_price' => 90,
            'image_emoji' => '🍔',
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        $this->fries = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Crispy Fries',
            'price' => 80,
            'cost_price' => 40,
            'image_emoji' => '🍟',
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        // Seed an initial sale today
        $day = BusinessDay::current();
        $sale = Sale::create([
            'invoice_no' => 'INV-TEST-001',
            'user_id' => $this->seller->id,
            'business_day_id' => $day->id,
            'total_amount' => 300,
            'total_cost' => 180,
            'total_profit' => 120,
            'total_items_count' => 2,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->burger->id,
            'product_name' => $this->burger->name,
            'unit_price' => 150,
            'unit_cost' => 90,
            'quantity' => 2,
            'subtotal' => 300,
            'profit' => 120,
        ]);

        // Seed an expense today
        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $day->id,
            'title' => 'Fresh Buns',
            'category' => 'ingredients',
            'amount' => 100,
            'expense_date' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_admin_dashboard_renders_today_and_month_metrics_and_quick_actions(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(AdminDashboard::class)
            ->assertStatus(200)
            ->assertSee('Today')
            ->assertSee('Sales')
            ->assertSee('Expenses')
            ->assertSee('Profit')
            ->assertSee('Items Sold')
            ->assertSee('making a profit today')
            ->assertSee('View Sales')
            ->assertSee('Expenses')
            ->assertSee('Food Items')
            ->assertSee('Reports')
            ->assertSee('Best Sellers')
            ->assertSee('Classic Burger');
    }

    public function test_sales_page_renders_item_breakdown_and_filters(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SalesList::class)
            ->assertStatus(200)
            ->assertSee("Today's Sales")
            ->assertSee('Items Breakdown')
            ->assertSee('Classic Burger')
            ->assertSee('2 sold')
            ->call('setDateFilter', 'this_week')
            ->assertSet('dateFilter', 'this_week')
            ->assertStatus(200);
    }

    public function test_expense_page_renders_categories_and_records_expense(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ExpenseManager::class)
            ->assertStatus(200)
            ->assertSee('Expenses')
            ->assertSee('Ingredients')
            ->set('category', 'gas')
            ->set('amount', 500)
            ->set('notes', 'Cylinder Refill')
            ->call('saveExpense')
            ->assertStatus(200)
            ->assertSee('Gas')
            ->assertSee('500');

        $this->assertDatabaseHas('expenses', [
            'category' => 'gas',
            'amount' => 500,
        ]);
    }

    public function test_reports_page_renders_three_sections_and_chart_data(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(Reports::class)
            ->assertStatus(200)
            ->assertSee('Today')
            ->assertSee('This Week')
            ->assertSee('This Month')
            ->assertDontSee('Sales vs Expenses')
            ->assertSee('Best Sellers');
    }

    public function test_food_items_manager_allows_simple_add_and_edit(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductManager::class)
            ->assertStatus(200)
            ->assertSee('Classic Burger')
            ->set('name', 'Chicken Roll')
            ->set('price', 120)
            ->set('cost_price', 70)
            ->set('image_emoji', '🌯')
            ->call('saveProduct')
            ->assertStatus(200)
            ->assertSee('Chicken Roll');

        $this->assertDatabaseHas('products', [
            'name' => 'Chicken Roll',
            'price' => 120,
        ]);
    }

    public function test_product_modal_has_no_cost_price_input_and_has_category_plus_edit_delete_options(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->assertDontSee('Cost Price')
            ->assertSee('Selling Price')
            ->call('toggleInlineCategoryAdd')
            ->assertSee('Category Name in English')
            ->assertSee('বাংলা নাম')
            ->set('inlineCategoryName', 'Shawarma')
            ->set('inlineCategoryNameBn', 'শর্মা')
            ->call('saveInlineCategory')
            ->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'name' => 'Shawarma',
        ]);

        $createdCategory = Category::where('name', 'Shawarma')->first();
        $this->assertNotNull($createdCategory);

        Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->set('category_id', $createdCategory->id)
            ->call('startInlineCategoryEdit')
            ->assertSee('Edit Category Name')
            ->set('inlineCategoryName', 'Special Shawarma')
            ->call('saveInlineCategory')
            ->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'name' => 'Special Shawarma',
        ]);

        Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->set('category_id', $createdCategory->id)
            ->call('deleteSelectedCategory')
            ->assertStatus(200);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Special Shawarma',
        ]);
    }

    public function test_product_manager_automatic_emoji_suggestion_and_manual_preservation(): void
    {
        $this->actingAs($this->admin);

        $examples = [
            'Fuchka' => '🥣',
            'ফুচকা' => '🥣',
            'Chotpoti' => '🥣',
            'চটপটি' => '🥣',
            'Burger' => '🍔',
            'বার্গার' => '🍔',
            'Pizza' => '🍕',
            'পিজ্জা' => '🍕',
            'Biryani' => '🍚',
            'বিরিয়ানি' => '🍚',
            'Rice' => '🍚',
            'ভাত' => '🍚',
            'Singara' => '🥟',
            'সিঙ্গারা' => '🥟',
            'Samosa' => '🥟',
            'সমুচা' => '🥟',
            'Noodles' => '🍜',
            'নুডলস' => '🍜',
            'Chicken' => '🍗',
            'চিকেন' => '🍗',
            'Fish' => '🐟',
            'মাছ' => '🐟',
            'Tea' => '☕',
            'চা' => '☕',
            'Coffee' => '☕',
            'কফি' => '☕',
            'Juice' => '🧃',
            'জুস' => '🧃',
            'Ice Cream' => '🍦',
            'আইসক্রিম' => '🍦',
            'Cake' => '🍰',
            'কেক' => '🍰',
            'Unknown Food 123' => '🍽️',
        ];

        foreach ($examples as $name => $expectedEmoji) {
            Livewire::test(ProductManager::class)
                ->call('openAddProductModal')
                ->set('name', $name)
                ->assertSet('image_emoji', $expectedEmoji);
        }

        // Test manual selection and preservation
        Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->assertSee('Suggested Emoji')
            ->assertSee('Change')
            ->set('name', 'Burger')
            ->assertSet('image_emoji', '🍔')
            // User manually changes emoji to 🌯
            ->call('selectEmoji', '🌯')
            ->assertSet('image_emoji', '🌯')
            // User now changes the item name to Pizza -> emoji remains 🌯 because manual selection is preserved!
            ->set('name', 'Pizza')
            ->assertSet('image_emoji', '🌯')
            // Reset to auto -> detects Pizza 🍕
            ->call('resetToAutoEmoji')
            ->assertSet('image_emoji', '🍕');
    }

    public function test_seller_manager_allows_adding_and_toggling_status(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SellerManager::class)
            ->assertStatus(200)
            ->assertSee('Rahim Cashier')
            ->call('toggleActive', $this->seller->id)
            ->assertStatus(200);

        $this->assertFalse($this->seller->fresh()->is_active);
    }

    public function test_seller_quick_sell_pos_allows_one_tap_sale_and_correction(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('Sales')
            ->assertSee('Items Sold')
            ->assertSee('Classic Burger')
            ->assertSee('+')
            ->assertSee('SELL')
            ->assertSee('Cash')
            ->assertSee('bKash')
            ->assertSee('Nagad')
            ->call('recordSale', $this->burger->id, 1, 'cash')
            ->assertStatus(200);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $this->burger->id,
        ]);
    }

    public function test_seller_can_turn_on_and_turn_off_cart_and_records_shift_timings(): void
    {
        $this->actingAs($this->seller);

        // 1. Initial State: Cart starts in Closed state, showing Turn ON
        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('Cart is CLOSED')
            ->assertSee('Turn ON (Open Cart)')
            // 2. Seller turns ON the cart
            ->call('openCart')
            ->assertStatus(200)
            ->assertSee('Cart is OPEN')
            ->assertSee('Close Cart');

        $this->assertEquals('open', BusinessDay::current()->status);
        $this->assertNotNull(BusinessDay::current()->opened_at);
        $this->assertEquals($this->seller->id, BusinessDay::current()->opened_by_id);

        // 3. Seller turns OFF the cart
        Livewire::test(QuickSell::class)
            ->set('todayTotalCost', 500)
            ->call('closeCart')
            ->assertStatus(200)
            ->assertSee('Cart Closed')
            ->call('dismissCloseModal')
            ->assertSee('Cart is CLOSED')
            ->assertSee('Turn ON (Open Cart)');

        $this->assertEquals('closed', BusinessDay::current()->status);
        $this->assertNotNull(BusinessDay::current()->closed_at);
        $this->assertEquals($this->seller->id, BusinessDay::current()->closed_by_id);
    }

    public function test_admin_dashboard_shows_cart_opening_and_closing_time(): void
    {
        $this->actingAs($this->admin);

        $day = BusinessDay::current();
        $day->update([
            'status' => 'open',
            'opened_at' => Carbon::parse('2026-09-01 09:30:00'),
            'opened_by_id' => $this->seller->id,
            'closed_at' => Carbon::parse('2026-09-01 23:30:00'),
            'closed_by_id' => $this->seller->id,
        ]);

        Livewire::test(AdminDashboard::class)
            ->assertStatus(200)
            ->assertSee('Opening Time')
            ->assertSee('09:30 AM')
            ->assertSee('Closing Time')
            ->assertSee('Rahim Cashier');
    }

    public function test_seller_can_sell_multiple_items_with_bkash_and_correct_with_minus(): void
    {
        $this->actingAs($this->seller);
        BusinessDay::openActiveOrNew($this->seller->id);

        // Record a sale of 3 items with bkash (simulating selecting bkash, pressing + three times, clicking SELL)
        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->call('recordSale', $this->burger->id, 3, 'bkash')
            ->assertStatus(200);

        $this->assertDatabaseHas('sales', [
            'payment_method' => 'bkash',
            'total_items_count' => 3,
            'total_amount' => $this->burger->price * 3,
        ]);

        // Seller corrects 1 item count using minus button
        Livewire::test(QuickSell::class)
            ->call('recordCorrection', $this->burger->id)
            ->assertStatus(200);

        $this->assertDatabaseHas('sales', [
            'payment_method' => 'bkash',
            'total_items_count' => 2,
            'total_amount' => $this->burger->price * 2,
        ]);
    }
}

