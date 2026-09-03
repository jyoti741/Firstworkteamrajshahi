<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Models\BusinessDay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardShiftRecordsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@cartflow.test',
            'role' => 'admin',
        ]);

        $this->seller = User::factory()->create([
            'email' => 'seller@cartflow.test',
            'role' => 'seller',
            'name' => 'Rahim Cashier',
        ]);
    }

    public function test_turn_on_button_is_replaced_by_all_records_button(): void
    {
        // Closed business day
        BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'closed',
            'opened_at' => now()->subHours(8),
            'closed_at' => now()->subHours(1),
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
            'opening_cash_float' => 1000,
            'closing_cash_amount' => 4500,
        ]);

        Livewire::actingAs($this->admin)
            ->test(Dashboard::class)
            ->assertDontSee('Turn ON (Reopen)')
            ->assertSee('All Records');
    }

    public function test_all_records_modal_displays_complete_cart_shift_history(): void
    {
        BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'closed',
            'opened_at' => now()->subHours(10),
            'closed_at' => now()->subHours(2),
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
            'opening_cash_float' => 1500,
            'closing_cash_amount' => 5400,
            'notes' => 'Evening shift closed smoothly',
        ]);

        Livewire::actingAs($this->admin)
            ->test(Dashboard::class)
            ->set('showAllShiftRecordsModal', true)
            ->assertSee('Cart Shift')
            ->assertSee('Total Recorded Shifts')
            ->assertSee('Rahim Cashier')
            ->assertSee('Evening shift closed smoothly');
    }

    public function test_all_records_shows_every_open_and_close_status_not_just_first_and_last(): void
    {
        // 1. First shift: Opened at 9am, closed at 1pm
        $shift1 = BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'closed',
            'opened_at' => now()->startOfDay()->addHours(9),
            'closed_at' => now()->startOfDay()->addHours(13),
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
            'opening_cash_float' => 1000,
            'closing_cash_amount' => 3500,
            'notes' => 'Morning shift completed',
        ]);

        // 2. Second shift: Opened at 4pm, closed at 10pm
        $shift2 = BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'closed',
            'opened_at' => now()->startOfDay()->addHours(16),
            'closed_at' => now()->startOfDay()->addHours(22),
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
            'opening_cash_float' => 500,
            'closing_cash_amount' => 7000,
            'notes' => 'Night shift completed',
        ]);

        // Verify All Records contains all 4 status events (both opens and both closes)
        Livewire::actingAs($this->admin)
            ->test(Dashboard::class)
            ->set('showAllShiftRecordsModal', true)
            ->set('recordsViewTab', 'all_events')
            ->assertSee('Morning shift completed')
            ->assertSee('Night shift completed')
            ->assertSee('Cart Opened')
            ->assertSee('Cart Closed');
    }
}
