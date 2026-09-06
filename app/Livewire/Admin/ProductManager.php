<?php

namespace App\Livewire\Admin;

use App\Helpers\FoodImageHelper;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Food Items & Pricing Management')]
class ProductManager extends Component
{
    use WithFileUploads;

    public string $activeTab = 'products'; // 'products', 'categories'
    public string $categoryFilter = 'all';
    public string $search = '';

    // Product Modal state
    public bool $showProductModal = false;
    public ?int $editingProductId = null;
    public string $name = '';
    public string $name_bn = '';
    public ?int $category_id = null;
    public string $description = '';
    public ?float $price = null;
<<<<<<< HEAD
    public ?float $cost_price = 0.0; // Kept as internal property for backward DB/test compatibility
    public string $image_emoji = '🍔';
    public $photo = null;
    public ?string $image_path = null;
    public ?string $suggested_image_title = null;
    public bool $is_suggested = false;
    public bool $showPresetGallery = false;
=======
    public ?float $cost_price = 0.0;
    public string $image_emoji = '🍽️';
>>>>>>> jyoti2nd
    public int $current_stock = 0;
    public bool $track_inventory = true;
    public bool $is_available = true;
    public int $sort_order = 0;
    public bool $manualEmojiSet = false;
    public bool $showEmojiPicker = false;

    // Inline Category creation in Add/Edit form
    public bool $showNewCategoryInput = false;
    public string $newCategoryName = '';

    // Category Modal state (inline from + button or categories tab)
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryNameBn = '';
    public string $categoryIcon = '🍔';
    public int $categorySortOrder = 0;

    // Category Selection Action Popup State (Pop up shown ONLY when category is selected)
    public bool $showCategoryActionModal = false;
    public ?int $actionCategoryId = null;

    // Edit Category Modal
    public bool $showEditCategoryModal = false;
    public string $editCategoryName = '';
    public string $editCategoryNameBn = '';

    // Delete Category Modal
    public bool $showDeleteCategoryModal = false;
    public ?int $deletingCategoryId = null;
    public ?string $deletingCategoryName = null;

    // Inline Category state for food item modal
    public bool $showInlineCategoryInput = false;
    public ?int $inlineCategoryEditingId = null;
    public string $inlineCategoryName = '';
    public string $inlineCategoryNameBn = '';
    public string $inlineCategoryIcon = '🍔';

    public function openAddProductModal(): void
    {
        $this->reset([
            'editingProductId',
            'name',
            'name_bn',
            'description',
            'price',
            'cost_price',
            'photo',
            'image_path',
            'suggested_image_title',
            'is_suggested',
            'showPresetGallery',
            'showNewCategoryInput',
            'newCategoryName',
            'current_stock',
            'sort_order',
            'showCategoryActionModal',
            'actionCategoryId',
            'showEditCategoryModal',
            'showDeleteCategoryModal',
        ]);
        $this->manualEmojiSet = false;
        $this->showEmojiPicker = false;
        $this->category_id = Category::first()?->id;
        $this->image_emoji = $this->detectEmoji();
        $this->track_inventory = true;
        $this->is_available = true;
        $this->showProductModal = true;
        $this->cancelInlineCategory();
    }

    public function updatedCategoryId($val): void
    {
        $valInt = $val ? (int) $val : null;
        if ($valInt && Category::where('id', $valInt)->exists()) {
            $this->actionCategoryId = $valInt;
            $this->showCategoryActionModal = true;
        } else {
            $this->showCategoryActionModal = false;
            $this->actionCategoryId = null;
        }
    }

    public function selectCategoryAction(?int $id): void
    {
        if ($id && Category::where('id', $id)->exists()) {
            $this->category_id = $id;
            $this->actionCategoryId = $id;
            $this->showCategoryActionModal = true;
        }
    }

    public function closeCategoryActionModal(): void
    {
        $this->showCategoryActionModal = false;
    }

    public function openEditCategoryModal(?int $id = null): void
    {
        $catId = $id ?: $this->actionCategoryId ?: $this->category_id;
        if (!$catId) return;

        $category = Category::findOrFail($catId);
        $this->editingCategoryId = $category->id;
        $this->editCategoryName = $category->name;
        $this->editCategoryNameBn = $category->name_bn ?? '';
        $this->showEditCategoryModal = true;
        $this->showCategoryActionModal = false;
    }

