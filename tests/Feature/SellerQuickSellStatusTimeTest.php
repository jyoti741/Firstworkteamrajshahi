<?php

namespace Tests\Feature;

use App\Livewire\Seller\QuickSell;
use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SellerQuickSellStatusTimeTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'role' => 'seller',
            'is_active' => true,
            'name' => 'Farzan Seller',
            'locale' => 'en',
        ]);

        CartSetting::set('cart_name', 'CartFlow Demo');
        CartSetting::set('currency_symbol', '৳');

        $category = Category::create([
            'name' => 'Snacks',
            'icon' => '🍔',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Burger',
            'price' => 150,
            'cost_price' => 90,
            'is_available' => true,
        ]);
    }

    public function test_quick_sell_displays_opening_time_when_open(): void
    {
        $openTime = Carbon::parse('2026-09-03 10:15:00');
        Carbon::setTestNow($openTime);

        BusinessDay::create([
            'date' => $openTime->toDateString(),
            'status' => 'open',
            'opened_at' => $openTime,
            'opened_by_id' => $this->seller->id,
        ]);

        Livewire::actingAs($this->seller)
            ->test(QuickSell::class)
            ->assertSee('Cart is OPEN')
            ->assertSee('Opened:')
            ->assertSee('10:15 AM');
    }

    public function test_quick_sell_displays_closing_time_when_closed(): void
    {
        $openTime = Carbon::parse('2026-09-03 09:00:00');
        $closeTime = Carbon::parse('2026-09-03 13:45:00');

        BusinessDay::create([
            'date' => $openTime->toDateString(),
            'status' => 'closed',
            'opened_at' => $openTime,
            'closed_at' => $closeTime,
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
        ]);

        Carbon::setTestNow($closeTime);

        Livewire::actingAs($this->seller)
            ->test(QuickSell::class)
            ->assertSee('Cart is CLOSED')
            ->assertSee('Closed:')
            ->assertSee('01:45 PM');
    }

    public function test_quick_sell_updates_time_dynamically_on_reopen(): void
    {
        // 1. Initial morning shift: closed at 1:00 PM
        $openTime1 = Carbon::parse('2026-09-03 09:00:00');
        $closeTime1 = Carbon::parse('2026-09-03 13:00:00');

        $day = BusinessDay::create([
            'date' => $openTime1->toDateString(),
            'status' => 'closed',
            'opened_at' => $openTime1,
            'closed_at' => $closeTime1,
            'opened_by_id' => $this->seller->id,
            'closed_by_id' => $this->seller->id,
        ]);

        // 2. Reopen at 4:30 PM
        $reopenTime = Carbon::parse('2026-09-03 16:30:00');
        Carbon::setTestNow($reopenTime);

        Livewire::actingAs($this->seller)
            ->test(QuickSell::class)
            ->call('openCart')
            ->assertSee('Cart is OPEN')
            ->assertSee('Opened:')
            ->assertSee('04:30 PM');
    }
}
