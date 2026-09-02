<?php

namespace App\Livewire\Admin;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Expense;
use Carbon\Carbon;
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
    public string $dateFilter = 'today'; // 'today', 'this_week', 'this_month', 'all'
    public string $search = '';

    // Modal state for Add/Edit
    public bool $showExpenseModal = false;
    public ?int $editingExpenseId = null;
    public string $title = '';
    public string $category = 'ingredients';
    public ?float $amount = null;
    public string $expense_date = '';
    public string $notes = '';

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
        $this->notes = $expense->notes ?? '';
        $this->showExpenseModal = true;
    }

    public function saveExpense(): void
    {
        $this->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.5',
            'notes' => 'nullable|string|max:500',
        ]);

        if (empty($this->expense_date)) {
            $this->expense_date = Carbon::today()->toDateString();
        }

        // Auto-fill title if empty from category and note
        $categoryLabel = Expense::categoryLabels()[$this->category] ?? ucfirst($this->category);
        $finalTitle = trim($this->title) !== '' 
            ? $this->title 
            : (!empty($this->notes) ? $this->notes : "{$categoryLabel} Expense");

        $businessDay = BusinessDay::whereDate('date', $this->expense_date)->first()
            ?: BusinessDay::create(['date' => $this->expense_date, 'status' => 'open', 'opened_at' => now()]);

        if ($this->editingExpenseId) {
            $expense = Expense::findOrFail($this->editingExpenseId);
            $expense->update([
                'title' => $finalTitle,
                'category' => $this->category,
                'amount' => $this->amount,
                'expense_date' => $this->expense_date,
                'notes' => $this->notes,
            ]);
            session()->flash('success', 'Expense updated successfully.');
        } else {
            Expense::create([
                'user_id' => auth()->id(),
                'business_day_id' => $businessDay->id,
                'title' => $finalTitle,
                'category' => $this->category,
                'amount' => $this->amount,
                'expense_date' => $this->expense_date,
                'notes' => $this->notes,
            ]);
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

    public function render()
    {
        $startDate = match ($this->dateFilter) {
            'this_week' => Carbon::now()->startOfWeek()->toDateString(),
            'this_month' => Carbon::now()->startOfMonth()->toDateString(),
            'all' => '2020-01-01',
            default => Carbon::today()->toDateString(),
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

        $expenses = $query->latest('expense_date')->latest('id')->paginate(12);

        // Period Total
        $totalExpensesAmount = (clone $query)->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', Carbon::today())->sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year)->sum('amount');

        // Category Breakdown for the active filter
        $categoryBreakdown = Expense::where(function ($q) use ($startDate, $endDate) {
                if ($this->dateFilter === 'today') {
                    $q->whereDate('expense_date', Carbon::today());
                } elseif ($this->dateFilter === 'this_month') {
                    $q->whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year);
                } elseif ($this->dateFilter === 'this_week') {
                    $q->whereBetween('expense_date', [$startDate, $endDate]);
                }
            })
            ->select('category', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total_amount'), \Illuminate\Support\Facades\DB::raw('COUNT(id) as count'))
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        return view('livewire.admin.expense-manager', [
            'expenses' => $expenses,
            'totalExpensesAmount' => $totalExpensesAmount,
            'todayExpenses' => $todayExpenses,
            'thisMonthExpenses' => $thisMonthExpenses,
            'categoryBreakdown' => $categoryBreakdown,
            'categoryLabels' => [
                'ingredients' => 'Ingredients',
                'transportation' => 'Transportation',
                'packaging' => 'Packaging',
                'gas' => 'Gas',
                'utilities' => 'Utilities & Power',
                'salaries' => 'Staff Salaries',
                'rent' => 'Cart Space / Rent',
                'other' => 'Other / Miscellaneous',
            ],
            'currency' => CartSetting::currency(),
        ]);
    }
}
