<?php

namespace App\Livewire\Admin;

use App\Models\CartSetting;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.admin')]
#[Title('Sales Management & Invoices')]
class SalesList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFilter = 'today'; // 'today', 'yesterday', 'this_week', 'this_month', 'all'
    public string $paymentFilter = 'all';
    public string $sellerFilter = 'all';
    public string $statusFilter = 'all';

    // Receipt View Modal
    public ?int $viewingSaleId = null;

    public function setDateFilter(string $filter): void
    {
        $this->dateFilter = $filter;
        $this->resetPage();
    }

    public function viewReceipt(int $saleId): void
    {
        $this->viewingSaleId = $saleId;
    }

    public function closeReceipt(): void
    {
        $this->viewingSaleId = null;
    }

    public function cancelSale(int $saleId): void
    {
        $sale = Sale::find($saleId);

        if ($sale && $sale->status === 'completed') {
            DB::transaction(function () use ($sale) {
                $sale->update(['status' => 'cancelled']);

                foreach ($sale->items as $item) {
                    if ($item->product && $item->product->track_inventory) {
                        $item->product->adjustStock($item->quantity, 'adjustment', auth()->id(), "Admin Cancelled Sale #{$sale->invoice_no}");
                    }
                }
            });

            session()->flash('success', "Sale #{$sale->invoice_no} was cancelled and inventory restored.");
        }
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'sales-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice No', 'Date Time', 'Staff / Cashier', 'Payment Method', 'Items Count', 'Total Amount (BDT)', 'Total Cost (BDT)', 'Net Profit (BDT)', 'Status']);

            $sales = Sale::with(['user', 'items'])->latest('id')->get();

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->invoice_no,
                    $sale->created_at->format('Y-m-d H:i:s'),
                    $sale->user->name ?? 'N/A',
                    strtoupper($sale->payment_method),
                    $sale->total_items_count,
                    $sale->total_amount,
                    $sale->total_cost,
                    $sale->total_profit,
                    ucfirst($sale->status),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    public function render()
    {
        // 1. Date Range Boundaries
        $startDate = match ($this->dateFilter) {
            'yesterday' => Carbon::yesterday()->startOfDay(),
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            'all' => Carbon::parse('2020-01-01')->startOfDay(),
            default => Carbon::today()->startOfDay(),
        };

        $endDate = match ($this->dateFilter) {
            'yesterday' => Carbon::yesterday()->endOfDay(),
            default => Carbon::now()->endOfDay(),
        };

        $query = Sale::with(['items.product', 'user']);

        if ($this->dateFilter !== 'all') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($this->paymentFilter !== 'all') {
            $query->where('payment_method', $this->paymentFilter);
        }

        if ($this->sellerFilter !== 'all') {
            $query->where('user_id', $this->sellerFilter);
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
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Summary Totals
        $completedSalesQuery = (clone $query)->where('status', 'completed');
        $totalSalesAmount = (float) (clone $completedSalesQuery)->sum('total_amount');
        $totalItemsCount = (int) (clone $completedSalesQuery)->sum('total_items_count');
        $totalProfitAmount = (float) (clone $completedSalesQuery)->sum('total_profit');
        $totalOrdersCount = (int) (clone $completedSalesQuery)->count();

        // 2. Product Items Sold Breakdown for the active filter
        $itemBreakdown = \App\Models\SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed');
                if ($this->dateFilter !== 'all') {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->with('product')
            ->select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->get();

        $sales = $query->latest('id')->paginate(12);
        $sellers = User::orderBy('name')->get();
        $viewingSale = $this->viewingSaleId ? Sale::with(['items.product', 'user'])->find($this->viewingSaleId) : null;

        return view('livewire.admin.sales-list', [
            'sales' => $sales,
            'sellers' => $sellers,
            'viewingSale' => $viewingSale,
            'totalSalesAmount' => $totalSalesAmount,
            'totalItemsCount' => $totalItemsCount,
            'totalProfitAmount' => $totalProfitAmount,
            'totalOrdersCount' => $totalOrdersCount,
            'itemBreakdown' => $itemBreakdown,
            'currency' => CartSetting::currency(),
        ]);
    }
}
