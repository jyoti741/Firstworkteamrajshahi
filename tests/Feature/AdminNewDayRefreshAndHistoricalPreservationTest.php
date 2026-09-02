<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\SellerOverview;
use App\Livewire\Seller\QuickSell;
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

class AdminNewDayRefreshAndHistoricalPreservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Product $burger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Farhan Owner',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Rahim Seller',
            'role' => 'seller',
            'is_active' => true,
            'locale' => 'en',
        ]);

        $category = Category::create([
            'name' => 'Burgers',
            'name_bn' => 'বার্গার',
        ]);

        $this->burger = Product::create([
            'category_id' => $category->id,
            'name' => 'Classic Burger',
            'name_bn' => 'ক্লাসিক বার্গার',
            'price' => 150,
            'cost_price' => 80,
            'is_available' => true,
        ]);
    }

    public function test_admin_refreshes_on_new_day_while_preserving_previous_day_records(): void
    {
        // ==========================================
        // 1. SEED YESTERDAY'S CLOSED SESSION
        // ==========================================
        $yesterday = Carbon::yesterday();
        $yesterdayDay = BusinessDay::create([
            'date' => $yesterday,
            'status' => 'closed',
            'opened_at' => (clone $yesterday)->setHour(10)->setMinute(0),
            'closed_at' => (clone $yesterday)->setHour(22)->setMinute(0),
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
            'closing_cash_amount' => 3000,
        ]);

        $yesterdaySale = Sale::create([
            'invoice_no' => 'INV-YEST-3000',
            'user_id' => $this->seller->id,
            'business_day_id' => $yesterdayDay->id,
            'total_amount' => 3000,
            'total_cost' => 1600,
            'total_profit' => 1400,
            'total_items_count' => 20,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => (clone $yesterday)->setHour(15)->setMinute(0),
            'updated_at' => (clone $yesterday)->setHour(15)->setMinute(0),
        ]);

        SaleItem::create([
            'sale_id' => $yesterdaySale->id,
            'product_id' => $this->burger->id,
            'product_name' => $this->burger->name,
            'unit_price' => 150,
            'unit_cost' => 80,
            'quantity' => 20,
            'subtotal' => 3000,
            'profit' => 1400,
            'created_at' => (clone $yesterday)->setHour(15)->setMinute(0),
            'updated_at' => (clone $yesterday)->setHour(15)->setMinute(0),
        ]);

        Expense::create([
            'business_day_id' => $yesterdayDay->id,
            'user_id' => $this->admin->id,
            'title' => 'Yesterday Supplies',
            'category' => 'raw_materials',
            'amount' => 1000,
            'expense_date' => $yesterday->toDateString(),
        ]);

        // ==========================================
        // 2. VERIFY NEW DAY (TODAY) BEFORE OPENING CART
        // ==========================================
        $dash = Livewire::actingAs($this->admin)->test(Dashboard::class);
        $this->assertEquals(0.0, (float) $dash->viewData('todaySales'));
        $this->assertEquals(0, (int) $dash->viewData('todayItemsSold'));
        $dash->assertSee('Closed');

        // Verify yesterday's data is preserved when filtering by yesterday or this month
        $dash->set('timeRange', 'yesterday')
            ->assertSee('3,000') // Yesterday sales preserved
            ->assertSee('1,000'); // Yesterday expenses preserved

        Livewire::actingAs($this->admin)
            ->test(Reports::class)
            ->assertSee('3,000')
            ->assertSee('1,000');

        // ==========================================
        // 3. SELLER OPENS CART TODAY & SELLS 4 BURGERS (4 * 150 = 600)
        // ==========================================
        $qs = Livewire::actingAs($this->seller)->test(QuickSell::class);
        $qs->call('openCart');
        for ($i = 0; $i < 4; $i++) {
            $qs->call('recordSale', $this->burger->id, 1, 'cash');
        }

        // ==========================================
        // 4. VERIFY ADMIN REFLECTS NEW DAY LIVE DATA + PREVIOUS DAY IN TOTALS
        // ==========================================
        $dashToday = Livewire::actingAs($this->admin)->test(Dashboard::class);
        $this->assertEquals(600.0, (float) $dashToday->viewData('todaySales'));
        $this->assertEquals(4, (int) $dashToday->viewData('todayItemsSold'));
        $this->assertEquals(3600.0, (float) $dashToday->viewData('monthSales')); // 3000 yesterday + 600 today

        // ==========================================
        // 5. SELLER CLOSES TODAY'S CART SESSION
        // ==========================================
        $qs->call('openCloseModal')
            ->call('closeCart')
            ->assertSee('Cart Closed ✓');

        // Both distinct days/sessions are preserved in DB
        $this->assertEquals(2, BusinessDay::count());
        $this->assertEquals(3600.0, (float) Sale::sum('total_amount'));
    }
}
