<?php

namespace Tests\Feature;

use App\Helpers\BanglaHelper;
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

class SellerCloseCartCostTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $admin;
    protected BusinessDay $businessDay;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        CartSetting::set('cart_name', 'CartFlow Burgers');
        CartSetting::set('currency_symbol', '৳');

        $this->admin = User::factory()->create([
            'name' => 'Farhan Owner',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Rahim Cashier',
            'role' => 'seller',
            'is_active' => true,
            'locale' => 'en',
        ]);

        $category = Category::create([
            'name' => 'Burgers',
            'name_bn' => 'বার্গার',
            'icon' => '🍔',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Classic Burger',
            'name_bn' => 'ক্লাসিক বার্গার',
            'price' => 200,
            'cost_price' => 100,
            'image_emoji' => '🍔',
            'current_stock' => 100,
        ]);

        $this->businessDay = BusinessDay::openActiveOrNew($this->seller->id);

        // Record 40 items @ 200 = ৳8,000 total sales
        $sale = Sale::create([
            'invoice_no' => 'INV-TEST-8000',
            'user_id' => $this->seller->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 8000,
            'total_cost' => 4000,
            'total_profit' => 4000,
            'total_items_count' => 40,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => Carbon::now('Asia/Dhaka'),
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'unit_price' => 200,
            'unit_cost' => 100,
            'quantity' => 40,
            'subtotal' => 8000,
            'profit' => 4000,
            'created_at' => Carbon::now('Asia/Dhaka'),
        ]);
    }

    public function test_seller_can_open_close_cart_modal_and_see_summary(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('Close Cart')
            ->call('openCloseModal')
            ->assertSet('showCloseModal', true)
            ->assertSee('Total Sales')
            ->assertSee('৳8,000')
            ->assertSee('Total Items Sold')
            ->assertSee('40')
            ->assertSee("Today's Total Cost")
            ->assertSee('Submit');
    }

    public function test_seller_submits_total_cost_calculates_profit_and_closes_cart(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(QuickSell::class)
            ->call('openCloseModal')
            ->set('todayTotalCost', 2500)
            ->call('closeCart')
            ->assertHasNoErrors()
            ->assertSet('isCartOpen', false)
            ->assertSet('isCartClosedSubmitted', true)
            ->assertSet('closedSalesTotal', 8000.0)
            ->assertSet('closedCost', 2500.0)
            ->assertSet('closedProfit', 5500.0)
            ->assertSee('Cart Closed ✓')
            ->assertSee('৳8,000')
            ->assertSee('৳2,500')
            ->assertSee('৳5,500');

        // Check BusinessDay status and cost
        $this->businessDay->refresh();
        $this->assertTrue($this->businessDay->isClosed());
        $this->assertEquals(2500.0, (float) $this->businessDay->closing_cost);
        $this->assertEquals($this->seller->id, $this->businessDay->closed_by_id);

        // Check Expense record saved for Admin reports
        $this->assertDatabaseHas('expenses', [
            'business_day_id' => $this->businessDay->id,
            'amount' => 2500,
        ]);
    }

    public function test_close_cart_in_bangla_mode_displays_bangla_labels_numerals_and_profit(): void
    {
        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);
        app()->setLocale('bn');
        session(['seller_locale' => 'bn']);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('কার্ট বন্ধ করুন')
            ->call('openCloseModal')
            ->assertSet('showCloseModal', true)
            ->assertSee('মোট বিক্রি')
            ->assertSee('৳৮,০০০')
            ->assertSee('মোট বিক্রিত আইটেম')
            ->assertSee('৪০')
            ->assertSee('আজকের মোট খরচ')
            ->assertSee('জমা দিন')
            ->set('todayTotalCost', 2500)
            ->call('closeCart')
            ->assertHasNoErrors()
            ->assertSee('কার্ট বন্ধ হয়েছে ✓')
            ->assertSee('৳৮,০০০')
            ->assertSee('৳২,৫০০')
            ->assertSee('মোট লাভ')
            ->assertSee('৳৫,৫০০');
    }

    public function test_prevents_duplicate_closing_of_same_cart(): void
    {
        $this->actingAs($this->seller);

        // First close
        Livewire::test(QuickSell::class)
            ->call('openCloseModal')
            ->set('todayTotalCost', 2500)
            ->call('closeCart');

        $this->businessDay->refresh();
        $this->assertTrue($this->businessDay->isClosed());

        // Attempt second close
        Livewire::test(QuickSell::class)
            ->call('openCloseModal')
            ->assertSet('showCloseModal', false);

        // Ensure only 1 expense was created
        $this->assertEquals(1, Expense::where('business_day_id', $this->businessDay->id)->count());
    }

    public function test_cost_is_available_to_admin_reports_and_profit_calculations(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(QuickSell::class)
            ->call('openCloseModal')
            ->set('todayTotalCost', 2500)
            ->call('closeCart');

        // Verify Admin calculation
        $todayExpenses = (float) Expense::whereDate('expense_date', Carbon::today()->toDateString())->sum('amount');
        $this->assertEquals(2500.0, $todayExpenses);

        $todaySales = (float) Sale::where('status', 'completed')->whereDate('created_at', Carbon::today())->sum('total_amount');
        $this->assertEquals(8000.0, $todaySales);

        $adminCalculatedProfit = $todaySales - $todayExpenses;
        $this->assertEquals(5500.0, $adminCalculatedProfit);
    }
}
