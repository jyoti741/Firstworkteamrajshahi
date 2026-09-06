<?php

namespace App\Livewire\Seller;

use App\Helpers\BanglaHelper;
use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.seller')]
#[Title('Quick Sell POS')]
class QuickSell extends Component
{
    public string $search = '';
    public ?int $selectedCategoryId = null;
    public string $paymentMethod = 'cash'; // 'cash', 'bkash', 'nagad', 'card'

    // Quick Expense Modal state
    public bool $showExpenseModal = false;
    public string $expenseTitle = '';
    public string $expenseCategory = 'raw_materials';
    public ?float $expenseAmount = null;
    public string $expenseNotes = '';

    // Success feedback animation flag
    public ?int $lastSoldProductId = null;
    public ?string $feedbackMessage = null;

    // Cart Shift / Open-Close state
    // Cart Shift / Open-Close state
    public bool $isCartOpen = false;
    public bool $showCloseModal = false;
    public ?float $todayTotalCost = null;
    public bool $isCartClosedSubmitted = false;
    public ?float $closedSalesTotal = null;
    public ?int $closedItemsTotal = null;
    public ?float $closedCost = null;
    public ?float $closedProfit = null;

    public function mount(): void
    {
        $day = BusinessDay::activeSession();
        $this->isCartOpen = $day !== null;
    }

    /**
     * Turn ON / Open Cart (Starts a new session if previously closed)
     */
    public function openCart(): void
    {
        $day = BusinessDay::openActiveOrNew(auth()->id());
        $this->isCartOpen = true;
        $this->showCloseModal = false;
        $this->isCartClosedSubmitted = false;
        $this->todayTotalCost = null;
        $time = BanglaHelper::formatTime(now(), app()->getLocale());
        if (app()->getLocale() === 'bn') {
            $this->feedbackMessage = "🟢 কার্ট চালু হয়েছে! শুরু: {$time} • বিক্রেতা: " . auth()->user()->name;
        } else {
            $this->feedbackMessage = "🟢 Cart is OPEN! Started at {$time} by " . auth()->user()->name;
        }
    }

    /**
     * Open Close Cart Modal with validation check
     */
    public function openCloseModal(): void
    {
        $day = BusinessDay::activeSession();
        if (!$day) {
            $this->isCartOpen = false;
            $this->showCloseModal = false;
            $msg = seller_trans('cart_already_closed');
            $this->feedbackMessage = "ℹ️ {$msg}";
            return;
        }

        $this->isCartClosedSubmitted = false;
        $this->todayTotalCost = null;
        $this->resetValidation();
        $this->showCloseModal = true;
    }

    /**
     * Submit Close Cart: Finalizes active session, saves final session summary, closes cart
     */
    public function closeCart(): void
    {
        $day = BusinessDay::activeSession();

        // Prevent duplicate closing of the same cart/shift
        if (!$day) {
            $this->isCartOpen = false;
            $this->showCloseModal = false;
            $msg = seller_trans('cart_already_closed');
            $this->feedbackMessage = "ℹ️ {$msg}";
            return;
        }

        // Current session-scoped sales and item counts
        $sessionSales = (float) Sale::where('business_day_id', $day->id)
            ->where('status', 'completed')
            ->sum('total_amount');
        $sessionItems = (int) Sale::where('business_day_id', $day->id)
            ->where('status', 'completed')
            ->sum('total_items_count');

        $this->closedSalesTotal = $sessionSales;
        $this->closedItemsTotal = $sessionItems;

        $day->closeCart(auth()->id());

        $this->isCartOpen = false;
        $this->isCartClosedSubmitted = true;

        if (app()->getLocale() === 'bn') {
            $this->feedbackMessage = "🔴 কার্ট বন্ধ হয়েছে ✓";
        } else {
            $this->feedbackMessage = "🔴 Cart Closed ✓";
        }
    }

    /**
     * Dismiss the Close Cart modal
     */
    public function dismissCloseModal(): void
    {
        $this->showCloseModal = false;
        $this->isCartClosedSubmitted = false;
        $this->todayTotalCost = null;
        $this->resetValidation();
    }

