<?php

namespace App\Livewire\Admin;

use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Food Items & Pricing Management')]
class ProductManager extends Component
{
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
    public ?float $cost_price = 0.0;
    public string $image_emoji = '🍔';
    public int $current_stock = 0;
    public bool $track_inventory = true;
    public bool $is_available = true;
    public int $sort_order = 0;

    // Category Modal state
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryNameBn = '';
    public string $categoryIcon = '🍔';
    public int $categorySortOrder = 0;

    public function openAddProductModal(): void
    {
        $this->reset([
            'editingProductId', 'name', 'name_bn', 'description', 'price', 
            'cost_price', 'current_stock', 'sort_order'
        ]);
        $this->image_emoji = '🍔';
        $this->category_id = Category::first()?->id;
        $this->track_inventory = true;
        $this->is_available = true;
        $this->showProductModal = true;
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
        $this->image_emoji = $product->image_emoji ?? '🍔';
        $this->current_stock = $product->current_stock;
        $this->track_inventory = $product->track_inventory;
        $this->is_available = $product->is_available;
        $this->sort_order = $product->sort_order;
        $this->showProductModal = true;
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
        ]);

        $defaultCatId = $this->category_id ?: Category::first()?->id ?: Category::create(['name' => 'General', 'name_bn' => 'সাধারণ', 'icon' => '🍔'])->id;

        $data = [
            'name' => $this->name,
            'name_bn' => $this->name_bn ?: null,
            'category_id' => $defaultCatId,
            'description' => $this->description ?? '',
            'price' => $this->price,
            'cost_price' => $this->cost_price ?? 0,
            'image_emoji' => $this->image_emoji ?: '🍔',
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
        $product->is_available = ! $product->is_available;
        $product->save();
    }

    public function deleteProduct(int $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Food item removed.');
    }

    // Category Methods
    public function openAddCategoryModal(): void
    {
        $this->reset(['editingCategoryId', 'categoryName', 'categoryNameBn', 'categorySortOrder']);
        $this->categoryIcon = '🍔';
        $this->showCategoryModal = true;
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

        $data = [
            'name' => $this->categoryName,
            'name_bn' => $this->categoryNameBn ?: null,
            'icon' => $this->categoryIcon ?: '🍔',
            'sort_order' => $this->categorySortOrder,
        ];

        if ($this->editingCategoryId) {
            Category::findOrFail($this->editingCategoryId)->update($data);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create($data);
            session()->flash('success', 'New category created.');
        }

        $this->showCategoryModal = false;
    }

    public function deleteCategory(int $id): void
    {
        Category::findOrFail($id)->delete();
        session()->flash('success', 'Category deleted.');
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
                $q->where('name', 'like', '%'.$term.'%')
                    ->orWhere('name_bn', 'like', '%'.$term.'%');
            });
        }

        $products = $query->orderBy('sort_order')->get();

        return view('livewire.admin.product-manager', [
            'products' => $products,
            'categories' => $categories,
            'currency' => CartSetting::currency(),
        ]);
    }
}
