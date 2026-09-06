<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_to_their_role_dashboard(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $this->actingAs($seller);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('seller.quick-sell'));

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_dashboard_does_not_display_expenses_and_profit_options(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Core dashboard performance sections must show Sales, Items Sold, Orders
        $response->assertSee("Today's Sales", false);
        $response->assertSee("Items Sold");
        $response->assertSee("Orders");
        $response->assertSee("Assets & Liabilities", false);

        // The dashboard body must NOT show operational profit/loss calculations
        $response->assertDontSee("You're making a profit today");
        $response->assertDontSee("Loss Today");
        $response->assertDontSee("Month Profit");
        $response->assertDontSee("Month Expenses");
    }
}
