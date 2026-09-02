<?php

namespace App\Livewire\Admin;

use App\Models\CartSetting;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.admin')]
#[Title('Reports & Analytics')]
class Reports extends Component
{
    public int $chartDays = 7; // 7 or 30

    public function setChartDays(int $days): void
    {
        $this->chartDays = $days;
    }

    public function exportReportCsv(): StreamedResponse
    {
        $fileName = 'financial-report-'.now()->format('YmdHis').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Period', 'Sales (BDT)', 'Expenses (BDT)', 'Net Profit (BDT)']);

            // Today
            $tSales = Sale::where('status', 'completed')->whereDate('created_at', Carbon::today())->sum('total_amount');
            $tExp = Expense::whereDate('expense_date', Carbon::today())->sum('amount');
            fputcsv($handle, ['Today', $tSales, $tExp, $tSales - $tExp]);

            // This Week
            $wSales = Sale::where('status', 'completed')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
            $wExp = Expense::whereBetween('expense_date', [Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()])->sum('amount');
            fputcsv($handle, ['This Week', $wSales, $wExp, $wSales - $wExp]);

            // This Month
            $mSales = Sale::where('status', 'completed')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_amount');
            $mExp = Expense::whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year)->sum('amount');
            fputcsv($handle, ['This Month', $mSales, $mExp, $mSales - $mExp]);

            fclose($handle);
        }, $fileName);
    }

    public function render()
    {
        // 1. Section 1: TODAY
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $todaySales = (float) Sale::where('status', 'completed')->whereBetween('created_at', [$todayStart, $todayEnd])->sum('total_amount');
        $todayExpenses = (float) Expense::whereDate('expense_date', Carbon::today()->toDateString())->sum('amount');
        $todayProfit = $todaySales - $todayExpenses;

        // 2. Section 2: THIS WEEK
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekSales = (float) Sale::where('status', 'completed')->whereBetween('created_at', [$weekStart, $weekEnd])->sum('total_amount');
        $weekExpenses = (float) Expense::whereBetween('expense_date', [$weekStart->toDateString(), $weekEnd->toDateString()])->sum('amount');
        $weekProfit = $weekSales - $weekExpenses;

        // 3. Section 3: THIS MONTH
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthSales = (float) Sale::where('status', 'completed')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        $monthExpenses = (float) Expense::whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->sum('amount');
        $monthProfit = $monthSales - $monthExpenses;

        // 4. Section 4: CHART DATA (Sales vs Expenses for 7 or 30 Days)
        $chartStart = Carbon::today()->subDays($this->chartDays - 1)->startOfDay();
        $chartEnd = Carbon::today()->endOfDay();

        $chartPoints = [];
        $maxChartValue = 100; // minimum ceiling

        for ($i = $this->chartDays - 1; $i >= 0; $i--) {
            $dayDate = Carbon::today()->subDays($i);
            $dayString = $dayDate->toDateString();

            $daySales = (float) Sale::where('status', 'completed')
                ->whereDate('created_at', $dayString)
                ->sum('total_amount');

            $dayExpenses = (float) Expense::whereDate('expense_date', $dayString)->sum('amount');

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

        // 5. Section 5: BEST-SELLING ITEMS RANKING
        $bestSellingItems = SaleItem::whereHas('sale', function ($q) {
                $q->where('status', 'completed');
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
            ->take(8)
            ->get();

        return view('livewire.admin.reports', [
            'todaySales' => $todaySales,
            'todayExpenses' => $todayExpenses,
            'todayProfit' => $todayProfit,
            'weekSales' => $weekSales,
            'weekExpenses' => $weekExpenses,
            'weekProfit' => $weekProfit,
            'monthSales' => $monthSales,
            'monthExpenses' => $monthExpenses,
            'monthProfit' => $monthProfit,
            'chartPoints' => $chartPoints,
            'maxChartValue' => $maxChartValue,
            'bestSellingItems' => $bestSellingItems,
            'chartDays' => $this->chartDays,
            'currency' => CartSetting::currency(),
        ]);
    }
}
