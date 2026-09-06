<?php

namespace App\Livewire\Admin;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Expense;
use App\Models\ExpenseClosing;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Operating Expense Management')]
class ExpenseManager extends Component
{
    use WithPagination;

    public string $categoryFilter = 'all';
    public string $dateFilter = 'all'; // Default to all so owner sees all running expenses
    public string $search = '';

    // Modal state for Add/Edit Expense
    public bool $showExpenseModal = false;
    public ?int $editingExpenseId = null;
    public string $title = '';
    public string $category = 'ingredients';
    public ?float $amount = null;
    public string $expense_date = '';
    public string $expense_time = '';
    public string $notes = '';

    // Closing Modal states
    public bool $showCloseConfirmModal = false;
    public bool $showClosingSummary = false;
    public ?ExpenseClosing $latestClosing = null;

    public function setDateFilter(string $filter): void
    {
        $this->dateFilter = $filter;
        $this->resetPage();
    }

    public function openAddModal(): void
    {
        $this->reset(['editingExpenseId', 'title', 'amount', 'notes']);
        $this->category = 'ingredients';
        $this->expense_date = Carbon::today()->toDateString();
        $this->expense_time = Carbon::now()->format('H:i');
        $this->showExpenseModal = true;
    }

    public function editExpense(int $id): void
    {
        $expense = Expense::findOrFail($id);
        $this->editingExpenseId = $expense->id;
        $this->title = $expense->title;
        $this->category = $expense->category;
        $this->amount = (float) $expense->amount;
        $this->expense_date = $expense->expense_date->toDateString();
        $this->expense_time = $expense->expense_time
            ? Carbon::parse($expense->expense_time)->format('H:i')
            : ($expense->created_at ? $expense->created_at->format('H:i') : Carbon::now()->format('H:i'));
        $this->notes = $expense->notes ?? '';
        $this->showExpenseModal = true;
    }

