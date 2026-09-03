<?php

namespace Tests\Feature;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMobileViewResponsiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'name' => 'Admin Owner Farzan',
            'email' => 'admin@cartflow.test',
        ]);

        CartSetting::set('cart_name', 'Rajshahi Street Food Kitchen');
        CartSetting::set('currency_symbol', '৳');
        CartSetting::set('allow_seller_expense', true);

        BusinessDay::create([
            'date' => today(),
            'status' => 'open',
            'opened_by' => $this->admin->id,
            'opened_at' => now(),
            'opening_cash_float' => 5000,
        ]);

        $category = Category::create([
            'name' => 'Burgers',
            'name_bn' => 'বার্গার',
            'icon' => '🍔',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Smoky BBQ Beef Burger Special Edition',
            'name_bn' => 'স্মোকি বিফ বার্গার স্পেশাল',
            'price' => 250,
            'cost_price' => 150,
            'is_available' => true,
            'image_emoji' => '🍔',
        ]);

        $sale = Sale::create([
            'user_id' => $this->admin->id,
            'invoice_no' => 'CF-TEST-1001',
            'total_amount' => 250,
            'total_items_count' => 1,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 250,
            'unit_cost' => 150,
            'quantity' => 1,
            'subtotal' => 250,
            'profit' => 100,
        ]);

        Expense::create([
            'user_id' => $this->admin->id,
            'category' => 'ingredients',
            'amount' => 1500,
            'expense_date' => today(),
            'title' => 'Fresh Buns and Beef Patties Wholesale',
            'notes' => 'Urgent procurement for today evening rush',
        ]);
    }

    public function test_admin_dashboard_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('min-w-0 flex-1 mr-2', false);
        $response->assertSee('tracking-tight truncate', false);
        $response->assertSee('text-lg sm:text-2xl font-black text-[#1E8E3E] tracking-tight mt-1 truncate', false);
        $response->assertSee('flex flex-wrap items-center gap-x-2 gap-y-0.5', false);
        $response->assertSee('truncate block', false);
    }

    public function test_admin_sales_list_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.sales'));

        $response->assertOk();
        $response->assertSee('shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap', false);
        $response->assertSee('w-full sm:w-48', false);
        $response->assertSee('truncate block', false);
    }

    public function test_admin_expense_manager_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.expenses'));

        $response->assertOk();
        $response->assertSee('shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap', false);
        $response->assertSee('min-w-0 flex-1', false);
        $response->assertSee('w-full sm:w-48', false);
        $response->assertSee('break-words', false);
    }

    public function test_admin_product_manager_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products'));

        $response->assertOk();
        $response->assertSee('flex flex-col sm:flex-row sm:items-center gap-2', false);
        $response->assertSee('min-w-0 flex-1', false);
        $response->assertSee('truncate', false);
        $response->assertSee('flex flex-wrap items-center gap-x-2 gap-y-0.5', false);
    }

    public function test_admin_reports_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports'));

        $response->assertOk();
        $response->assertSee('grid grid-cols-1 sm:grid-cols-3', false);
        $response->assertSee('truncate block', false);
    }

    public function test_admin_seller_manager_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.sellers'));

        $response->assertOk();
        $response->assertSee('min-w-0 flex-1', false);
        $response->assertSee('flex flex-wrap items-center gap-x-2 gap-y-0.5', false);
    }

    public function test_admin_seller_overview_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.sellers.overview'));

        $response->assertOk();
        $response->assertSee('p-3 sm:p-4', false);
        $response->assertSee('Top Food Items', false);
    }

    public function test_admin_settings_renders_with_mobile_responsive_classes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('text-lg sm:text-2xl font-extrabold', false);
        $response->assertSee('min-w-[640px]', false);
    }
}
