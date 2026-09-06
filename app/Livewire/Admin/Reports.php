<?php

namespace App\Livewire\Admin;

use App\Models\AssetLiability;
use App\Models\CartSetting;
use App\Models\Expense;
use App\Models\Sale;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.admin')]
#[Title('Daily Business Report')]
class Reports extends Component
{
    public string $selectedDate = '';

    public function mount(): void
    {
        $this->selectedDate = Carbon::today()->toDateString();
    }

    public function goToPreviousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->toDateString();
    }

    public function goToNextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->toDateString();
    }

    public function goToToday(): void
    {
        $this->selectedDate = Carbon::today()->toDateString();
    }

    public function exportReportCsv(): StreamedResponse
    {
        $dateStr = $this->selectedDate ?: Carbon::today()->toDateString();
        $fileName = 'daily-report-' . $dateStr . '.csv';

        return response()->streamDownload(function () use ($dateStr) {
            $handle = fopen('php://output', 'w');

            // 1. Header
            fputcsv($handle, ['Daily Activity Report - CartFlow', $dateStr]);
            fputcsv($handle, []);

            // 2. Sales
            fputcsv($handle, ['--- SALES RECORDS ---']);
            fputcsv($handle, ['Invoice No', 'Time', 'Payment Method', 'Items Summary', 'Total Amount (BDT)']);

            $sales = Sale::with('items')
                ->where('status', 'completed')
                ->whereDate('created_at', $dateStr)
                ->latest('created_at')
                ->get();

            foreach ($sales as $sale) {
                $itemsSummary = $sale->items->map(fn ($i) => "{$i->product_name} x{$i->quantity}")->implode(', ');
                fputcsv($handle, [
                    $sale->invoice_no,
                    $sale->created_at?->format('h:i A'),
                    strtoupper($sale->payment_method),
                    $itemsSummary,
                    $sale->total_amount,
                ]);
            }
            fputcsv($handle, ['Total Sales', '', '', $sales->sum('total_items_count') . ' items', $sales->sum('total_amount')]);
            $csvCash = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'cash');
            $csvBkash = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'bkash');
            $csvNagad = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'nagad');
            fputcsv($handle, ['Payment Methods', "Cash: {$csvCash->count()} (BDT {$csvCash->sum('total_amount')})", "bKash: {$csvBkash->count()} (BDT {$csvBkash->sum('total_amount')})", "Nagad: {$csvNagad->count()} (BDT {$csvNagad->sum('total_amount')})", '']);
            fputcsv($handle, []);

            // 3. Expenses
            fputcsv($handle, ['--- EXPENSES RECORDS ---']);
            fputcsv($handle, ['Expense Name', 'Category', 'Time', 'Note', 'Amount (BDT)']);

            $expenses = Expense::whereDate('expense_date', $dateStr)->latest('created_at')->get();
            foreach ($expenses as $expense) {
                fputcsv($handle, [
                    $expense->title,
                    $expense->category_label,
                    $expense->formatted_time,
                    $expense->notes ?? '',
                    $expense->amount,
                ]);
            }
            fputcsv($handle, ['Total Expenses', '', '', '', $expenses->sum('amount')]);
            fputcsv($handle, []);

            // 4. Assets
            fputcsv($handle, ['--- ASSETS RECORDS ---']);
            fputcsv($handle, ['Asset Name', 'Time', 'Amount (BDT)']);

            $assets = AssetLiability::assets()->whereDate('record_date', $dateStr)->latest('record_time')->get();
            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->name,
                    $asset->formatted_time,
                    $asset->amount,
                ]);
            }
            fputcsv($handle, ['Total Assets Added', '', $assets->sum('amount')]);

            fclose($handle);
        }, $fileName);
    }

    public function render()
    {
        $dateCarbon = Carbon::parse($this->selectedDate);

        // 1. Sales on the selected day
        $sales = Sale::with(['items', 'user'])
            ->where('status', 'completed')
            ->whereDate('created_at', $this->selectedDate)
            ->latest('created_at')
            ->get();

        $totalSalesAmount = (float) $sales->sum('total_amount');
        $totalItemsSold = (int) $sales->sum('total_items_count');

        // Payment method breakdown for selected date (Cash, bKash, Nagad)
        $cashSales = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'cash');
        $cashCount = (int) $cashSales->count();
        $cashAmount = (float) $cashSales->sum('total_amount');

        $bkashSales = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'bkash');
        $bkashCount = (int) $bkashSales->count();
        $bkashAmount = (float) $bkashSales->sum('total_amount');

        $nagadSales = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'nagad');
        $nagadCount = (int) $nagadSales->count();
        $nagadAmount = (float) $nagadSales->sum('total_amount');

        $cardSales = $sales->filter(fn ($s) => strtolower($s->payment_method) === 'card');
        $cardCount = (int) $cardSales->count();
        $cardAmount = (float) $cardSales->sum('total_amount');

        // 2. Expenses on the selected day
        $expenses = Expense::with('user')
            ->whereDate('expense_date', $this->selectedDate)
            ->latest('created_at')
            ->get();

        $totalExpensesAmount = (float) $expenses->sum('amount');

        // 3. Assets added on the selected day
        $assets = AssetLiability::assets()
            ->whereDate('record_date', $this->selectedDate)
            ->latest('record_time')
            ->latest('id')
            ->get();

        $totalAssetsAdded = (float) $assets->sum('amount');

        $todayStr = Carbon::today()->toDateString();
        $isToday = $this->selectedDate === $todayStr;

        // Calculate Today's Total Sales and Item Sold (calendar today benchmark)
        if ($isToday) {
            $todaySalesAmount = $totalSalesAmount;
            $todayItemsSold = $totalItemsSold;
        } else {
            $todaySalesQuery = Sale::where('status', 'completed')
                ->whereDate('created_at', $todayStr);
            $todaySalesAmount = (float) (clone $todaySalesQuery)->sum('total_amount');
            $todayItemsSold = (int) (clone $todaySalesQuery)->sum('total_items_count');
        }

        return view('livewire.admin.reports', [
            'selectedDate' => $this->selectedDate,
            'dateCarbon' => $dateCarbon,
            'isToday' => $isToday,
            'todaySalesAmount' => $todaySalesAmount,
            'todayItemsSold' => $todayItemsSold,
            'sales' => $sales,
            'totalSalesAmount' => $totalSalesAmount,
            'totalItemsSold' => $totalItemsSold,
            'cashCount' => $cashCount,
            'cashAmount' => $cashAmount,
            'bkashCount' => $bkashCount,
            'bkashAmount' => $bkashAmount,
            'nagadCount' => $nagadCount,
            'nagadAmount' => $nagadAmount,
            'cardCount' => $cardCount,
            'cardAmount' => $cardAmount,
            'expenses' => $expenses,
            'totalExpensesAmount' => $totalExpensesAmount,
            'assets' => $assets,
            'totalAssetsAdded' => $totalAssetsAdded,
            'currency' => CartSetting::currency(),
        ]);
    }
}