    /**
     * Toggle Cart Status (Turn ON / Turn OFF)
     */
    public function toggleCartStatus(): void
    {
        if ($this->isCartOpen) {
            $this->openCloseModal();
        } else {
            $this->openCart();
        }
    }

    /**
     * Instant 1-tap sale recording for a product with explicit payment method (cash, bkash, nagad)
     */
    public function recordSale(int $productId, int $quantity = 1, ?string $paymentMethod = null): void
    {
        $businessDay = BusinessDay::activeSession();
        if (!$businessDay) {
            $this->isCartOpen = false;
            $msg = app()->getLocale() === 'bn' ? 'কার্ট বন্ধ আছে। অনুগ্রহ করে কার্ট চালু করুন।' : 'Cart is closed. Please turn ON cart first.';
            $this->dispatch('notify', message: $msg, type: 'error');
            return;
        }

        $product = Product::find($productId);

        if (!$product || !$product->is_available) {
            $this->dispatch('notify', message: app()->getLocale() === 'bn' ? 'খাবারটি বর্তমানে অনুপলব্ধ' : 'Product is currently unavailable', type: 'error');
            return;
        }

        // Determine payment method (defaults to cash if not provided)
        $method = in_array($paymentMethod, ['cash', 'bkash', 'nagad', 'card']) ? $paymentMethod : ($this->paymentMethod ?? 'cash');

        // Check inventory if tracking is enabled
        if ($product->track_inventory && $product->current_stock < $quantity) {
            $msg = app()->getLocale() === 'bn'
                ? "স্টক কম! মাত্র " . BanglaHelper::toBanglaNumeral($product->current_stock) . " টি অবশিষ্ট আছে।"
                : "Low stock alert! Only {$product->current_stock} remaining.";
            $this->dispatch('notify', message: $msg, type: 'warning');
        }

        $subtotal = $product->price * $quantity;
        $totalCost = $product->cost_price * $quantity;
        $profit = $subtotal - $totalCost;

        DB::transaction(function () use ($product, $quantity, $businessDay, $subtotal, $totalCost, $profit, $method) {
            $sale = Sale::create([
                'invoice_no' => Sale::generateInvoiceNumber(),
                'user_id' => auth()->id(),
                'business_day_id' => $businessDay->id,
                'total_amount' => $subtotal,
                'total_cost' => $totalCost,
                'total_profit' => $profit,
                'total_items_count' => $quantity,
                'payment_method' => $method,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'unit_cost' => $product->cost_price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'profit' => $profit,
            ]);

            if ($product->track_inventory) {
                $product->adjustStock(-$quantity, 'sale', auth()->id(), "POS Sale #{$sale->invoice_no}");
            }
        });

        $this->lastSoldProductId = $productId;
        $displayName = $product->displayName();
        $curr = BanglaHelper::formatCurrency($subtotal);
        $qty = BanglaHelper::formatNumber($quantity);
        $methodName = seller_trans($method);

        if (app()->getLocale() === 'bn') {
            $this->feedbackMessage = "+{$qty} {$displayName} ({$curr}) • {$methodName} বিক্রি রেকর্ড করা হয়েছে!";
        } else {
            $this->feedbackMessage = "+{$quantity} {$displayName} ({$curr}) • {$methodName} recorded!";
        }
    }

