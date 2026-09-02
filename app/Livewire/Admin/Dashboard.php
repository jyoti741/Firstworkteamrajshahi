<?php

namespace App\Livewire\Admin;

use App\Models\BusinessDay;
use App\Models\CartSetting;
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
        $todaySalesQuery = Sale::where('status', 'completed')->whereBetween('created_at', [$todayStart, $todayEnd]);
        $todaySales = (float) (clone $todaySalesQuery)->sum('total_amount');
        $todayItemsSold = (int) (clone $todaySalesQuery)->sum('total_items_count');
        $todayOrdersCount = (int) (clone $todaySalesQuery)->count();
        $todayExpenses = (float) Expense::whereDate('expense_date', Carbon::today()->toDateString())->sum('amount');
        $todayProfit = $todaySales - $todayExpenses;
        $isTodayProfit = $todayProfit >= 0;
        $todayLoss = $todayProfit < 0 ? abs($todayProfit) : 0;

        // 4. THIS MONTH'S METRICS (Core Requirement #1)
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthSales = (float) Sale::where('status', 'completed')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        $monthExpenses = (float) Expense::whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->sum('amount');
        $monthProfit = $monthSales - $monthExpenses;

        // 5. Active Range Sales & Expenses
        $salesQuery = Sale::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate]);
        $grossSales = (clone $salesQuery)->sum('total_amount');
        $totalCostOfGoods = (clone $salesQuery)->sum('total_cost');
        $ordersCount = (clone $salesQuery)->count();
        $itemsSoldCount = (clone $salesQuery)->sum('total_items_count');
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
        $categoriesData = Category::with(['products.saleItems' => function ($q) use ($startDate, $endDate) {
            $q->whereHas('sale', function ($sq) use ($startDate, $endDate) {
                $sq->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            });
        }])->get()->map(function ($cat) {
            $revenue = $cat->products->flatMap->saleItems->sum('subtotal');
            return [
                'name' => $cat->name,
                'icon' => $cat->icon,
                'revenue' => $revenue,
            ];
        })->sortByDesc('revenue');

        // 8. Payment Methods Breakdown
        $paymentBreakdown = [
            'cash' => (clone $salesQuery)->where('payment_method', 'cash')->sum('total_amount'),
            'bkash' => (clone $salesQuery)->where('payment_method', 'bkash')->sum('total_amount'),
            'nagad' => (clone $salesQuery)->where('payment_method', 'nagad')->sum('total_amount'),
            'card' => (clone $salesQuery)->where('payment_method', 'card')->sum('total_amount'),
        ];

        // 9. Business Day Status & Recent Sales
        $currentBusinessDay = BusinessDay::with(['openedBy', 'closedBy'])->whereDate('date', Carbon::today())->latest('id')->first();
        if (! $currentBusinessDay) {
            $currentBusinessDay = BusinessDay::current();
        }
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
            'recentSales' => $recentSales,
            'currency' => CartSetting::currency(),
        ]);
    }
}