    public function saveEditedCategory(): void
    {
        $this->validate([
            'editCategoryName' => 'required|string|max:100',
            'editCategoryNameBn' => 'nullable|string|max:100',
        ]);

        $category = Category::findOrFail($this->editingCategoryId);

        $name = trim($this->editCategoryName);
        $isBangla = (bool) preg_match('/[\x{0980}-\x{09FF}]/u', $name);
        $nameBn = $this->editCategoryNameBn ?: ($isBangla ? $name : Category::translateToBangla($name));

        $category->update([
            'name' => $name,
            'name_bn' => $nameBn,
        ]);

        $this->category_id = $category->id;
        $this->showEditCategoryModal = false;
        session()->flash('success', "Category '{$category->name}' updated.");
    }

    public function openDeleteCategoryModal(?int $id = null): void
    {
        $catId = $id ?: $this->actionCategoryId ?: $this->category_id;
        if (!$catId) return;

        $category = Category::findOrFail($catId);
        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->showDeleteCategoryModal = true;
        $this->showCategoryActionModal = false;
    }

    public function confirmDeleteCategory(): void
    {
        if ($this->deletingCategoryId) {
            $category = Category::find($this->deletingCategoryId);
            if ($category) {
                $name = $category->name;
                $category->products()->update(['category_id' => null]);
                $category->delete();

                if ($this->category_id === $this->deletingCategoryId) {
                    $this->category_id = null;
                }
                if ((string) $this->categoryFilter === (string) $this->deletingCategoryId) {
                    $this->categoryFilter = 'all';
                }
                session()->flash('success', "Category '{$name}' deleted.");
            }
        }

        $this->showDeleteCategoryModal = false;
        $this->deletingCategoryId = null;
        $this->deletingCategoryName = null;
        $this->showCategoryActionModal = false;
    }