    /**
     * Instant 1-tap correction/decrement
     */
    public function recordCorrection(int $productId): void
    {
        $activeSession = BusinessDay::activeSession();
        $sessionId = $activeSession?->id;

        // Find the latest sale containing this product in the active session
        $lastSaleItemQuery = SaleItem::where('product_id', $productId)
            ->whereHas('sale', function ($query) use ($sessionId) {
                $query->where('status', 'completed');
                if ($sessionId) {
                    $query->where('business_day_id', $sessionId);
                } else {
                    $query->whereDate('created_at', Carbon::today());
                }
            })
            ->latest('id');

        $lastSaleItem = $lastSaleItemQuery->first();

        if (!$lastSaleItem) {
            $this->feedbackMessage = app()->getLocale() === 'bn'
                ? 'বর্তমান শিফটে এই খাবারের কোনো বিক্রি নেই যা সংশোধন করা যাবে।'
                : 'No sales recorded for this item in this shift to cancel.';
            return;
        }

        $sale = $lastSaleItem->sale;
        $product = $lastSaleItem->product;

        DB::transaction(function () use ($sale, $product, $lastSaleItem) {
            if ($sale->items()->count() === 1 && $lastSaleItem->quantity === 1) {
                // If it's a single item sale, mark sale as cancelled
                $sale->update(['status' => 'cancelled']);
            } else {
                // Adjust quantity or delete line
                if ($lastSaleItem->quantity > 1) {
                    $lastSaleItem->quantity -= 1;
                    $lastSaleItem->subtotal -= $lastSaleItem->unit_price;
                    $lastSaleItem->profit -= ($lastSaleItem->unit_price - $lastSaleItem->unit_cost);
                    $lastSaleItem->save();

                    $sale->total_amount -= $lastSaleItem->unit_price;
                    $sale->total_cost -= $lastSaleItem->unit_cost;
                    $sale->total_profit -= ($lastSaleItem->unit_price - $lastSaleItem->unit_cost);
                    $sale->total_items_count -= 1;
                    $sale->save();
                } else {
                    $lastSaleItem->delete();
                    $sale->total_amount -= $lastSaleItem->unit_price;
                    $sale->total_cost -= $lastSaleItem->unit_cost;
                    $sale->total_profit -= ($lastSaleItem->unit_price - $lastSaleItem->unit_cost);
                    $sale->total_items_count -= 1;
                    if ($sale->total_items_count <= 0) {
                        $sale->update(['status' => 'cancelled']);
                    } else {
                        $sale->save();
                    }
                }
            }

            // Restore inventory
            if ($product && $product->track_inventory) {
                $product->adjustStock(1, 'adjustment', auth()->id(), "POS Sale Correction #{$sale->invoice_no}");
            }
        });

        $displayName = $product ? $product->displayName() : $lastSaleItem->product_name;
        $one = BanglaHelper::formatNumber(1);

        if (app()->getLocale() === 'bn') {
            $this->feedbackMessage = "−{$one} {$displayName} সংশোধন করা হয়েছে!";
        } else {
            $this->feedbackMessage = "−1 {$displayName} corrected!";
        }
    }

    /**
     * Quick undo the very last sale
     */
    public function undoLastSale(int $saleId): void
    {
        $sale = Sale::where('id', $saleId)
            ->where('status', 'completed')
            ->first();

        if ($sale) {
            DB::transaction(function () use ($sale) {
                $sale->update(['status' => 'cancelled']);

                foreach ($sale->items as $item) {
                    if ($item->product && $item->product->track_inventory) {
                        $item->product->adjustStock($item->quantity, 'adjustment', auth()->id(), "Undid Sale #{$sale->invoice_no}");
                    }
                }
            });

            if (app()->getLocale() === 'bn') {
                $this->feedbackMessage = "লেনদেন #{$sale->invoice_no} বাতিল ও ফেরত দেওয়া হয়েছে।";
            } else {
                $this->feedbackMessage = "Sale #{$sale->invoice_no} cancelled & refunded.";
            }
        }
    }

