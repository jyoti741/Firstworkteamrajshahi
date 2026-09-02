<?php

namespace Tests\Feature;

use App\Livewire\Admin\SellerOverview;
use App\Models\BusinessDay;
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

class AdminSellerOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $sellerRahim;
    protected User $sellerKarim;
    protected Product $burger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Farhan Owner',
            'email' => 'admin@cartflow.test',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->sellerRahim = User::factory()->create([
            'name' => 'Rahim',
            'email' => 'rahim@cartflow.test',
            'role' => 'seller',
            'is_active' => true,
        ]);

        $this->sellerKarim = User::factory()->create([
            'name' => 'Karim',
            'email' => 'karim@cartflow.test',
            'role' => 'seller',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Burgers',
            'slug' => 'burgers',
            'icon' => '🍔',
        ]);

        $this->burger = Product::create([
            'category_id' => $category->id,
            'name' => 'Chicken Burger',
            'bangla_name' => 'চিকেন বার্গার',
            'price' => 150,
            'cost_price' => 90,
            'stock_quantity' => 100,
            'is_available' => true,
        ]);
    }

    public function test_admin_can_access_seller_overview_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.sellers.overview'));
        $response->assertStatus(200);
        $response->assertSee('All Registered Sellers');
    }

    public function test_seller_is_forbidden_from_accessing_admin_seller_overview(): void
    {
        $response = $this->actingAs($this->sellerRahim)->get(route('admin.sellers.overview'));
        $response->assertRedirect(route('seller.quick-sell'));
    }

    public function test_admin_can_view_specific_seller_filtered_data(): void
    {
        // 1. Create a business day
        $day = BusinessDay::create([
            'date' => Carbon::today()->toDateString(),
            'status' => 'closed',
            'opened_at' => Carbon::now()->subHours(4),
            'closed_at' => Carbon::now(),
            'opened_by_id' => $this->sellerRahim->id,
            'closed_by_id' => $this->sellerRahim->id,
            'opening_cash_float' => 500,
            'closing_cost' => 2500,
            'closing_cash_amount' => 6000,
        ]);

        // 2. Rahim sales: 8,000 (55 items)
        $saleRahim = Sale::create([
            'invoice_no' => 'INV-RAHIM-01',
            'user_id' => $this->sellerRahim->id,
            'business_day_id' => $day->id,
            'total_amount' => 8000,
            'total_cost' => 4500,
            'total_profit' => 3500,
            'total_items_count' => 55,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $saleRahim->id,
            'product_id' => $this->burger->id,
            'product_name' => 'Chicken Burger',
            'quantity' => 55,
            'unit_price' => 150,
            'subtotal' => 8000,
        ]);

        // 3. Rahim expense: 2,500
        Expense::create([
            'user_id' => $this->sellerRahim->id,
            'business_day_id' => $day->id,
            'title' => "Today's Total Cost",
            'category' => 'raw_materials',
            'amount' => 2500,
            'expense_date' => Carbon::today()->toDateString(),
        ]);

        // 4. Karim sales: 3,000 (20 items)
        $saleKarim = Sale::create([
            'invoice_no' => 'INV-KARIM-01',
            'user_id' => $this->sellerKarim->id,
            'business_day_id' => $day->id,
            'total_amount' => 3000,
            'total_cost' => 1800,
            'total_profit' => 1200,
            'total_items_count' => 20,
            'payment_method' => 'bkash',
            'status' => 'completed',
        ]);

        // 5. Test Livewire Component for Rahim
        Livewire::actingAs($this->admin)
            ->test(SellerOverview::class, ['user' => $this->sellerRahim])
            ->assertSee('Rahim')
            ->assertSee('8,000') // Rahim's Sales
            ->assertSee('2,500') // Rahim's Cost
            ->assertSee('5,500') // Rahim's Net Profit: 8000 - 2500 = 5500
            ->assertSee('55')    // Rahim's items sold
            ->assertDontSee('3,000'); // Karim's sales not in Rahim's total

        // 6. Test Livewire Component for Karim
        Livewire::actingAs($this->admin)
            ->test(SellerOverview::class, ['user' => $this->sellerKarim])
            ->assertSee('Karim')
            ->assertSee('3,000')
            ->assertDontSee('8,000');

        // 7. Test Livewire Component for All Sellers (Aggregated)
        Livewire::actingAs($this->admin)
            ->test(SellerOverview::class)
            ->assertSee('All Registered Sellers')
            ->assertSee('11,000'); // 8000 + 3000 = 11000
    }

    public function test_date_filter_switching_updates_statistics(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(SellerOverview::class, ['user' => $this->sellerRahim]);

        $component->call('setPeriod', 'week');
        $this->assertEquals('week', $component->get('period'));

        $component->call('setPeriod', 'month');
        $this->assertEquals('month', $component->get('period'));
    }
}
