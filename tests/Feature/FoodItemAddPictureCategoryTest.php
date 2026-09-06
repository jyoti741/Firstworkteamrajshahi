<?php

namespace Tests\Feature;

use App\Helpers\FoodImageHelper;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Seller\QuickSell;
use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FoodItemAddPictureCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        CartSetting::set('cart_name', 'CartFlow Food Cart');
        CartSetting::set('currency_symbol', '৳');

        $this->admin = User::factory()->create([
            'name' => 'Cart Owner',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Seller Rahim',
            'role' => 'seller',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Fast Food',
            'name_bn' => 'ফাস্ট ফুড',
            'icon' => '🍔',
        ]);
    }

    public function test_food_item_add_form_renders_picture_section_and_no_cost_price(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Chicken Burger',
            'price' => 180,
            'cost_price' => 110,
            'image_emoji' => '🍔',
        ]);

        Livewire::test(ProductManager::class)
            ->assertStatus(200)
            ->assertSee('Chicken Burger')
            // Cost price must be removed from item cards
            ->assertDontSee('(Cost: ৳')
            ->assertDontSee('Profit: +৳')
            // Open Add Modal
            ->call('openAddProductModal')
            ->assertSee('Item Picture')
            ->assertSee('Upload Picture')
            ->assertSee('Suggest Picture')
            ->assertSee('Item Name (English)')
            ->assertSee('Item Name (বাংলা)')
            ->assertSee('Selling Price')
            // Cost price must NOT be in the form
            ->assertDontSee('Cost Price');
    }

    public function test_automatic_picture_suggestion_for_bangla_and_english_foods(): void
    {
        $this->actingAs($this->admin);

        // 1. Fuchka / ফুচকা
        $fuchkaMatch = FoodImageHelper::matchImage('Fuchka', 'ফুচকা');
        $this->assertEquals('images/foods/fuchka.jpg', $fuchkaMatch['path']);

        // 2. Chotpoti / চটপটি
        $chotpotiMatch = FoodImageHelper::matchImage('Chotpoti', 'চটপটি');
        $this->assertEquals('images/foods/chotpoti.jpg', $chotpotiMatch['path']);

        // 3. Biryani / বিরিয়ানি
        $biryaniMatch = FoodImageHelper::matchImage('Kacchi Biryani', 'কাচ্চি বিরিয়ানি');
        $this->assertEquals('images/foods/biryani.jpg', $biryaniMatch['path']);

        // 4. Burger / বার্গার
        $burgerMatch = FoodImageHelper::matchImage('Beef Burger', 'বিফ বার্গার');
        $this->assertEquals('images/foods/burger.jpg', $burgerMatch['path']);

        // 5. Tea / চা
        $teaMatch = FoodImageHelper::matchImage('Special Milk Tea', 'স্পেশাল দুধ চা');
        $this->assertEquals('images/foods/tea.jpg', $teaMatch['path']);

        // Test in Livewire component via suggestPicture action
        Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->set('name', 'Special Fuchka')
            ->set('name_bn', 'স্পেশাল ফুচকা')
            ->call('suggestPicture')
            ->assertSet('image_path', 'images/foods/fuchka.jpg')
            ->assertSet('is_suggested', true);
    }

    public function test_can_upload_photo_for_food_item(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->image('custom_burger.jpg');

        Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->set('name', 'Artisan Smash Burger')
            ->set('name_bn', 'স্ম্যাশ বার্গার')
            ->set('price', 250)
            ->set('category_id', $this->category->id)
            ->set('photo', $file)
            ->call('saveProduct')
            ->assertStatus(200);

        $product = Product::where('name', 'Artisan Smash Burger')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
        $this->assertStringContainsString('products/', $product->image_path);
        $this->assertStringContainsString('storage/', $product->image_url);
    }

    public function test_category_plus_button_shows_inline_field_to_write_category_name_and_auto_selects(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->call('openInlineCategoryInput')
            ->assertSet('showNewCategoryInput', true)
            ->set('newCategoryName', 'স্ট্রিট ফুড')
            ->call('saveInlineCategory')
            ->assertSet('showNewCategoryInput', false);

        $newCategory = Category::where('name', 'স্ট্রিট ফুড')->first();
        $this->assertNotNull($newCategory);
        $this->assertEquals('স্ট্রিট ফুড', $newCategory->name_bn);

        // Verify the newly created category is automatically selected in the item form!
        $this->assertEquals($newCategory->id, $component->get('category_id'));
    }

    public function test_quick_sell_displays_food_picture_when_available(): void
    {
        BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'open',
            'opened_at' => now(),
            'opened_by_id' => $this->seller->id,
        ]);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Fuchka Platter',
            'name_bn' => 'ফুচকা প্ল্যাটার',
            'price' => 80,
            'cost_price' => 0,
            'image_path' => 'images/foods/fuchka.jpg',
            'image_emoji' => '🥣',
            'is_available' => true,
        ]);

        $this->actingAs($this->seller);

        Livewire::test(QuickSell::class)
            ->assertStatus(200)
            ->assertSee('images/foods/fuchka.jpg')
            ->assertSee('Fuchka Platter');
    }

    public function test_end_to_end_add_food_item_flow_and_view_in_quick_sell(): void
    {
        // 1. Admin visits products page via HTTP GET
        $response = $this->actingAs($this->admin)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Food Items');
        $response->assertDontSee('Cost Price');

        // 2. Open modal, create new category via inline field, suggest picture, and save product
        $component = Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->call('openInlineCategoryInput')
            ->set('newCategoryName', 'Street Food')
            ->call('saveInlineCategory')
            ->set('name', 'Dhaka Fuchka')
            ->set('name_bn', 'ঢাকা ফুচকা')
            ->set('price', 90)
            ->call('suggestPicture')
            ->assertSet('image_path', 'images/foods/fuchka.jpg')
            ->call('saveProduct')
            ->assertStatus(200);

        // Verify product was created with suggested image path and new category
        $this->assertDatabaseHas('products', [
            'name' => 'Dhaka Fuchka',
            'name_bn' => 'ঢাকা ফুচকা',
            'price' => 90,
            'image_path' => 'images/foods/fuchka.jpg',
        ]);

        // 3. Open business day and visit Quick Sell
        BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'open',
            'opened_at' => now(),
            'opened_by_id' => $this->seller->id,
        ]);

        $quickSellResponse = $this->actingAs($this->seller)->get('/seller/quick-sell');
        $quickSellResponse->assertStatus(200);
        $quickSellResponse->assertSee('images/foods/fuchka.jpg');
        $quickSellResponse->assertSee('Dhaka Fuchka');
    }

    public function test_category_selection_triggers_edit_and_delete_action_popup(): void
    {
        $this->actingAs($this->admin);

        // Before selecting, popup modal is false
        $component = Livewire::test(ProductManager::class)
            ->call('openAddProductModal')
            ->assertSet('showCategoryActionModal', false)
            ->assertSet('category_id', null);

        // Selecting category reveals popup modal
        $component->set('category_id', $this->category->id)
            ->assertSet('showCategoryActionModal', true)
            ->assertSet('actionCategoryId', $this->category->id)
            ->assertSee('Edit Category Name')
            ->assertSee('Delete Category')
            ->assertSee('Continue with this Category');

        // Closing popup keeps category selected
        $component->call('closeCategoryActionModal')
            ->assertSet('showCategoryActionModal', false)
            ->assertSet('category_id', $this->category->id);
    }

    public function test_owner_can_edit_category_name_and_auto_translate_to_bangla(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductManager::class)
            ->call('openEditCategoryModal', $this->category->id)
            ->assertSet('showEditCategoryModal', true)
            ->assertSet('editCategoryName', 'Fast Food')
            ->set('editCategoryName', 'Fuska')
            ->set('editCategoryNameBn', '')
            ->call('saveEditedCategory')
            ->assertSet('showEditCategoryModal', false);

        $this->category->refresh();
        $this->assertEquals('Fuska', $this->category->name);
        // Automatic translation for standard food words
        $this->assertEquals('ফুচকা', $this->category->name_bn);
    }

    public function test_owner_can_delete_category_without_deleting_products(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Burger Item',
            'price' => 150,
            'cost_price' => 0,
        ]);

        Livewire::test(ProductManager::class)
            ->call('openDeleteCategoryModal', $this->category->id)
            ->assertSet('showDeleteCategoryModal', true)
            ->assertSet('deletingCategoryId', $this->category->id)
            ->call('confirmDeleteCategory')
            ->assertSet('showDeleteCategoryModal', false);

        $this->assertDatabaseMissing('categories', ['id' => $this->category->id]);

        $product->refresh();
        $this->assertNull($product->category_id);
    }

    public function test_quick_sell_shows_category_in_bangla_when_seller_selects_bangla(): void
    {
        BusinessDay::create([
            'date' => now()->toDateString(),
            'status' => 'open',
            'opened_at' => now(),
            'opened_by_id' => $this->seller->id,
        ]);

        // Category saved only with English name 'Fuska'
        $fuskaCategory = Category::create([
            'name' => 'Fuska',
            'name_bn' => null,
            'icon' => '🥣',
        ]);

        Product::create([
            'category_id' => $fuskaCategory->id,
            'name' => 'Special Fuska',
            'name_bn' => 'স্পেশাল ফুচকা',
            'price' => 70,
            'cost_price' => 0,
            'is_available' => true,
        ]);

        $this->actingAs($this->seller);

        // 1. In English locale
        session(['seller_locale' => 'en']);
        Livewire::withQueryParams(['locale' => 'en'])
            ->test(QuickSell::class)
            ->assertSee('Fuska');

        // 2. In Bangla locale - category display name should be 'ফুচকা' even if name_bn was null!
        session(['seller_locale' => 'bn']);
        $this->seller->update(['locale' => 'bn']);

        Livewire::withQueryParams(['locale' => 'bn'])
            ->test(QuickSell::class)
            ->assertSee('ফুচকা');
    }
}