    /**
     * Submit quick expense from staff
     */
    public function saveExpense(): void
    {
        if (!CartSetting::allowSellerExpense()) {
            $this->dispatch('notify', message: app()->getLocale() === 'bn' ? 'অ্যাডমিন কর্তৃক খরচ এন্ট্রি নিষ্ক্রিয় রয়েছে।' : 'Staff expenses are disabled by Admin.', type: 'error');
            return;
        }

        $this->validate([
            'expenseTitle' => 'required|string|max:150',
            'expenseCategory' => 'required|string',
            'expenseAmount' => 'required|numeric|min:1',
            'expenseNotes' => 'nullable|string|max:300',
        ]);

        $businessDay = BusinessDay::activeSession() ?? BusinessDay::current();

        Expense::create([
            'user_id' => auth()->id(),
            'business_day_id' => $businessDay?->id,
            'title' => $this->expenseTitle,
            'category' => $this->expenseCategory,
            'amount' => $this->expenseAmount,
            'expense_date' => Carbon::today()->toDateString(),
            'notes' => $this->expenseNotes,
        ]);

        $this->reset(['expenseTitle', 'expenseAmount', 'expenseNotes']);
        $this->showExpenseModal = false;
        $this->feedbackMessage = app()->getLocale() === 'bn' ? 'খরচ সফলভাবে রেকর্ড করা হয়েছে!' : 'Expense recorded successfully!';
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategoryId = $categoryId;
    }

    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
    }

    /**
     * Get aggregate sales stats for today
     */
    private function getTodaySalesData(): array
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $stats = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw("
                COALESCE(SUM(total_amount), 0) as total,
                COALESCE(SUM(total_items_count), 0) as items_count,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash,
                COALESCE(SUM(CASE WHEN payment_method = 'bkash' THEN total_amount ELSE 0 END), 0) as bkash,
                COALESCE(SUM(CASE WHEN payment_method = 'nagad' THEN total_amount ELSE 0 END), 0) as nagad
            ")
            ->first();

        return [
            'total' => (float) ($stats->total ?? 0),
            'items_count' => (int) ($stats->items_count ?? 0),
            'cash' => (float) ($stats->cash ?? 0),
            'bkash' => (float) ($stats->bkash ?? 0),
            'nagad' => (float) ($stats->nagad ?? 0),
        ];
    }

    /**
     * Get per-product sales stats for today
     */
    private function getProductSalesStats(): \Illuminate\Support\Collection
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $stats = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$todayStart, $todayEnd])
            ->selectRaw("
                sale_items.product_id,
                COALESCE(SUM(sale_items.quantity), 0) as count,
                COALESCE(SUM(sale_items.subtotal), 0) as revenue,
                COALESCE(SUM(CASE WHEN sales.payment_method = 'cash' THEN sale_items.quantity ELSE 0 END), 0) as cash_count,
                COALESCE(SUM(CASE WHEN sales.payment_method = 'bkash' THEN sale_items.quantity ELSE 0 END), 0) as bkash_count,
                COALESCE(SUM(CASE WHEN sales.payment_method = 'nagad' THEN sale_items.quantity ELSE 0 END), 0) as nagad_count
            ")
            ->groupBy('sale_items.product_id')
            ->get();

        return $stats->keyBy('product_id')->map(function ($row) {
            return [
                'count' => (int) $row->count,
                'revenue' => (float) $row->revenue,
                'cash_count' => (int) $row->cash_count,
                'bkash_count' => (int) $row->bkash_count,
                'nagad_count' => (int) $row->nagad_count,
            ];
        });
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $locale = session('seller_locale', auth()->user()?->locale ?? app()->getLocale());
        if (in_array($locale, ['en', 'bn'], true)) {
            app()->setLocale($locale);
        }

        // 1. Get Categories
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        // 2. Query Products
        $productsQuery = Product::with('category')
            ->where('is_available', true);

        if ($this->selectedCategoryId) {
            $productsQuery->where('category_id', $this->selectedCategoryId);
        }

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $productsQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('name_bn', 'like', '%' . $term . '%');
            });
        }

        $products = $productsQuery->orderBy('sort_order')->get();

        // 3. Current Day & Cart Status Resolution
        $activeSession = BusinessDay::activeSession();
        $this->isCartOpen = $activeSession !== null;
        $currentBusinessDay = $activeSession ?? BusinessDay::where('date', Carbon::today()->toDateString())->latest('id')->first();

        // 4. Sales & Expenses Data (Optimized single SQL aggregate queries)
        $salesData = $this->getTodaySalesData();
        $productTodaySales = $this->getProductSalesStats();

        $greeting = BanglaHelper::getGreeting($locale);

        return view('livewire.seller.quick-sell', [
            'greeting' => $greeting,
            'currentBusinessDay' => $currentBusinessDay,
            'categories' => $categories,
            'products' => $products,
            'productTodaySales' => $productTodaySales,
            'todaySalesTotal' => $salesData['total'],
            'todayItemsTotal' => $salesData['items_count'],
            'cashSalesTotal' => $salesData['cash'],
            'bkashSalesTotal' => $salesData['bkash'],
            'nagadSalesTotal' => $salesData['nagad'],
            'todayExpenses' => collect(),
            'todayExpensesTotal' => 0.0,
            'recentSales' => collect(),
            'currency' => CartSetting::currency(),
            'allowExpense' => CartSetting::allowSellerExpense(),
            'locale' => $locale,
        ]);
    }
}
