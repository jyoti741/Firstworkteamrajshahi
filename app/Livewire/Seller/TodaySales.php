<?php

namespace App\Livewire\Seller;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.seller')]
#[Title('Today\'s Sales Log')]
class TodaySales extends Component
{
    use WithPagination;

    public string $paymentFilter = 'all';
    public string $statusFilter = 'all';
    public string $search = '';

    public function cancelSale(int $saleId): void
    {
        $sale = Sale::where('id', $saleId)
            ->where('status', 'completed')
            ->first();

        if ($sale) {
            DB::transaction(function () use ($sale) {
                $sale->update(['status' => 'cancelled']);

                foreach ($sale->items as $item) {
                    if ($item->product && $item->product->track_inventory) {
                        $item->product->adjustStock($item->quantity, 'adjustment', auth()->id(), "Cancelled Sale #{$sale->invoice_no}");
                    }
                }
            });

            $msg = app()->getLocale() === 'bn'
                ? "লেনদেন #{$sale->invoice_no} সফলভাবে বাতিল করা হয়েছে।"
                : "Sale #{$sale->invoice_no} has been cancelled.";
            session()->flash('success', $msg);
        }
    }

    public function render()
    {
        $locale = app()->getLocale();
        $todayCompletedSales = Sale::whereDate('created_at', Carbon::today())
            ->where('status', 'completed');

        $totalRevenue = (float) (clone $todayCompletedSales)->sum('total_amount');
        $totalItems = (int) (clone $todayCompletedSales)->sum('total_items_count');
        $totalOrders = (int) (clone $todayCompletedSales)->count();

        $cashTotal = (float) (clone $todayCompletedSales)->where('payment_method', 'cash')->sum('total_amount');
        $bkashTotal = (float) (clone $todayCompletedSales)->where('payment_method', 'bkash')->sum('total_amount');
        $nagadTotal = (float) (clone $todayCompletedSales)->where('payment_method', 'nagad')->sum('total_amount');

        // 2. Query Sales List for Today
        $query = Sale::with(['items.product', 'user'])
            ->whereDate('created_at', Carbon::today());

        if ($this->paymentFilter !== 'all') {
            $query->where('payment_method', $this->paymentFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('product_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product', function ($itemQuery) use ($search) {
                        $itemQuery->where('name_bn', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->latest('id')->paginate(15);

        return view('livewire.seller.today-sales', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalItems' => $totalItems,
            'totalOrders' => $totalOrders,
            'cashTotal' => $cashTotal,
            'bkashTotal' => $bkashTotal,
            'nagadTotal' => $nagadTotal,
            'currency' => CartSetting::currency(),
            'locale' => $locale,
        ]);
    }
}
