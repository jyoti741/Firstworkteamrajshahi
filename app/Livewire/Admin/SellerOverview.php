<?php

namespace App\Livewire\Admin;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Seller-Wise Performance Overview')]
class SellerOverview extends Component
{
    use WithPagination;

    public ?int $sellerId = null;
    public string $period = 'today'; // 'today', 'week', 'month', 'custom'
    public string $startDate = '';
    public string $endDate = '';
    public int $chartDays = 7; // 7 or 30

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->sellerId = $user->id;
        } elseif (request()->has('seller_id')) {
            $this->sellerId = (int) request('seller_id');
        }

        $this->startDate = Carbon::today()->toDateString();
        $this->endDate = Carbon::today()->toDateString();
    }

    public function selectSeller(?int $id): void
    {
        $this->sellerId = $id;
        $this->resetPage();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->resetPage();

        if ($period === 'today') {
            $this->startDate = Carbon::today()->toDateString();
            $this->endDate = Carbon::today()->toDateString();
        } elseif ($period === 'week') {
            $this->startDate = Carbon::now()->startOfWeek()->toDateString();
            $this->endDate = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($period === 'month') {
            $this->startDate = Carbon::now()->startOfMonth()->toDateString();
            $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        }
    }

    public function setChartDays(int $days): void
    {
        $this->chartDays = $days;
    }

    public function render()
    {
        // 1. Resolve Selected Seller & List of all sellers
        $allSellers = User::where('role', 'seller')
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get();

        $selectedSeller = $this->sellerId ? User::find($this->sellerId) : null;

        // 2. Resolve Date Range Boundaries
        $start = match ($this->period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'custom' => ! empty($this->startDate) ? Carbon::parse($this->startDate)->startOfDay() : Carbon::today()->startOfDay(),
            default => Carbon::today()->startOfDay(),
        };

        $end = match ($this->period) {
            'week' => Carbon::now()->endOfWeek(),
            'month' => Carbon::now()->endOfMonth(),
            'custom' => ! empty($this->endDate) ? Carbon::parse($this->endDate)->endOfDay() : Carbon::today()->endOfDay(),
            default => Carbon::today()->endOfDay(),
        };

        // 3. Filtered Sales Queries
        $salesQuery = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end]);

        if ($this->sellerId) {
            $salesQuery->where('user_id', $this->sellerId);
        }

        $totalSales = (float) (clone $salesQuery)->sum('total_amount');
        $totalOrdersCount = (int) (clone $salesQuery)->count();
        $totalItemsSold = (int) (clone $salesQuery)->sum('total_items_count');
        $averageOrderValue = $totalOrdersCount > 0 ? round($totalSales / $totalOrdersCount, 2) : 0;

        // 4. Filtered Expenses Queries (Shift closing costs + recorded expenses)
        $expenseQuery = Expense::whereDate('expense_date', '>=', $start->toDateString())
            ->whereDate('expense_date', '<=', $end->toDateString());

        if ($this->sellerId) {
            $expenseQuery->where('user_id', $this->sellerId);
        }

        $totalExpenses = (float) (clone $expenseQuery)->sum('amount');
        $totalProfit = $totalSales - $totalExpenses;
        $profitMargin = $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 1) : 0;

        // 5. Payment Methods Breakdown
        $paymentMethodsRaw = (clone $salesQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $paymentBreakdown = [];
        foreach (['cash' => 'Cash', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'card' => 'Card / POS', 'other' => 'Other'] as $key => $label) {
            $found = $paymentMethodsRaw->firstWhere('payment_method', $key);
            $amt = $found ? (float) $found->total : 0.0;
            $cnt = $found ? (int) $found->count : 0;
            $pct = $totalSales > 0 ? round(($amt / $totalSales) * 100, 1) : 0;
            $paymentBreakdown[$key] = [
                'label' => $label,
                'amount' => $amt,
                'count' => $cnt,
                'percentage' => $pct,
            ];
        }

        // 6. Best-Selling Food Items for this seller
        $bestSellingQuery = SaleItem::whereHas('sale', function ($q) use ($start, $end) {
            $q->where('status', 'completed')
                ->whereBetween('created_at', [$start, $end]);
            if ($this->sellerId) {
                $q->where('user_id', $this->sellerId);
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
            ->take(6)
            ->get();

        // 7. Closed Cart / Shift History
        $shiftsQuery = BusinessDay::with(['openedBy', 'closedBy'])->latest('date');
        if ($this->sellerId) {
            $shiftsQuery->where(function ($q) {
                $q->where('opened_by_id', $this->sellerId)
                    ->orWhere('closed_by_id', $this->sellerId);
            });
        }
        $recentShifts = $shiftsQuery->take(6)->get();

        // 8. Daily Chart Data (Sales vs Expenses for this seller)
        $chartPoints = [];
        $maxChartValue = 100;

        for ($i = $this->chartDays - 1; $i >= 0; $i--) {
            $dayDate = Carbon::today()->subDays($i);
            $dayString = $dayDate->toDateString();

            $dSalesQuery = Sale::where('status', 'completed')->whereDate('created_at', $dayString);
            if ($this->sellerId) {
                $dSalesQuery->where('user_id', $this->sellerId);
            }
            $daySales = (float) $dSalesQuery->sum('total_amount');

            $dExpQuery = Expense::whereDate('expense_date', $dayString);
            if ($this->sellerId) {
                $dExpQuery->where('user_id', $this->sellerId);
            }
            $dayExpenses = (float) $dExpQuery->sum('amount');

            $maxChartValue = max($maxChartValue, $daySales, $dayExpenses);

            $chartPoints[] = [
                'date' => $dayString,
                'label' => $this->chartDays <= 7 ? $dayDate->format('D') : $dayDate->format('d M'),
                'full_label' => $dayDate->format('d M Y'),
                'sales' => $daySales,
                'expenses' => $dayExpenses,
                'profit' => $daySales - $dayExpenses,
            ];
        }

        // 9. Recent Sales List for this seller
        $recentSalesQuery = Sale::with(['user', 'items'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end]);
        if ($this->sellerId) {
            $recentSalesQuery->where('user_id', $this->sellerId);
        }
        $recentSales = $recentSalesQuery->latest('id')->take(8)->get();

        // 10. Recent Expenses / Shift Costs List for this seller
        $recentExpensesQuery = Expense::with(['user', 'businessDay'])
            ->whereDate('expense_date', '>=', $start->toDateString())
            ->whereDate('expense_date', '<=', $end->toDateString());
        if ($this->sellerId) {
            $recentExpensesQuery->where('user_id', $this->sellerId);
        }
        $recentExpenses = $recentExpensesQuery->latest('id')->take(8)->get();

        return view('livewire.admin.seller-overview', [
            'allSellers' => $allSellers,
            'selectedSeller' => $selectedSeller,
            'totalSales' => $totalSales,
            'totalOrdersCount' => $totalOrdersCount,
            'totalItemsSold' => $totalItemsSold,
            'averageOrderValue' => $averageOrderValue,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'profitMargin' => $profitMargin,
            'paymentBreakdown' => $paymentBreakdown,
            'bestSellingItems' => $bestSellingQuery,
            'recentShifts' => $recentShifts,
            'chartPoints' => $chartPoints,
            'maxChartValue' => $maxChartValue,
            'recentSales' => $recentSales,
            'recentExpenses' => $recentExpenses,
            'currency' => CartSetting::currency(),
        ]);
    }
}
