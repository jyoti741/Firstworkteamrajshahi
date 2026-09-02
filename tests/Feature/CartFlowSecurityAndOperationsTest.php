<?php

namespace Tests\Feature;

use App\Livewire\Seller\QuickSell;
use App\Models\BusinessDay;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartFlowSecurityAndOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Product $burger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Owner',
            'email' => 'admin@cartflow.test',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Seller Cashier',
            'email' => 'seller@cartflow.test',
            'role' => 'seller',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Burgers',
            'icon' => '🍔',
            'is_active' => true,
        ]);

        $this->burger = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Burger',
            'price' => 150,
            'cost_price' => 90,
            'image_emoji' => '🍔',
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        BusinessDay::openActiveOrNew($this->seller->id);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));

        $response = $this->get('/seller/quick-sell');
        $response->assertRedirect(route('login'));
    }

    public function test_seller_is_strictly_forbidden_from_admin_dashboard(): void
    {
        $response = $this->actingAs($this->seller)->get('/admin/dashboard');
        $response->assertRedirect(route('seller.quick-sell'));

        $response = $this->actingAs($this->seller)->get('/admin/products');
        $response->assertRedirect(route('seller.quick-sell'));

        $response = $this->actingAs($this->seller)->get('/admin/reports');
        $response->assertRedirect(route('seller.quick-sell'));

        $response = $this->actingAs($this->seller)->get('/admin/settings');
        $response->assertRedirect(route('seller.quick-sell'));
    }

    public function test_seller_json_request_to_admin_returns_403(): void
    {
        $response = $this->actingAs($this->seller)->getJson('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_seller_can_access_seller_panel(): void
    {
        $response = $this->actingAs($this->seller)->get('/seller/quick-sell');
        $response->assertStatus(200);

        $response = $this->actingAs($this->seller)->get('/seller/today-sales');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/sales');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/expenses');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/products');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/inventory');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/reports');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/sellers');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(200);
    }

    public function test_seller_can_record_one_tap_sale(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(QuickSell::class)
            ->call('recordSale', $this->burger->id, 1)
            ->assertSee('Test Burger');

        $this->assertDatabaseHas('sales', [
            'user_id' => $this->seller->id,
            'total_amount' => 150.00,
            'total_cost' => 90.00,
            'total_profit' => 60.00,
            'total_items_count' => 1,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_name' => 'Test Burger',
            'unit_price' => 150.00,
            'quantity' => 1,
            'subtotal' => 150.00,
        ]);

        // Inventory was decremented from 50 to 49
        $this->assertEquals(49, $this->burger->fresh()->current_stock);
    }

    public function test_seller_can_correct_sale(): void
    {
        $this->actingAs($this->seller);

        $component = Livewire::test(QuickSell::class);

        // Record 1 sale
        $component->call('recordSale', $this->burger->id, 1);
        $this->assertEquals(1, Sale::where('status', 'completed')->count());

        // Correct 1 sale
        $component->call('recordCorrection', $this->burger->id);
        $this->assertEquals(0, Sale::where('status', 'completed')->count());

        // Inventory restored to 50
        $this->assertEquals(50, $this->burger->fresh()->current_stock);
    }
}
