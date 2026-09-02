<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Reports as AdminReports;
use App\Livewire\Admin\SellerOverview;
use App\Livewire\Seller\QuickSell;
use App\Models\BusinessDay;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartSessionResetAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Product $burger;
    protected Product $fries;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Owner Admin',
            'email' => 'admin@cartflow.test',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Rahim',
            'email' => 'rahim@cartflow.test',
            'role' => 'seller',
            'is_active' => true,
            'locale' => 'en',
        ]);

        $category = Category::create([
            'name' => 'Fast Food',
            'name_bn' => 'ফাস্ট ফুড',
            'slug' => 'fast-food',
        ]);

        $this->burger = Product::create([
            'category_id' => $category->id,
            'name' => 'Classic Burger',
            'name_bn' => 'ক্লাসিক বার্গার',
            'price' => 150,
            'cost_price' => 80,
            'is_available' => true,
        ]);

        $this->fries = Product::create([
            'category_id' => $category->id,
            'name' => 'Crispy Fries',
            'name_bn' => 'ক্রিস্পি ফ্রাইস',
            'price' => 100,
            'cost_price' => 50,
            'is_available' => true,
        ]);
    }

    public function test_cart_session_reset_after_closing_while_preserving_history(): void
    {
        $this->actingAs($this->seller);

        // ==========================================
        // 1. START SESSION 1
        // ==========================================
        Livewire::test(QuickSell::class)
            ->assertSee('Turn ON (Open Cart)')
            ->call('openCart')
            ->assertStatus(200)
            ->assertSee('Close Cart');

        $session1 = BusinessDay::activeSession();
        $this->assertNotNull($session1);
        $this->assertTrue($session1->isOpen());

        // Sells 10 Burgers (10 * 150 = 1500) and 5 Fries (5 * 100 = 500) -> Total = 2,000 (15 items)
        $qs1 = Livewire::test(QuickSell::class);
        for ($i = 0; $i < 10; $i++) {
            $qs1->call('recordSale', $this->burger->id);
        }
        for ($i = 0; $i < 5; $i++) {
            $qs1->call('recordSale', $this->fries->id);
        }

        $session1Sales = (float) Sale::where('business_day_id', $session1->id)->sum('total_amount');
        $session1Items = (int) Sale::where('business_day_id', $session1->id)->sum('total_items_count');
        $this->assertEquals(2000.0, $session1Sales);
        $this->assertEquals(15, $session1Items);

        // ==========================================
        // 2. CLOSE SESSION 1
        // ==========================================
        $qs1->call('openCloseModal')
            ->assertSee('৳2,000')
            ->assertSee('15')
            ->call('closeCart')
            ->assertHasNoErrors()
            ->assertSee('Cart Closed ✓')
            ->assertSee('৳2,000')
            ->assertSee('15')
            ->call('dismissCloseModal');

        $session1->refresh();
        $this->assertTrue($session1->isClosed());
        $this->assertNull(BusinessDay::activeSession());

        // ==========================================
        // 3. START SESSION 2 (REOPENING CART)
        // ==========================================
        $qs2 = Livewire::test(QuickSell::class)
            ->assertSee('Turn ON (Open Cart)')
            ->call('openCart')
            ->assertStatus(200)
            ->assertSee('Close Cart');

        $session2 = BusinessDay::activeSession();
        $this->assertNotNull($session2);
        $this->assertTrue($session2->isOpen());
        $this->assertNotEquals($session1->id, $session2->id);

        // Verify that Session 2 starts fresh with ZERO counters
        $productStats = $qs2->viewData('productTodaySales');
        $this->assertTrue($productStats->isEmpty() || !isset($productStats[$this->burger->id]));
        $this->assertEquals(0.0, (float) $qs2->viewData('todaySalesTotal'));
        $this->assertEquals(0, (int) $qs2->viewData('todayItemsTotal'));

        // ==========================================
        // 4. RECORD SALES IN SESSION 2
        // ==========================================
        // Sells 3 Burgers in Session 2 (3 * 150 = 450)
        for ($i = 0; $i < 3; $i++) {
            $qs2->call('recordSale', $this->burger->id);
        }

        $session2Sales = (float) Sale::where('business_day_id', $session2->id)->sum('total_amount');
        $session2Items = (int) Sale::where('business_day_id', $session2->id)->sum('total_items_count');
        $this->assertEquals(450.0, $session2Sales);
        $this->assertEquals(3, $session2Items);

        // ==========================================
        // 5. VERIFY COMPLETE HISTORICAL INTEGRITY
        // ==========================================
        // 1. TodaySales view for seller is refreshed to Session 2 only
        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Seller\TodaySales::class)
            ->assertSee('৳450') // Session 2 Sales
            ->assertDontSee('৳2,000'); // Session 1 Sales not in current session

        // 2. Session 1 sales still exist in DB
        $this->assertEquals(15, Sale::where('business_day_id', $session1->id)->sum('total_items_count'));
        $this->assertEquals(2000.0, (float) Sale::where('business_day_id', $session1->id)->sum('total_amount'));

        // 3. Session 2 sales exist in DB
        $this->assertEquals(3, Sale::where('business_day_id', $session2->id)->sum('total_items_count'));
        $this->assertEquals(450.0, (float) Sale::where('business_day_id', $session2->id)->sum('total_amount'));

        // 4. Both distinct sessions exist in DB
        $this->assertEquals(2, BusinessDay::count());

        // 5. Admin Dashboard reflects total cumulative sales across all sessions
        Livewire::actingAs($this->admin)
            ->test(AdminDashboard::class)
            ->assertSee('2,450') // Total Today Sales = 2000 + 450 = 2450
            ->assertSee('18');   // Total Items Sold = 15 + 3 = 18

        // 6. Admin Seller Overview reflects Rahim's cumulative sales and shift history
        Livewire::actingAs($this->admin)
            ->test(SellerOverview::class, ['user' => $this->seller])
            ->assertSee('Rahim')
            ->assertSee('2,450')
            ->assertSee('18');   // 18 units
    }
}
