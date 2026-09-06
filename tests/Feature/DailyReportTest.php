<?php

namespace Tests\Feature;

use App\Livewire\Admin\Reports;
use App\Models\AssetLiability;
use App\Models\BusinessDay;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DailyReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected BusinessDay $businessDay;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->businessDay = BusinessDay::create([
            'date' => Carbon::today()->toDateString(),
            'status' => 'open',
            'opened_at' => now(),
        ]);
    }

    public function test_admin_can_access_daily_report_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports'));
        $response->assertStatus(200);
        $response->assertSee('Daily Activity Report');
        $response->assertSee('Sales Records');
        $response->assertSee('Expenses');
        $response->assertSee('Assets Added');
    }

    public function test_report_displays_sales_with_item_details_and_bottom_total(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Smash Burger',
            'price' => 180,
            'cost_price' => 90,
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        $sale = new Sale([
            'invoice_no' => 'INV-260906-0001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 360,
            'total_cost' => 180,
            'total_profit' => 180,
            'total_items_count' => 2,
            'payment_method' => 'bkash',
            'status' => 'completed',
        ]);
        $sale->timestamps = false;
        $sale->created_at = Carbon::today()->setHour(14)->setMinute(30);
        $sale->updated_at = Carbon::today()->setHour(14)->setMinute(30);
        $sale->save();

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => 'Smash Burger',
            'quantity' => 2,
            'unit_price' => 180,
            'unit_cost' => 90,
            'subtotal' => 360,
            'profit' => 180,
        ]);

        Livewire::test(Reports::class)
            ->set('selectedDate', Carbon::today()->toDateString())
            ->assertSee('INV-260906-0001')
            ->assertSee('Smash Burger')
            ->assertSee('02:30 PM')
            ->assertSee('BKASH')
            ->assertSee('360')
            ->assertSee('Total Sales:')
            ->assertSee('2 items sold');
    }

    public function test_report_displays_expenses_and_bottom_total(): void
    {
        $this->actingAs($this->admin);

        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Gas Cylinder Refill',
            'category' => 'gas',
            'amount' => 2500,
            'expense_date' => Carbon::today()->toDateString(),
            'expense_time' => '11:15:00',
            'notes' => 'Store receipt #442',
        ]);

        Livewire::test(Reports::class)
            ->set('selectedDate', Carbon::today()->toDateString())
            ->assertSee('Gas Cylinder Refill')
            ->assertSee('Gas')
            ->assertSee('11:15 AM')
            ->assertSee('Store receipt #442')
            ->assertSee('2,500')
            ->assertSee('Total Expenses:');
    }

    public function test_report_displays_assets_and_bottom_total(): void
    {
        $this->actingAs($this->admin);

        AssetLiability::create([
            'type' => 'asset',
            'name' => 'Commercial Deep Freezer',
            'amount' => 45000,
            'record_date' => Carbon::today()->toDateString(),
            'record_time' => '10:00:00',
        ]);

        Livewire::test(Reports::class)
            ->set('selectedDate', Carbon::today()->toDateString())
            ->assertSee('Commercial Deep Freezer')
            ->assertSee('10:00 AM')
            ->assertSee('45,000')
            ->assertSee('Total Assets Added:');
    }

    public function test_records_from_other_dates_are_excluded(): void
    {
        $this->actingAs($this->admin);

        $yesterday = Carbon::today()->subDay()->toDateString();
        $today = Carbon::today()->toDateString();

        // Yesterday's activity
        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Yesterday Special Onion',
            'category' => 'ingredients',
            'amount' => 600,
            'expense_date' => $yesterday,
        ]);

        // Today's activity
        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Today Fresh Milk',
            'category' => 'ingredients',
            'amount' => 250,
            'expense_date' => $today,
        ]);

        Livewire::test(Reports::class)
            ->set('selectedDate', $today)
            ->assertSee('Today Fresh Milk')
            ->assertDontSee('Yesterday Special Onion');
    }

    public function test_date_navigation_controls(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::today()->subDay()->toDateString();
        $tomorrow = Carbon::today()->addDay()->toDateString();

        Livewire::test(Reports::class)
            ->assertSet('selectedDate', $today)
            ->call('goToPreviousDay')
            ->assertSet('selectedDate', $yesterday)
            ->call('goToNextDay')
            ->assertSet('selectedDate', $today)
            ->call('goToNextDay')
            ->assertSet('selectedDate', $tomorrow)
            ->call('goToToday')
            ->assertSet('selectedDate', $today);
    }

    public function test_assets_are_kept_separate_and_not_counted_as_operating_expenses(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->toDateString();

        // Operating expense: ৳1,000
        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Kitchen Oil',
            'category' => 'ingredients',
            'amount' => 1000,
            'expense_date' => $today,
        ]);

        // Asset investment: ৳50,000
        AssetLiability::create([
            'type' => 'asset',
            'name' => 'Food Cart Display Rack',
            'amount' => 50000,
            'record_date' => $today,
            'record_time' => '09:00:00',
        ]);

        Livewire::test(Reports::class)
            ->set('selectedDate', $today)
            ->assertViewHas('totalExpensesAmount', 1000.0) // Strictly 1,000
            ->assertViewHas('totalAssetsAdded', 50000.0);   // Strictly 50,000
    }

    public function test_report_displays_todays_total_sales_and_items_sold_metrics(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::today()->subDay()->toDateString();

        // Create today's sale with 3 items totaling 600
        $product = Product::create([
            'name' => 'Chicken Shawarma',
            'price' => 200,
            'cost_price' => 100,
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        $sale = new Sale([
            'invoice_no' => 'INV-TODAY-001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 600,
            'total_cost' => 300,
            'total_profit' => 300,
            'total_items_count' => 3,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
        $sale->timestamps = false;
        $sale->created_at = Carbon::today()->setHour(12)->setMinute(0);
        $sale->updated_at = Carbon::today()->setHour(12)->setMinute(0);
        $sale->save();

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => 'Chicken Shawarma',
            'quantity' => 3,
            'unit_price' => 200,
            'unit_cost' => 100,
            'subtotal' => 600,
            'profit' => 300,
        ]);

        // When viewing today:
        Livewire::test(Reports::class)
            ->set('selectedDate', $today)
            ->assertSee("Today's Total Sales")
            ->assertSee("Today's Items Sold")
            ->assertSee('600')
            ->assertSee('3')
            ->assertViewHas('todaySalesAmount', 600.0)
            ->assertViewHas('todayItemsSold', 3);

        // When viewing yesterday, today's live benchmark should still be present:
        Livewire::test(Reports::class)
            ->set('selectedDate', $yesterday)
            ->assertViewHas('todaySalesAmount', 600.0)
            ->assertViewHas('todayItemsSold', 3)
            ->assertSee('Today:')
            ->assertSee('items sold')
            ->assertSee('600');
    }

    public function test_report_displays_cash_bkash_and_nagad_counts(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->toDateString();

        // 1 Cash sale
        Sale::create([
            'invoice_no' => 'INV-CASH-001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 300,
            'total_cost' => 150,
            'total_profit' => 150,
            'total_items_count' => 1,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        // 2 bKash sales
        Sale::create([
            'invoice_no' => 'INV-BKASH-001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 400,
            'total_cost' => 200,
            'total_profit' => 200,
            'total_items_count' => 1,
            'payment_method' => 'bkash',
            'status' => 'completed',
        ]);
        Sale::create([
            'invoice_no' => 'INV-BKASH-002',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 450,
            'total_cost' => 220,
            'total_profit' => 230,
            'total_items_count' => 1,
            'payment_method' => 'bkash',
            'status' => 'completed',
        ]);

        // 1 Nagad sale
        Sale::create([
            'invoice_no' => 'INV-NAGAD-001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 250,
            'total_cost' => 100,
            'total_profit' => 150,
            'total_items_count' => 1,
            'payment_method' => 'nagad',
            'status' => 'completed',
        ]);

        Livewire::test(Reports::class)
            ->set('selectedDate', $today)
            ->assertViewHas('cashCount', 1)
            ->assertViewHas('cashAmount', 300.0)
            ->assertViewHas('bkashCount', 2)
            ->assertViewHas('bkashAmount', 850.0)
            ->assertViewHas('nagadCount', 1)
            ->assertViewHas('nagadAmount', 250.0)
            ->assertSee('Cash')
            ->assertSee('bKash')
            ->assertSee('Nagad')
            ->assertSee('850')
            ->assertSee('300')
            ->assertSee('250');
    }
}
