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
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $metrics = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw("
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(SUM(total_items_count), 0) as total_items,
                COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_total,
                COALESCE(SUM(CASE WHEN payment_method = 'bkash' THEN total_amount ELSE 0 END), 0) as bkash_total,
                COALESCE(SUM(CASE WHEN payment_method = 'nagad' THEN total_amount ELSE 0 END), 0) as nagad_total
            ")
            ->first();

        $totalRevenue = (float) ($metrics->total_revenue ?? 0);
        $totalItems = (int) ($metrics->total_items ?? 0);
        $totalOrders = (int) ($metrics->total_orders ?? 0);
        $cashTotal = (float) ($metrics->cash_total ?? 0);
        $bkashTotal = (float) ($metrics->bkash_total ?? 0);
        $nagadTotal = (float) ($metrics->nagad_total ?? 0);

        // 2. Query Sales List for Today
        $query = Sale::with(['items.product', 'user'])
            ->whereBetween('created_at', [$todayStart, $todayEnd]);

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
            'todaySalesTotal' => $totalRevenue,
            'todayItemsTotal' => $totalItems,
            'cashSalesTotal' => $cashTotal,
            'bkashSalesTotal' => $bkashTotal,
            'nagadSalesTotal' => $nagadTotal,
            'currency' => CartSetting::currency(),
            'locale' => $locale,
        ]);
    }
}
