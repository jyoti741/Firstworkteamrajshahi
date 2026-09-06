<?php

namespace App\Livewire\Admin;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\CartStatusLog;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Business Financial Dashboard')]
class Dashboard extends Component
{
    public string $timeRange = 'today'; // 'today', 'yesterday', 'this_week', 'this_month', 'this_year'

    // Business Day Close/Reopen modal state
    public bool $showDayModal = false;
    public bool $showAllShiftRecordsModal = false;
    public string $recordsViewTab = 'all_events'; // 'all_events', 'daily_summary'
    public ?float $closingCashAmount = null;
    public string $dayNotes = '';

    public function closeBusinessDay(): void
    {
        $currentDay = BusinessDay::current();

        if ($currentDay) {
            $currentDay->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by_id' => auth()->id(),
                'closing_cash_amount' => $this->closingCashAmount,
                'notes' => $this->dayNotes,
            ]);

            $salesSum = (float) $currentDay->sales()->where('status', 'completed')->sum('total_amount');
            CartStatusLog::logClose(auth()->id(), $this->closingCashAmount, 0, $salesSum, $this->dayNotes ?: 'Shift closed via Admin Dashboard', $currentDay->id);

            $this->showDayModal = false;
            $this->reset(['closingCashAmount', 'dayNotes']);
            session()->flash('success', 'Business Day has been closed successfully.');
        }
    }

    public function reopenBusinessDay(): void
    {
        $currentDay = BusinessDay::current();

        if ($currentDay) {
            $currentDay->update([
                'status' => 'open',
                'closed_at' => null,
                'closed_by_id' => null,
            ]);

            CartStatusLog::logOpen(auth()->id(), 0, 'Shift reopened from Admin Dashboard', $currentDay->id);

            session()->flash('success', 'Business Day reopened.');
        }
    }

    public function render()
    {
        // 1. Dynamic Greeting based on current hour
        $hour = Carbon::now()->hour;
        $greeting = match (true) {
            $hour < 12 => 'Good Morning 👋',
            $hour < 17 => 'Good Afternoon 👋',
            default => 'Good Evening 👋',
        };

        // 2. Determine Date Range for detailed view if filter clicked
        $startDate = match ($this->timeRange) {
            'yesterday' => Carbon::yesterday()->startOfDay(),
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            'this_year' => Carbon::now()->startOfYear(),
            default => Carbon::today()->startOfDay(),
        };

        $endDate = match ($this->timeRange) {
            'yesterday' => Carbon::yesterday()->endOfDay(),
            default => Carbon::now()->endOfDay(),
        };

        // 3. TODAY'S METRICS (Core Requirement #1)
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $todayStats = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw("
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(SUM(total_items_count), 0) as total_items,
                COUNT(*) as total_orders
            ")
            ->first();

        $todaySales = (float) ($todayStats->total_sales ?? 0);
        $todayItemsSold = (int) ($todayStats->total_items ?? 0);
        $todayOrdersCount = (int) ($todayStats->total_orders ?? 0);
        $todayExpenses = (float) Expense::whereDate('expense_date', Carbon::today()->toDateString())->sum('amount');
        $todayProfit = $todaySales - $todayExpenses;
        $isTodayProfit = $todayProfit >= 0;
        $todayLoss = $todayProfit < 0 ? abs($todayProfit) : 0;

        // 4. THIS MONTH'S METRICS (Core Requirement #1)
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthStats = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw("
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(SUM(total_items_count), 0) as total_items,
                COUNT(*) as total_orders
            ")
            ->first();
        $monthSales = (float) ($monthStats->total_sales ?? 0);
        $monthItemsSold = (int) ($monthStats->total_items ?? 0);
        $monthOrders = (int) ($monthStats->total_orders ?? 0);
        $monthExpenses = (float) Expense::whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->sum('amount');
        $monthProfit = $monthSales - $monthExpenses;

        // 5. Active Range Sales & Expenses
        $rangeStats = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                COALESCE(SUM(total_amount), 0) as gross_sales,
                COALESCE(SUM(total_cost), 0) as total_cost,
                COUNT(*) as orders_count,
                COALESCE(SUM(total_items_count), 0) as items_sold_count,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_total,
                COALESCE(SUM(CASE WHEN payment_method = 'bkash' THEN total_amount ELSE 0 END), 0) as bkash_total,
                COALESCE(SUM(CASE WHEN payment_method = 'nagad' THEN total_amount ELSE 0 END), 0) as nagad_total,
                COALESCE(SUM(CASE WHEN payment_method = 'card' THEN total_amount ELSE 0 END), 0) as card_total
            ")
            ->first();

        $grossSales = (float) ($rangeStats->gross_sales ?? 0);
        $totalCostOfGoods = (float) ($rangeStats->total_cost ?? 0);
        $ordersCount = (int) ($rangeStats->orders_count ?? 0);
        $itemsSoldCount = (int) ($rangeStats->items_sold_count ?? 0);
        $averageOrderValue = $ordersCount > 0 ? round($grossSales / $ordersCount, 2) : 0;

        $expensesTotal = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->sum('amount');
        $grossMargin = $grossSales - $totalCostOfGoods;
        $netProfit = $grossMargin - $expensesTotal;
        $netProfitMargin = $grossSales > 0 ? round(($netProfit / $grossSales) * 100, 1) : 0;

        // 6. Best-Selling Items Ranking (with product emoji)
        $bestSellingItems = SaleItem::whereHas('sale', function ($q) {
            $q->where('status', 'completed');
        })
            ->with('product')
            ->select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 7. Category Revenue Breakdown
        $categoriesData = Category::with([
            'products.saleItems' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('sale', function ($sq) use ($startDate, $endDate) {
                    $sq->where('status', 'completed')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                });
            }
        ])->get()->map(function ($cat) {
            $revenue = $cat->products->flatMap->saleItems->sum('subtotal');
            return [
                'name' => $cat->name,
                'icon' => $cat->icon,
                'revenue' => $revenue,
            ];
        })->sortByDesc('revenue');

        // 8. Payment Methods Breakdown (Computed from single indexed rangeStats query)
        $paymentBreakdown = [
            'cash' => (float) ($rangeStats->cash_total ?? 0),
            'bkash' => (float) ($rangeStats->bkash_total ?? 0),
            'nagad' => (float) ($rangeStats->nagad_total ?? 0),
            'card' => (float) ($rangeStats->card_total ?? 0),
        ];

        // 9. Business Day Status & Recent Sales
        $currentBusinessDay = BusinessDay::with(['openedBy', 'closedBy'])->whereDate('date', Carbon::today())->latest('id')->first();
        if (!$currentBusinessDay) {
            $currentBusinessDay = BusinessDay::current();
        }

        $allBusinessDays = BusinessDay::with(['openedBy', 'closedBy'])
            ->withSum(['sales' => fn($q) => $q->where('status', 'completed')], 'total_amount')
            ->withSum('expenses', 'amount')
            ->latest('date')
            ->latest('id')
            ->get();

        $allStatusLogs = CartStatusLog::with(['user', 'businessDay'])
            ->latest('occurred_at')
            ->latest('id')
            ->get();

        $recentSales = Sale::with(['items', 'user'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'greeting' => $greeting,
            'todaySales' => $todaySales,
            'todayExpenses' => $todayExpenses,
            'todayProfit' => $todayProfit,
            'isTodayProfit' => $isTodayProfit,
            'todayLoss' => $todayLoss,
            'todayItemsSold' => $todayItemsSold,
            'todayOrdersCount' => $todayOrdersCount,
            'monthSales' => $monthSales,
            'monthItemsSold' => $monthItemsSold,
            'monthOrders' => $monthOrders,
            'monthExpenses' => $monthExpenses,
            'monthProfit' => $monthProfit,
            'grossSales' => $grossSales,
            'totalCostOfGoods' => $totalCostOfGoods,
            'expensesTotal' => $expensesTotal,
            'grossMargin' => $grossMargin,
            'netProfit' => $netProfit,
            'netProfitMargin' => $netProfitMargin,
            'ordersCount' => $ordersCount,
            'itemsSoldCount' => $itemsSoldCount,
            'averageOrderValue' => $averageOrderValue,
            'bestSellingItems' => $bestSellingItems,
            'categoriesData' => $categoriesData,
            'paymentBreakdown' => $paymentBreakdown,
            'currentBusinessDay' => $currentBusinessDay,
            'allBusinessDays' => $allBusinessDays,
            'allStatusLogs' => $allStatusLogs,
            'recentSales' => $recentSales,
            'currency' => CartSetting::currency(),
        ]);
    }
}
