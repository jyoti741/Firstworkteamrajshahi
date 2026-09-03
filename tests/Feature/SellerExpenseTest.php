<?php

namespace Tests\Feature;

use App\Livewire\Seller\ExpenseManager;
use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class SellerExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $admin;
    protected BusinessDay $businessDay;

    protected function setUp(): void
    {
        parent::setUp();

        CartSetting::set('cart_name', 'CartFlow Kitchen');
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

        $this->businessDay = BusinessDay::openActiveOrNew($this->seller->id);
    }

    public function test_migration_adds_description_column_to_expenses_table(): void
    {
        $this->assertTrue(Schema::hasColumn('expenses', 'description'));
        $this->assertTrue(Schema::hasColumn('expenses', 'amount'));
    }

    public function test_seller_can_access_expenses_page(): void
    {
        $this->actingAs($this->seller);

        $response = $this->get(route('seller.expenses'));
        $response->assertStatus(200);
        $response->assertSee('Expenses');
        $response->assertSee("Today's Expenses");
        $response->assertSee('Add Expense');
    }

    public function test_seller_can_add_expense_with_description_and_amount(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(ExpenseManager::class)
            ->assertStatus(200)
            ->call('openAddModal')
            ->assertSet('showExpenseModal', true)
            ->set('description', 'Cooking Oil 5 Liters')
            ->set('amount', 850.50)
            ->call('saveExpense')
            ->assertHasNoErrors()
            ->assertSet('showExpenseModal', false);

        $this->assertDatabaseHas('expenses', [
            'user_id' => $this->seller->id,
            'description' => 'Cooking Oil 5 Liters',
            'title' => 'Cooking Oil 5 Liters',
            'amount' => 850.50,
        ]);

        $created = Expense::latest('id')->first();
        $this->assertTrue($created->expense_date->isToday());
    }

    public function test_validation_fails_for_missing_required_fields_and_invalid_amount(): void
    {
        $this->actingAs($this->seller);

        // Empty description & amount
        Livewire::test(ExpenseManager::class)
            ->set('description', '')
            ->set('amount', null)
            ->call('saveExpense')
            ->assertHasErrors(['description' => 'required', 'amount' => 'required']);

        // Description too short
        Livewire::test(ExpenseManager::class)
            ->set('description', 'A')
            ->set('amount', 100)
            ->call('saveExpense')
            ->assertHasErrors(['description' => 'min']);

        // Amount <= 0
        Livewire::test(ExpenseManager::class)
            ->set('description', 'Napkins')
            ->set('amount', 0)
            ->call('saveExpense')
            ->assertHasErrors(['amount' => 'min']);

        // Negative amount
        Livewire::test(ExpenseManager::class)
            ->set('description', 'Napkins')
            ->set('amount', -50)
            ->call('saveExpense')
            ->assertHasErrors(['amount' => 'min']);
    }

    public function test_seller_can_view_expenses_list(): void
    {
        $this->actingAs($this->seller);

        Expense::create([
            'user_id' => $this->seller->id,
            'business_day_id' => $this->businessDay->id,
            'description' => 'Burger Buns 40 Pack',
            'title' => 'Burger Buns 40 Pack',
            'amount' => 600,
            'expense_date' => Carbon::today()->toDateString(),
        ]);

        Livewire::test(ExpenseManager::class)
            ->assertStatus(200)
            ->assertSee('Burger Buns 40 Pack')
            ->assertSee('৳600');
    }

    public function test_seller_can_edit_an_expense(): void
    {
        $this->actingAs($this->seller);

        $expense = Expense::create([
            'user_id' => $this->seller->id,
            'business_day_id' => $this->businessDay->id,
            'description' => 'Mayonnaise Jar',
            'title' => 'Mayonnaise Jar',
            'amount' => 300,
            'expense_date' => Carbon::today()->toDateString(),
        ]);

        Livewire::test(ExpenseManager::class)
            ->call('editExpense', $expense->id)
            ->assertSet('editingExpenseId', $expense->id)
            ->assertSet('description', 'Mayonnaise Jar')
            ->assertSet('amount', 300.0)
            ->assertSet('showExpenseModal', true)
            ->set('description', 'Premium Mayonnaise 2 Jars')
            ->set('amount', 550)
            ->call('saveExpense')
            ->assertHasNoErrors()
            ->assertSet('showExpenseModal', false);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'description' => 'Premium Mayonnaise 2 Jars',
            'title' => 'Premium Mayonnaise 2 Jars',
            'amount' => 550,
        ]);
    }

    public function test_seller_can_delete_an_expense(): void
    {
        $this->actingAs($this->seller);

        $expense = Expense::create([
            'user_id' => $this->seller->id,
            'business_day_id' => $this->businessDay->id,
            'description' => 'Packaging Bags',
            'title' => 'Packaging Bags',
            'amount' => 250,
            'expense_date' => Carbon::today()->toDateString(),
        ]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);

        Livewire::test(ExpenseManager::class)
            ->call('deleteExpense', $expense->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_today_total_expenses_updates_dynamically(): void
    {
        $this->actingAs($this->seller);

        // Initially zero expenses today
        $component = Livewire::test(ExpenseManager::class)
            ->assertViewHas('todayTotal', 0.0)
            ->assertViewHas('todayCount', 0);

        // Add 1st expense: 400
        $component->call('openAddModal')
            ->set('description', 'Cheese Slices')
            ->set('amount', 400)
            ->call('saveExpense')
            ->assertViewHas('todayTotal', 400.0)
            ->assertViewHas('todayCount', 1);

        // Add 2nd expense: 350 -> total should be 750
        $component->call('openAddModal')
            ->set('description', 'Foil Wraps')
            ->set('amount', 350)
            ->call('saveExpense')
            ->assertViewHas('todayTotal', 750.0)
            ->assertViewHas('todayCount', 2);

        $lastExpense = Expense::latest('id')->first();

        // Edit 2nd expense from 350 to 500 -> total should be 900
        $component->call('editExpense', $lastExpense->id)
            ->set('amount', 500)
            ->call('saveExpense')
            ->assertViewHas('todayTotal', 900.0);

        // Delete 2nd expense -> total should go back to 400
        $component->call('deleteExpense', $lastExpense->id)
            ->assertViewHas('todayTotal', 400.0)
            ->assertViewHas('todayCount', 1);
    }

    public function test_bangla_localization_works_on_expenses_page(): void
    {
        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);
        app()->setLocale('bn');
        session(['seller_locale' => 'bn']);

        Expense::create([
            'user_id' => $this->seller->id,
            'business_day_id' => $this->businessDay->id,
            'description' => 'আলু এবং শাকসবজি',
            'title' => 'আলু এবং শাকসবজি',
            'amount' => 450,
            'expense_date' => Carbon::today()->toDateString(),
        ]);

        $response = $this->get(route('seller.expenses'));
        $response->assertStatus(200);
        $response->assertSee('খরচ');
        $response->assertSee('আজকের মোট খরচ');
        $response->assertSee('আলু এবং শাকসবজি');
        $response->assertSee('৳৪৫০');
    }
}