    public function editProduct(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editingProductId = $product->id;
        $this->name = $product->name;
        $this->name_bn = $product->name_bn ?? '';
        $this->category_id = $product->category_id;
        $this->description = $product->description ?? '';
        $this->price = (float) $product->price;
        $this->cost_price = (float) $product->cost_price;
        $this->image_emoji = $product->image_emoji ?: '🍽️';
        $this->manualEmojiSet = true;
        $this->showEmojiPicker = false;
        $this->image_path = $product->image_path;
        $this->photo = null;
        $this->showPresetGallery = false;
        $this->showNewCategoryInput = false;
        $this->newCategoryName = '';
        $this->is_suggested = $product->image_path && str_starts_with($product->image_path, 'images/');
        $this->suggested_image_title = null;

        if ($this->is_suggested) {
            foreach (FoodImageHelper::$availableImages as $img) {
                if ($img['path'] === $product->image_path) {
                    $this->suggested_image_title = $img['name'];
                    break;
                }
            }
        }
        $this->current_stock = $product->current_stock;
        $this->track_inventory = $product->track_inventory;
        $this->is_available = $product->is_available;
        $this->sort_order = $product->sort_order;
        $this->showProductModal = true;
        $this->cancelInlineCategory();
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'image|max:5120', // 5MB max
        ]);
        $this->is_suggested = false;
        $this->suggested_image_title = null;
    }

    public function suggestPicture(): void
    {
        $match = FoodImageHelper::matchImage($this->name, $this->name_bn, $this->category_id);
        $this->image_path = $match['path'];
        $this->image_emoji = $match['emoji'] ?? '🍔';
        $this->suggested_image_title = $match['name'] ?? null;
        $this->photo = null;
        $this->is_suggested = true;
    }

    public function selectSuggestedPicture(string $path, string $emoji, string $name): void
    {
        $this->image_path = $path;
        $this->image_emoji = $emoji;
        $this->suggested_image_title = $name;
        $this->photo = null;
        $this->is_suggested = true;
        $this->showPresetGallery = false;
    }

    public function removePicture(): void
    {
        $this->photo = null;
        $this->image_path = null;
        $this->suggested_image_title = null;
        $this->is_suggested = false;
    }

    public function togglePresetGallery(): void
    {
        $this->showPresetGallery = !$this->showPresetGallery;
    }

    public function saveProduct(): void
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image_emoji' => 'nullable|string|max:10',
            'category_id' => 'nullable|exists:categories,id',
            'current_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:5120',
        ]);

        $finalImagePath = $this->image_path;
        if ($this->photo) {
            $finalImagePath = $this->photo->store('products', 'public');
        }

        $defaultCatId = $this->category_id ?: Category::first()?->id ?: Category::create(['name' => 'General', 'name_bn' => 'সাধারণ', 'icon' => '🍔'])->id;

        $data = [
            'name' => $this->name,
            'name_bn' => $this->name_bn ?: null,
            'category_id' => $defaultCatId,
            'description' => $this->description ?? '',
            'price' => $this->price,
            'cost_price' => $this->cost_price ?? 0,
            'image_emoji' => $this->image_emoji ?: '🍽️',
            'image_path' => $finalImagePath,
            'current_stock' => $this->current_stock ?? 50,
            'track_inventory' => $this->track_inventory,
            'is_available' => $this->is_available,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingProductId) {
            Product::findOrFail($this->editingProductId)->update($data);
            session()->flash('success', "Food item '{$this->name}' updated.");
        } else {
            Product::create($data);
            session()->flash('success', "New item '{$this->name}' added to menu.");
        }

        $this->showProductModal = false;
    }

    public function toggleAvailability(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->is_available = !$product->is_available;
        $product->save();
    }

    public function deleteProduct(int $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Food item removed.');
    }

    // Inline Category Methods for Add/Edit Food Item Form
    public function openInlineCategoryInput(): void
    {
        $this->newCategoryName = '';
        $this->showNewCategoryInput = true;
    }

    public function closeInlineCategoryInput(): void
    {
        $this->newCategoryName = '';
        $this->showNewCategoryInput = false;
    }

    public function saveInlineCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:100',
        ]);

        $name = trim($this->newCategoryName);
        $isBangla = (bool) preg_match('/[\x{0980}-\x{09FF}]/u', $name);

        $cat = Category::create([
            'name' => $name,
            'name_bn' => $isBangla ? $name : null,
            'icon' => '🍲',
            'sort_order' => (Category::max('sort_order') ?? 0) + 1,
        ]);

        $this->category_id = $cat->id;
        $this->newCategoryName = '';
        $this->showNewCategoryInput = false;
        session()->flash('success', "Category '{$cat->name}' created and selected.");
    }

    // Category Methods
    public function openAddCategoryModal(): void
    {
        $this->reset(['editingCategoryId', 'categoryName', 'categoryNameBn', 'categorySortOrder']);
        $this->categoryIcon = '🍔';
        $this->showCategoryModal = true;
    }

    public function quickFillCategory(string $en, string $bn, string $icon = '🍔'): void
    {
        $this->categoryName = $en;
        $this->categoryNameBn = $bn;
        $this->categoryIcon = $icon;
    }

    public function editCategory(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryNameBn = $category->name_bn ?? '';
        $this->categoryIcon = $category->icon;
        $this->categorySortOrder = $category->sort_order;
        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:100',
            'categoryNameBn' => 'nullable|string|max:100',
            'categoryIcon' => 'nullable|string|max:10',
            'categorySortOrder' => 'integer',
        ]);

        $isBangla = (bool) preg_match('/[\x{0980}-\x{09FF}]/u', $this->categoryName);
        $nameBn = $this->categoryNameBn ?: ($isBangla ? $this->categoryName : null);

        $data = [
            'name' => $this->categoryName,
            'name_bn' => $nameBn,
            'icon' => $this->categoryIcon ?: '🍔',
            'sort_order' => $this->categorySortOrder,
        ];

        if ($this->editingCategoryId) {
            $cat = Category::findOrFail($this->editingCategoryId);
            $cat->update($data);
            session()->flash('success', "Category '{$cat->name}' updated.");
        } else {
            $cat = Category::create($data);
            $this->category_id = $cat->id; // Automatically select the newly created category!
            session()->flash('success', "Category '{$cat->name}' created and selected.");
        }

        $this->showCategoryModal = false;
    }

    public function deleteCategory(int $id): void
    {
        Category::findOrFail($id)->delete();
        if ((string) $this->categoryFilter === (string) $id) {
            $this->categoryFilter = 'all';
        }
        if ($this->category_id === $id) {
            $this->category_id = Category::first()?->id;
        }
        session()->flash('success', 'Category deleted.');
    }

    // Inline Category Methods for Food Item Modal
    public function toggleInlineCategoryAdd(): void
    {
        if ($this->showInlineCategoryInput && !$this->inlineCategoryEditingId) {
            $this->showInlineCategoryInput = false;
        } else {
            $this->inlineCategoryEditingId = null;
            $this->inlineCategoryName = '';
            $this->inlineCategoryNameBn = '';
            $this->inlineCategoryIcon = '🍔';
            $this->showInlineCategoryInput = true;
        }
    }

    public function startInlineCategoryEdit(): void
    {
        if ($this->category_id) {
            $category = Category::find($this->category_id);
            if ($category) {
                $this->inlineCategoryEditingId = $category->id;
                $this->inlineCategoryName = $category->name;
                $this->inlineCategoryNameBn = $category->name_bn ?? '';
                $this->inlineCategoryIcon = $category->icon ?: '🍔';
                $this->showInlineCategoryInput = true;
            }
        }
    }

    public function cancelInlineCategory(): void
    {
        $this->showInlineCategoryInput = false;
        $this->inlineCategoryEditingId = null;
        $this->inlineCategoryName = '';
        $this->inlineCategoryNameBn = '';
    }

    public function saveInlineCategory(): void
    {
        $this->validate([
            'inlineCategoryName' => 'required|string|max:100',
            'inlineCategoryNameBn' => 'nullable|string|max:100',
            'inlineCategoryIcon' => 'nullable|string|max:10',
        ]);

        $data = [
            'name' => $this->inlineCategoryName,
            'name_bn' => $this->inlineCategoryNameBn ?: null,
            'icon' => $this->inlineCategoryIcon ?: '🍔',
        ];

        if ($this->inlineCategoryEditingId) {
            $cat = Category::findOrFail($this->inlineCategoryEditingId);
            $cat->update($data);
            session()->flash('success', "Category '{$cat->name}' updated.");
        } else {
            $maxOrder = (int) Category::max('sort_order');
            $data['sort_order'] = $maxOrder + 1;
            $cat = Category::create($data);
            $this->category_id = $cat->id;
            session()->flash('success', "Category '{$cat->name}' created.");
        }

        $this->cancelInlineCategory();
    }

    public function deleteSelectedCategory(): void
    {
        if ($this->category_id) {
            $cat = Category::find($this->category_id);
            if ($cat) {
                $catId = $cat->id;
                $catName = $cat->name;
                $cat->delete();
                $this->category_id = Category::first()?->id;
                if ((string) $this->categoryFilter === (string) $catId) {
                    $this->categoryFilter = 'all';
                }
                session()->flash('success', "Category '{$catName}' deleted.");
            }
        }
        $this->cancelInlineCategory();
    }

    // Emoji Suggestion & Custom Selection
    public function toggleEmojiPicker(): void
    {
        $this->showEmojiPicker = ! $this->showEmojiPicker;
    }

    public function selectEmoji(string $emoji): void
    {
        $this->image_emoji = $emoji;
        $this->manualEmojiSet = true;
    }

    public function resetToAutoEmoji(): void
    {
        $this->manualEmojiSet = false;
        $this->image_emoji = $this->detectEmoji();
    }

    public function updatedName(): void
    {
        if (! $this->manualEmojiSet) {
            $this->image_emoji = $this->detectEmoji();
        }
    }

    public function updatedNameBn(): void
    {
        if (! $this->manualEmojiSet) {
            $this->image_emoji = $this->detectEmoji();
        }
    }

    public function updatedImageEmoji(): void
    {
        if (trim($this->image_emoji) !== '') {
            $this->manualEmojiSet = true;
        }
    }

    public function detectEmoji(): string
    {
        $text = mb_strtolower(trim($this->name . ' ' . $this->name_bn));
        if ($text === '') {
            return '🍽️';
        }

        // Ordered mapping of keywords to emoji
        $map = [
            // Fuchka & Chotpoti
            ['emoji' => '🥣', 'keywords' => [
                'fuchka', 'phuchka', 'puchka', 'fuska', 'phuska', 'ফুচকা', 'ফূচকা', 'ফুসকা',
                'chotpoti', 'chatpati', 'chot poti', 'চটপটি', 'চটপটী',
                'pani puri', 'panipuri', 'golgappa',
            ]],
            // Singara & Samosa & Momos
            ['emoji' => '🥟', 'keywords' => [
                'singara', 'shingara', 'singada', 'shingada', 'সিঙ্গারা', 'সিংগাড়া', 'সিংগাড়া',
                'samosa', 'samusa', 'somosa', 'somucha', 'সমুচা', 'সামুচা', 'সমুসা',
                'momo', 'momos', 'মোমো', 'dumpling', 'ডাম্পলিং',
            ]],
            // Pizza
            ['emoji' => '🍕', 'keywords' => [
                'pizza', 'pizzas', 'pizaa', 'pizah', 'পিজ্জা', 'পিৎজা', 'পিজা',
            ]],
            // Burger
            ['emoji' => '🍔', 'keywords' => [
                'burger', 'burgers', 'bargar', 'burgur', 'বার্গার', 'hamburger', 'cheeseburger', 'beef burger', 'chicken burger', 'patty',
            ]],
            // Ice Cream
            ['emoji' => '🍦', 'keywords' => [
                'ice cream', 'icecream', 'ice-cream', 'আইসক্রিম', 'আইস ক্রিম', 'kulfi', 'কুলফি', 'sundae',
            ]],
            // Cake & Pastry
            ['emoji' => '🍰', 'keywords' => [
                'cake', 'caek', 'কেক', 'pastry', 'পেস্ট্রি', 'brownie', 'ব্রাউনি', 'cupcake',
            ]],
            // Tea & Coffee
            ['emoji' => '☕', 'keywords' => [
                'coffee', 'coffe', 'cawfee', 'কফি', 'espresso', 'cappuccino', 'latte', 'cold coffee',
                'tea', 'chai', 'cha', 'চা', 'দুধ চা', 'লাল চা', 'milk tea', 'black tea', 'green tea', 'masala tea',
            ]],
            // Juice & Drinks
            ['emoji' => '🧃', 'keywords' => [
                'juice', 'jus', 'juce', 'জুস', 'sharbat', 'sherbet', 'শরবত', 'lemonade', 'লেবুর শরবত', 'orange juice', 'mango juice',
            ]],
            // Noodles & Chowmein & Pasta
            ['emoji' => '🍜', 'keywords' => [
                'noodle', 'noodles', 'nudls', 'নুডলস', 'নুডুলস', 'নুডুল', 'chowmein', 'chawmin', 'chow mein', 'chaomin', 'চাওমিন', 'চাউমিন',
                'ramen', 'maggi', 'pasta', 'পাস্তা', 'spaghetti',
            ]],
            // Biryani & Rice
            ['emoji' => '🍚', 'keywords' => [
                'biryani', 'biriyani', 'briyani', 'বিরিয়ানি', 'বিরিয়ানি', 'বিরিআনি',
                'rice', 'ভাত', 'fried rice', 'friedrice', 'ফ্রাইড রাইস',
                'khichuri', 'খিচুড়ি', 'খিচুড়ি', 'tehari', 'তেহারি', 'polao', 'pulao', 'পোলাও', 'kacchi', 'কাচ্চি',
            ]],
            // Chicken
            ['emoji' => '🍗', 'keywords' => [
                'chicken', 'chiken', 'chikn', 'চিকেন', 'মুরগি', 'মুরগী', 'fried chicken', 'ফ্রাইড চিকেন', 'wings', 'উইংস', 'drumstick', 'nugget',
            ]],
            // Fish & Seafood
            ['emoji' => '🐟', 'keywords' => [
                'fish', 'মাছ', 'fish fry', 'shrimp', 'prawn', 'চিংড়ি', 'চিংড়ি',
            ]],
            // Rolls & Shawarma & Wraps
            ['emoji' => '🌯', 'keywords' => [
                'roll', 'রোল', 'shawarma', 'sharma', 'শর্মা', 'wrap', 'র‍্যাপ', 'frankie',
            ]],
            // Fries & Chips
            ['emoji' => '🍟', 'keywords' => [
                'fry', 'fries', 'french fries', 'ফ্রেঞ্চ ফ্রাই', 'ফ্রাই', 'potato', 'আলু', 'chips', 'চিপস', 'wedges',
            ]],
            // Sandwiches
            ['emoji' => '🥪', 'keywords' => [
                'sandwich', 'sandwitch', 'স্যান্ডউইচ', 'sub', 'সাব', 'panini',
            ]],
            // Hot dog & Sausage
            ['emoji' => '🌭', 'keywords' => [
                'hotdog', 'hot dog', 'হটডগ', 'sausage', 'সসেজ',
            ]],
            // Tacos
            ['emoji' => '🌮', 'keywords' => [
                'taco', 'tacos', 'টাকো',
            ]],
            // Meat & Kebab & Steak
            ['emoji' => '🥩', 'keywords' => [
                'meat', 'beef', 'মাংস', 'বিফ', 'mutton', 'খাসি', 'steak', 'kebab', 'kabab', 'কাবাব', 'tikka', 'শিক',
            ]],
            // Soda & Soft Drink
            ['emoji' => '🥤', 'keywords' => [
                'soda', 'coke', 'pepsi', 'soft drink', 'কোল্ড ড্রিংক', 'shake', 'milkshake', 'মিল্কশেক', 'smoothie',
            ]],
            // Water
            ['emoji' => '💧', 'keywords' => [
                'water', 'pani', 'পানি', 'mineral water',
            ]],
            // Sweets & Desserts
            ['emoji' => '🍬', 'keywords' => [
                'sweet', 'sweets', 'mishti', 'misti', 'মিষ্টি', 'roshogolla', 'রসগোল্লা', 'gulab jamun', 'laddu', 'লাড্ডু',
            ]],
            // Bakery / Bread / Donut
            ['emoji' => '🍩', 'keywords' => [
                'donut', 'doughnut', 'ডোনাট',
            ]],
            ['emoji' => '🍞', 'keywords' => [
                'bread', 'toast', 'পাউরুটি', 'বন', 'bun',
            ]],
            ['emoji' => '🧇', 'keywords' => [
                'waffle', 'ওয়াফেল',
            ]],
            ['emoji' => '🥞', 'keywords' => [
                'pancake', 'প্যানকেক',
            ]],
            ['emoji' => '🥗', 'keywords' => [
                'salad', 'সালাদ',
            ]],
            ['emoji' => '🍳', 'keywords' => [
                'egg', 'omelette', 'ডিম', 'ওমলেট',
            ]],
            ['emoji' => '🍿', 'keywords' => [
                'popcorn', 'পপকর্ন',
            ]],
        ];

        foreach ($map as $entry) {
            foreach ($entry['keywords'] as $kw) {
                if (str_contains($text, mb_strtolower($kw))) {
                    return $entry['emoji'];
                }
            }
        }

        // Fallback: Check category icon if non-generic
        if ($this->category_id) {
            $cat = Category::find($this->category_id);
            if ($cat && $cat->icon && ! in_array($cat->icon, ['🍔', '📁', '🍽️', '🍴'], true)) {
                return $cat->icon;
            }
        }

        // Default food fallback: 🍽️
        return '🍽️';
    }

    public function getPopularFoodEmojisProperty(): array
    {
        return [
            '🥣', '🥟', '🍔', '🍕', '🍚', '🍜', '🍗', '🐟',
            '☕', '🧃', '🍦', '🍰', '🌯', '🍟', '🥪', '🌭',
            '🌮', '🥩', '🍢', '🍳', '🍤', '🍞', '🧇', '🥞',
            '🍩', '🍪', '🍫', '🍬', '🥤', '💧', '🍿', '🥗',
            '🍽️', '🍴',
        ];
    }

    public function render()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();

        $query = Product::with('category');

        if ($this->categoryFilter !== 'all') {
            $query->where('category_id', $this->categoryFilter);
        }

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('name_bn', 'like', '%' . $term . '%');
            });
        }

        $products = $query->orderBy('sort_order')->get();

        return view('livewire.admin.product-manager', [
            'products' => $products,
            'categories' => $categories,
            'presetImages' => FoodImageHelper::$availableImages,
            'currency' => CartSetting::currency(),
        ]);
    }
}
