<?php

namespace Tests\Feature;

use App\Livewire\Admin\ExpenseManager;
use App\Models\BusinessDay;
use App\Models\Expense;
use App\Models\ExpenseClosing;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseClosingTest extends TestCase
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

    public function test_admin_can_access_expense_page_with_close_and_calculate(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.expenses'));
        $response->assertStatus(200);
        $response->assertSee('Current Open Period');
        $response->assertSee('Close & Calculate', false);
        $response->assertSee('Closing History');
    }

    public function test_admin_can_add_expense_with_name_amount_date_and_time(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ExpenseManager::class)
            ->call('openAddModal')
            ->assertSet('showExpenseModal', true)
            ->set('title', '50 Burger Buns')
            ->set('amount', 750)
            ->set('category', 'ingredients')
            ->set('expense_date', '2026-09-06')
            ->set('expense_time', '10:30')
            ->set('notes', 'Bakery receipt')
            ->call('saveExpense')
            ->assertHasNoErrors()
            ->assertSet('showExpenseModal', false);

        $this->assertDatabaseHas('expenses', [
            'title' => '50 Burger Buns',
            'amount' => 750,
            'category' => 'ingredients',
        ]);

        $expense = Expense::where('title', '50 Burger Buns')->first();
        $this->assertNotNull($expense);
        $this->assertEquals('06 Sep 2026', $expense->formatted_date);
        $this->assertEquals('10:30 AM', $expense->formatted_time);
    }

    public function test_first_close_and_calculate_computes_all_prior_activity(): void
    {
        $this->actingAs($this->admin);

        // Sales: 10,000
        $sale = new Sale([
            'invoice_no' => 'INV-260906-0001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 10000,
            'total_cost' => 4000,
            'total_profit' => 6000,
            'total_items_count' => 10,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
        $sale->timestamps = false;
        $sale->created_at = Carbon::now()->subHours(3);
        $sale->updated_at = Carbon::now()->subHours(3);
        $sale->save();

        // Expenses: 4,000
        $expense = new Expense([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Beef Patties & Buns',
            'category' => 'ingredients',
            'amount' => 4000,
            'expense_date' => Carbon::today()->toDateString(),
            'expense_time' => '09:00:00',
        ]);
        $expense->timestamps = false;
        $expense->created_at = Carbon::now()->subHours(2);
        $expense->updated_at = Carbon::now()->subHours(2);
        $expense->save();

        Livewire::test(ExpenseManager::class)
            ->call('closeAndCalculate')
            ->assertSet('showClosingSummary', true)
            ->assertViewHas('runningSales', 0.0) // Fresh open period started
            ->assertViewHas('runningExpenses', 0.0);

        $closing = ExpenseClosing::first();
        $this->assertNotNull($closing);
        $this->assertEquals(10000.0, (float) $closing->total_sales);
        $this->assertEquals(4000.0, (float) $closing->total_expenses);
        $this->assertEquals(6000.0, (float) $closing->net_profit);
        $this->assertTrue($closing->is_profit);
    }

    public function test_second_close_and_calculate_calculates_only_activity_since_first_close(): void
    {
        $this->actingAs($this->admin);

        // Period 1: 10 Sep, 10:00 PM
        $firstCloseTime = Carbon::now()->subDays(5);
        $firstClosing = new ExpenseClosing([
            'user_id' => $this->admin->id,
            'period_start' => null,
            'closed_at' => $firstCloseTime,
            'total_sales' => 10000,
            'total_expenses' => 4000,
            'net_profit' => 6000,
            'sales_count' => 1,
            'expenses_count' => 1,
        ]);
        $firstClosing->timestamps = false;
        $firstClosing->created_at = $firstCloseTime;
        $firstClosing->updated_at = $firstCloseTime;
        $firstClosing->save();

        // Old Sale in Period 1 (prior to first close - should NOT be included in Period 2)
        $oldSale = new Sale([
            'invoice_no' => 'INV-OLD-0001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 10000,
            'status' => 'completed',
        ]);
        $oldSale->timestamps = false;
        $oldSale->created_at = $firstCloseTime->copy()->subHours(2);
        $oldSale->updated_at = $firstCloseTime->copy()->subHours(2);
        $oldSale->save();

        // Old Expense in Period 1 (prior to first close - should NOT be included in Period 2)
        $oldExpense = new Expense([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Old Period 1 Expense',
            'category' => 'ingredients',
            'amount' => 4000,
            'expense_date' => $firstCloseTime->toDateString(),
        ]);
        $oldExpense->timestamps = false;
        $oldExpense->created_at = $firstCloseTime->copy()->subHours(1);
        $oldExpense->updated_at = $firstCloseTime->copy()->subHours(1);
        $oldExpense->save();

        // NEW Sale in Period 2 (after first close) -> ৳8,000
        $newSale = new Sale([
            'invoice_no' => 'INV-NEW-0002',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 8000,
            'status' => 'completed',
        ]);
        $newSale->timestamps = false;
        $newSale->created_at = $firstCloseTime->copy()->addDays(2);
        $newSale->updated_at = $firstCloseTime->copy()->addDays(2);
        $newSale->save();

        // NEW Expense in Period 2 (after first close) -> ৳3,000
        $newExpense = new Expense([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Gas Cylinder Restock',
            'category' => 'gas',
            'amount' => 3000,
            'expense_date' => Carbon::today()->toDateString(),
        ]);
        $newExpense->timestamps = false;
        $newExpense->created_at = $firstCloseTime->copy()->addDays(3);
        $newExpense->updated_at = $firstCloseTime->copy()->addDays(3);
        $newExpense->save();

        // Trigger Second Close & Calculate
        Livewire::test(ExpenseManager::class)
            ->call('closeAndCalculate')
            ->assertSet('showClosingSummary', true);

        // Assert two closings exist
        $this->assertEquals(2, ExpenseClosing::count());

        $latestClosing = ExpenseClosing::latest('closed_at')->first();
        $this->assertEquals(8000.0, (float) $latestClosing->total_sales);
        $this->assertEquals(3000.0, (float) $latestClosing->total_expenses);
        $this->assertEquals(5000.0, (float) $latestClosing->net_profit);
        $this->assertTrue($latestClosing->is_profit);
    }

    public function test_closing_does_not_delete_or_reset_actual_sales_or_expenses(): void
    {
        $this->actingAs($this->admin);

        Sale::create([
            'invoice_no' => 'INV-PRESERVE-0001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 5000,
            'status' => 'completed',
            'created_at' => Carbon::now()->subHour(),
        ]);

        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Packaging Paper',
            'category' => 'packaging',
            'amount' => 1200,
            'expense_date' => Carbon::today()->toDateString(),
            'created_at' => Carbon::now()->subMinutes(30),
        ]);

        Livewire::test(ExpenseManager::class)->call('closeAndCalculate');

        // Sales and expenses must still exist in DB
        $this->assertDatabaseHas('sales', ['invoice_no' => 'INV-PRESERVE-0001']);
        $this->assertDatabaseHas('expenses', ['title' => 'Packaging Paper']);
    }

    public function test_loss_is_calculated_and_flagged_when_expenses_exceed_sales(): void
    {
        $this->actingAs($this->admin);

        // Sales: 2,000, Expenses: 5,000 => Loss: 3,000
        Sale::create([
            'invoice_no' => 'INV-LOSS-0001',
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'total_amount' => 2000,
            'status' => 'completed',
            'created_at' => Carbon::now()->subMinutes(20),
        ]);

        Expense::create([
            'user_id' => $this->admin->id,
            'business_day_id' => $this->businessDay->id,
            'title' => 'Staff Advance Salary',
            'category' => 'salaries',
            'amount' => 5000,
            'expense_date' => Carbon::today()->toDateString(),
            'created_at' => Carbon::now()->subMinutes(10),
        ]);

        Livewire::test(ExpenseManager::class)
            ->call('closeAndCalculate')
            ->assertSet('showClosingSummary', true);

        $closing = ExpenseClosing::first();
        $this->assertEquals(-3000.0, (float) $closing->net_profit);
        $this->assertFalse($closing->is_profit);
    }

    public function test_closing_history_displays_previous_calculations(): void
    {
        $this->actingAs($this->admin);

        ExpenseClosing::create([
            'user_id' => $this->admin->id,
            'closed_at' => Carbon::parse('2026-09-10 22:00:00'),
            'total_sales' => 10000,
            'total_expenses' => 4000,
            'net_profit' => 6000,
        ]);

        ExpenseClosing::create([
            'user_id' => $this->admin->id,
            'closed_at' => Carbon::parse('2026-09-15 21:00:00'),
            'total_sales' => 8000,
            'total_expenses' => 3000,
            'net_profit' => 5000,
        ]);

        Livewire::test(ExpenseManager::class)
            ->assertSee('10 Sep · 10:00 PM')
            ->assertSee('10,000')
            ->assertSee('4,000')
            ->assertSee('6,000')
            ->assertSee('15 Sep · 09:00 PM')
            ->assertSee('8,000')
            ->assertSee('3,000')
            ->assertSee('5,000');
    }
}