    public function saveExpense(): void
    {
        $this->validate([
            'title' => 'nullable|string|max:255',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'expense_time' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        if (empty($this->expense_date)) {
            $this->expense_date = Carbon::today()->toDateString();
        }

        $time = trim($this->expense_time) !== ''
            ? Carbon::parse($this->expense_time)->format('H:i:s')
            : Carbon::now()->format('H:i:s');

        $recordedDateTime = Carbon::parse("{$this->expense_date} {$time}");

        // Auto-fill title if empty from category and note
        $categoryLabel = Expense::categoryLabels()[$this->category] ?? ucfirst($this->category);
        $finalTitle = trim($this->title) !== ''
            ? trim($this->title)
            : (!empty($this->notes) ? $this->notes : "{$categoryLabel} Expense");

        $businessDay = BusinessDay::whereDate('date', $this->expense_date)->first()
            ?: BusinessDay::create(['date' => $this->expense_date, 'status' => 'open', 'opened_at' => now()]);

        if ($this->editingExpenseId) {
            $expense = Expense::findOrFail($this->editingExpenseId);
            $expense->timestamps = false;
            $expense->title = $finalTitle;
            $expense->category = $this->category;
            $expense->amount = $this->amount;
            $expense->expense_date = $this->expense_date;
            $expense->expense_time = $time;
            $expense->notes = $this->notes;
            $expense->created_at = $recordedDateTime;
            $expense->save();
            $expense->timestamps = true;

            session()->flash('success', 'Expense updated successfully.');
        } else {
            $expense = new Expense([
                'user_id' => auth()->id(),
                'business_day_id' => $businessDay->id,
                'title' => $finalTitle,
                'category' => $this->category,
                'amount' => $this->amount,
                'expense_date' => $this->expense_date,
                'expense_time' => $time,
                'notes' => $this->notes,
            ]);
            $expense->timestamps = false;
            $expense->created_at = $recordedDateTime;
            $expense->updated_at = now();
            $expense->save();
            $expense->timestamps = true;

            session()->flash('success', 'Expense recorded successfully.');
        }

        $this->showExpenseModal = false;
        $this->reset(['editingExpenseId', 'title', 'amount', 'notes']);
    }

    public function deleteExpense(int $id): void
    {
        Expense::findOrFail($id)->delete();
        session()->flash('success', 'Expense removed.');
    }

    /**
     * Close the current running period and calculate sales, expenses, and profit.
     */
    public function closeAndCalculate(): void
    {
        $lastClosing = ExpenseClosing::latest('closed_at')->first();
        $periodStart = $lastClosing?->closed_at;
        $closedAt = Carbon::now();

        // 1. All completed sales recorded since previous closing
        $salesQuery = Sale::where('status', 'completed')
            ->when($periodStart, fn ($q) => $q->where('created_at', '>', $periodStart))
            ->where('created_at', '<=', $closedAt);

        $totalSales = (float) $salesQuery->sum('total_amount');
        $salesCount = $salesQuery->count();

        // 2. All expenses recorded since previous closing
        $expensesQuery = Expense::query()
            ->when($periodStart, fn ($q) => $q->where('created_at', '>', $periodStart))
            ->where('created_at', '<=', $closedAt);

        $totalExpenses = (float) $expensesQuery->sum('amount');
        $expensesCount = $expensesQuery->count();

        // 3. Profit or Loss
        $netProfit = $totalSales - $totalExpenses;

        // 4. Save permanent historical closing record
        $closing = ExpenseClosing::create([
            'user_id' => auth()->id(),
            'period_start' => $periodStart,
            'closed_at' => $closedAt,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'sales_count' => $salesCount,
            'expenses_count' => $expensesCount,
        ]);

        $this->latestClosing = $closing;
        $this->showClosingSummary = true;
        $this->showCloseConfirmModal = false;

        session()->flash('success', 'Closing calculation completed and saved to history.');
    }

    public function viewClosingSummary(int $id): void
    {
        $this->latestClosing = ExpenseClosing::findOrFail($id);
        $this->showClosingSummary = true;
    }

    public function render()
    {
        $startDate = match ($this->dateFilter) {
            'this_week' => Carbon::now()->startOfWeek()->toDateString(),
            'this_month' => Carbon::now()->startOfMonth()->toDateString(),
            'today' => Carbon::today()->toDateString(),
            default => '2020-01-01',
        };

        $endDate = match ($this->dateFilter) {
            'all' => Carbon::now()->addYear()->toDateString(),
            default => Carbon::now()->toDateString(),
        };

        $query = Expense::with('user');

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->dateFilter === 'today') {
            $query->whereDate('expense_date', Carbon::today());
        } elseif ($this->dateFilter === 'this_week') {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        } elseif ($this->dateFilter === 'this_month') {
            $query->whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year);
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $expenses = $query->latest('expense_date')->latest('created_at')->latest('id')->paginate(15);

        // Overall total expenses
        $totalExpensesAmount = (clone $query)->sum('amount');
        $allTotalExpenses = Expense::sum('amount');

        // Running register: Activity since previous closing
        $lastClosing = ExpenseClosing::latest('closed_at')->first();
        $openPeriodStart = $lastClosing?->closed_at;

        $runningSales = (float) Sale::where('status', 'completed')
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->sum('total_amount');

        $runningExpenses = (float) Expense::query()
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->sum('amount');

        $runningProfit = $runningSales - $runningExpenses;
        $runningSalesCount = Sale::where('status', 'completed')
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->count();
        $runningItemsSold = (int) Sale::where('status', 'completed')
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->sum('total_items_count');
        $runningExpensesCount = Expense::query()
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->count();

        // Payment method breakdown for open period
        $runningCashCount = Sale::where('status', 'completed')
            ->where('payment_method', 'cash')
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->count();
        $runningBkashCount = Sale::where('status', 'completed')
            ->where('payment_method', 'bkash')
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->count();
        $runningNagadCount = Sale::where('status', 'completed')
            ->where('payment_method', 'nagad')
            ->when($openPeriodStart, fn ($q) => $q->where('created_at', '>', $openPeriodStart))
            ->count();

        // Today's total sales and items sold (calendar day)
        $todaySalesAmount = (float) Sale::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $todayItemsSold = (int) Sale::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_items_count');

        // Closing History records
        $closingHistory = ExpenseClosing::with('user')->latest('closed_at')->get();

        return view('livewire.admin.expense-manager', [
            'expenses' => $expenses,
            'totalExpensesAmount' => $totalExpensesAmount,
            'allTotalExpenses' => $allTotalExpenses,
            'lastClosing' => $lastClosing,
            'openPeriodStart' => $openPeriodStart,
            'runningSales' => $runningSales,
            'runningExpenses' => $runningExpenses,
            'runningProfit' => $runningProfit,
            'runningSalesCount' => $runningSalesCount,
            'runningItemsSold' => $runningItemsSold,
            'runningCashCount' => $runningCashCount,
            'runningBkashCount' => $runningBkashCount,
            'runningNagadCount' => $runningNagadCount,
            'todaySalesAmount' => $todaySalesAmount,
            'todayItemsSold' => $todayItemsSold,
            'runningExpensesCount' => $runningExpensesCount,
            'closingHistory' => $closingHistory,
            'categoryLabels' => Expense::categoryLabels(),
            'currency' => CartSetting::currency(),
        ]);
    }
}
