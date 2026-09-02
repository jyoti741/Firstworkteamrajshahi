<?php

namespace App\Livewire\Admin;

use App\Models\CartSetting;
use App\Models\InventoryLog;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Inventory & Stock Management')]
class InventoryManager extends Component
{
    use WithPagination;

    public string $activeTab = 'stock'; // 'stock', 'logs'
    public string $search = '';
    public bool $lowStockOnly = false;

    // Restock / Adjustment Modal
    public bool $showAdjustModal = false;
    public ?int $selectedProductId = null;
    public string $adjustType = 'restock'; // 'restock', 'waste', 'adjustment'
    public ?int $adjustQuantity = 10;
    public string $adjustNotes = '';

    public function openAdjustModal(int $productId, string $type = 'restock'): void
    {
        $this->selectedProductId = $productId;
        $this->adjustType = $type;
        $this->adjustQuantity = ($type === 'restock') ? 20 : 1;
        $this->adjustNotes = '';
        $this->showAdjustModal = true;
    }

    public function quickRestock(int $productId, int $qty): void
    {
        $product = Product::findOrFail($productId);
        $product->adjustStock($qty, 'restock', auth()->id(), "Quick Restock (+{$qty}) by Admin");
        session()->flash('success', "Restocked +{$qty} units for '{$product->name}'.");
    }

    public function applyAdjustment(): void
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'adjustQuantity' => 'required|integer|min:1',
            'adjustType' => 'required|in:restock,waste,adjustment',
            'adjustNotes' => 'nullable|string|max:300',
        ]);

        $product = Product::findOrFail($this->selectedProductId);
        $qtyChange = ($this->adjustType === 'restock') ? $this->adjustQuantity : -$this->adjustQuantity;

        $product->adjustStock(
            $qtyChange,
            $this->adjustType,
            auth()->id(),
            $this->adjustNotes ?: ucfirst($this->adjustType).' adjustment'
        );

        $this->showAdjustModal = false;
        session()->flash('success', "Inventory updated for '{$product->name}'. New stock: {$product->current_stock}");
    }

    public function render()
    {
        $productsQuery = Product::with('category')->where('track_inventory', true);

        if ($this->lowStockOnly) {
            $productsQuery->where('current_stock', '<=', 10);
        }

        if (trim($this->search) !== '') {
            $productsQuery->where('name', 'like', '%'.trim($this->search).'%');
        }

        $products = $productsQuery->orderBy('current_stock')->get();
        $lowStockCount = Product::where('track_inventory', true)->where('current_stock', '<=', 10)->count();

        // Logs Query
        $logs = InventoryLog::with(['product', 'user'])->latest('id')->paginate(15);
        $selectedProduct = $this->selectedProductId ? Product::find($this->selectedProductId) : null;

        return view('livewire.admin.inventory-manager', [
            'products' => $products,
            'lowStockCount' => $lowStockCount,
            'logs' => $logs,
            'selectedProduct' => $selectedProduct,
            'currency' => CartSetting::currency(),
        ]);
    }
}
