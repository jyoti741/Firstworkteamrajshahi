<?php

namespace Tests\Feature;

use App\Helpers\BanglaHelper;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Seller\LanguageSwitcher;
use App\Livewire\Seller\QuickSell;
use App\Livewire\Seller\TodaySales;
use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SellerBanglaLanguageSwitchingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Category $category;
    protected Product $burger;
    protected Product $fries;

    protected function setUp(): void
    {
        parent::setUp();

        CartSetting::set('cart_name', 'CartFlow Burgers');
        CartSetting::set('currency_symbol', '৳');

        $this->admin = User::factory()->create([
            'name' => 'Owner Farhan',
            'role' => 'admin',
            'is_active' => true,
            'locale' => 'en',
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Rahim Cashier',
            'role' => 'seller',
            'is_active' => true,
            'locale' => 'en',
        ]);

        $this->category = Category::create([
            'name' => 'Burgers',
            'name_bn' => 'বার্গার',
            'icon' => '🍔',
            'sort_order' => 1,
        ]);

        $this->burger = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Classic Burger',
            'name_bn' => 'ক্লাসিক বার্গার',
            'price' => 150,
            'cost_price' => 90,
            'image_emoji' => '🍔',
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        $this->fries = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Crispy Fries',
            'name_bn' => 'ক্রিস্পি ফ্রাইস',
            'price' => 80,
            'cost_price' => 40,
            'image_emoji' => '🍟',
            'current_stock' => 50,
            'track_inventory' => true,
            'is_available' => true,
        ]);

        $day = BusinessDay::openActiveOrNew($this->seller->id);
        $sale = Sale::create([
            'invoice_no' => 'INV-TEST-001',
            'user_id' => $this->seller->id,
            'business_day_id' => $day->id,
            'total_amount' => 150,
            'total_cost' => 90,
            'total_profit' => 60,
            'total_items_count' => 1,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->burger->id,
            'product_name' => $this->burger->name,
            'unit_price' => 150,
            'unit_cost' => 90,
            'quantity' => 1,
            'subtotal' => 150,
            'profit' => 60,
        ]);
    }

    public function test_bangla_helper_converts_numerals_and_currencies_correctly(): void
    {
        // Numbers
        $this->assertEquals('১৫০', BanglaHelper::toBanglaNumeral('150'));
        $this->assertEquals('১২', BanglaHelper::toBanglaNumeral(12));
        $this->assertEquals('১,৮০০', BanglaHelper::toBanglaNumeral('1,800'));
        $this->assertEquals('০', BanglaHelper::toBanglaNumeral(0));

        // Format Number helper
        $this->assertEquals('১৫০', BanglaHelper::formatNumber(150, 'bn'));
        $this->assertEquals('150', BanglaHelper::formatNumber(150, 'en'));
        $this->assertEquals('১,৮০০', BanglaHelper::formatNumber(1800, 'bn'));
        $this->assertEquals('1,800', BanglaHelper::formatNumber(1800, 'en'));

        // Currency format helper
        $this->assertEquals('৳১৫০', BanglaHelper::formatCurrency(150, 'bn'));
        $this->assertEquals('৳150', BanglaHelper::formatCurrency(150, 'en'));
        $this->assertEquals('৳১,৮০০', BanglaHelper::formatCurrency(1800, 'bn'));
        $this->assertEquals('৳1,800', BanglaHelper::formatCurrency(1800, 'en'));
    }

    public function test_product_and_category_displayName_returns_stored_bangla_and_english(): void
    {
        // When Bangla
        $this->assertEquals('ক্লাসিক বার্গার', $this->burger->displayName('bn'));
        $this->assertEquals('বার্গার', $this->category->displayName('bn'));

        // When English
        $this->assertEquals('Classic Burger', $this->burger->displayName('en'));
        $this->assertEquals('Burgers', $this->category->displayName('en'));

        // Fallback when name_bn is null
        $itemWithoutBn = Product::create([
            'name' => 'Special Sauce',
            'name_bn' => null,
            'price' => 20,
            'cost_price' => 10,
        ]);
        $this->assertEquals('Special Sauce', $itemWithoutBn->displayName('bn'));
    }

    public function test_seller_interface_defaults_to_english_and_displays_english_labels_and_numerals(): void
    {
        $this->actingAs($this->seller);
        app()->setLocale('en');

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('Today\'s Sales')
            ->assertSee('Items Sold')
            ->assertSee('Classic Burger')
            ->assertSee('+')
            ->assertSee('SELL')
            ->assertSee('Cash')
            ->assertSee('bKash')
            ->assertSee('Nagad')
            ->assertSee('৳150');
    }

    public function test_seller_can_switch_language_to_bangla_and_persists_in_user_and_session(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(LanguageSwitcher::class)
            ->assertStatus(200)
            ->call('switchLanguage', 'bn')
            ->assertDispatched('seller-locale-changed', locale: 'bn');

        $this->assertEquals('bn', $this->seller->fresh()->locale);
        $this->assertEquals('bn', session('seller_locale'));
    }

    public function test_when_bangla_selected_seller_quick_sell_displays_bangla_interface_numerals_and_food_names(): void
    {
        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);
        app()->setLocale('bn');
        session(['seller_locale' => 'bn']);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            // Stored Bangla Food Name
            ->assertSee('ক্লাসিক বার্গার')
            // Bangla Numerals and Currency
            ->assertSee('৳১৫০')
            // Bangla Buttons and Labels
            ->assertSee('বিক্রি')
            ->assertSee('আজকের বিক্রি')
            ->assertSee('মোট বিক্রিত আইটেম')
            ->assertSee('আজকের বিক্রি দেখুন')
            ->assertSee('ক্যাশ')
            ->assertSee('বিকাশ')
            ->assertSee('নগদ')
            // 1-tap sale in Bangla
            ->call('recordSale', $this->burger->id, 1)
            ->assertStatus(200)
            ->assertSee('বিক্রি রেকর্ড করা হয়েছে');
    }

    public function test_when_cart_is_closed_in_bangla_mode_it_shows_cart_chalu_korun_only(): void
    {
        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);
        app()->setLocale('bn');
        session(['seller_locale' => 'bn']);

        BusinessDay::query()->update(['status' => 'closed', 'closed_at' => now(), 'closed_by_id' => $this->seller->id]);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('কার্ট চালু করুন')
            ->assertDontSee('চালু করুন (কার্ট খুলুন)');
    }

    public function test_when_bangla_selected_today_sales_records_displays_bangla_text_and_numerals(): void
    {
        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);
        app()->setLocale('bn');
        session(['seller_locale' => 'bn']);

        Livewire::test(TodaySales::class)
            ->assertStatus(200)
            ->assertSee('আজকের বিক্রির তালিকা')
            ->assertSee('দ্রুত বিক্রি-তে ফিরুন')
            ->assertSee('আজকের বিক্রি')
            ->assertSee('মোট বিক্রিত আইটেম')
            ->assertSee('ক্যাশ')
            ->assertSee('বিকাশ')
            ->assertSee('নগদ')
            ->assertSee('ক্লাসিক বার্গার')
            ->assertSee('৳১৫০');
    }

    public function test_seller_language_middleware_persists_language_across_routes(): void
    {
        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);

        $response = $this->get(route('seller.quick-sell'));
        $response->assertStatus(200);
        $this->assertEquals('bn', app()->getLocale());
        $response->assertSee('বাংলা');

        $response2 = $this->get(route('seller.today-sales'));
        $response2->assertStatus(200);
        $this->assertEquals('bn', app()->getLocale());
    }

    public function test_admin_can_save_and_update_food_item_bangla_name(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductManager::class)
            ->assertStatus(200)
            ->set('name', 'Naga Blast Roll')
            ->set('name_bn', 'নাগা ব্লাস্ট রোল')
            ->set('price', 140)
            ->set('cost_price', 80)
            ->set('image_emoji', '🌯')
            ->call('saveProduct')
            ->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'name' => 'Naga Blast Roll',
            'name_bn' => 'নাগা ব্লাস্ট রোল',
            'price' => 140,
        ]);
    }

    public function test_language_switcher_is_rendered_inside_hamburger_menu_drawer(): void
    {
        $this->actingAs($this->seller);

        $response = $this->get(route('seller.quick-sell'));
        $response->assertStatus(200);

        // Language switcher options are in the page
        $response->assertSee('wire:click="switchLanguage(\'bn\')"', false);
        $response->assertSee('wire:click="switchLanguage(\'en\')"', false);
        $response->assertSee('বাংলা');
        $response->assertSee('English');

        // Confirm it is placed inside the drawer panel
        $content = $response->getContent();
        $this->assertStringContainsString('sellerMenuOpen', $content);
        $drawerPart = explode('<!-- Seller Hamburger Slideover Menu Drawer -->', $content)[1] ?? '';
        $drawerPanel = explode('<!-- Alert / Toast Notification Area -->', $drawerPart)[0] ?? '';
        $this->assertStringContainsString('switchLanguage', $drawerPanel);
    }

    public function test_category_names_convert_to_bangla_in_quick_sell_when_seller_selects_bangla(): void
    {
        Category::create([
            'name' => 'Drinks',
            'name_bn' => 'ড্রিংকস',
            'icon' => '🥤',
            'sort_order' => 2,
        ]);

        $this->seller->update(['locale' => 'bn']);
        $this->actingAs($this->seller);
        session(['seller_locale' => 'bn']);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('বার্গার')
            ->assertSee('ড্রিংকস');
    }
}
