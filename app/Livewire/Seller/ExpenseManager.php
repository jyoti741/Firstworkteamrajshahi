<?php

namespace App\Livewire\Seller;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use App\Models\Expense;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.seller')]
#[Title('Expenses')]
class ExpenseManager extends Component
{
    use WithPagination;

    public string $description = '';
    public ?float $amount = null;
    public ?int $editingExpenseId = null;
    public bool $showExpenseModal = false;

    public string $search = '';
    public string $dateFilter = 'today'; // 'today', 'all'

    protected function rules(): array
    {
        return [
            'description' => 'required|string|min:2|max:255',
            'amount' => 'required|numeric|min:0.5',
        ];
    }

    protected function messages(): array
    {
        return [
            'description.required' => app()->getLocale() === 'bn' ? 'খরচের বিবরণ দেওয়া আবশ্যক।' : 'Description is required.',
            'description.min' => app()->getLocale() === 'bn' ? 'বিবরণ কমপক্ষে ২ অক্ষরের হতে হবে।' : 'Description must be at least 2 characters.',
            'amount.required' => app()->getLocale() === 'bn' ? 'টাকার পরিমাণ দেওয়া আবশ্যক।' : 'Amount is required.',
            'amount.numeric' => app()->getLocale() === 'bn' ? 'টাকার পরিমাণ সংখ্যায় হতে হবে।' : 'Amount must be a valid number.',
            'amount.min' => app()->getLocale() === 'bn' ? 'টাকার পরিমাণ কমপক্ষে ০.৫ হতে হবে।' : 'Amount must be at least 0.5.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setDateFilter(string $filter): void
    {
        $this->dateFilter = $filter;
        $this->resetPage();
    }

    public function openAddModal(): void
    {
        $this->resetValidation();
        $this->reset(['description', 'amount', 'editingExpenseId']);
        $this->showExpenseModal = true;
    }

    public function editExpense(int $id): void
    {
        $this->resetValidation();
        $expense = Expense::where('user_id', auth()->id())->findOrFail($id);
        $this->editingExpenseId = $expense->id;
        $this->description = $expense->description ?: $expense->title;
        $this->amount = (float) $expense->amount;
        $this->showExpenseModal = true;
    }

    public function saveExpense(): void
    {
        $this->validate();

        $cleanDescription = trim($this->description);
        $cleanAmount = (float) $this->amount;

        if ($this->editingExpenseId) {
            $expense = Expense::where('user_id', auth()->id())->findOrFail($this->editingExpenseId);
            $expense->update([
                'description' => $cleanDescription,
                'title' => $cleanDescription,
                'amount' => $cleanAmount,
            ]);

            session()->flash('success', seller_trans('expense_updated'));
        } else {
            $businessDay = BusinessDay::activeSession() ?? BusinessDay::current();

            Expense::create([
                'user_id' => auth()->id(),
                'business_day_id' => $businessDay?->id,
                'description' => $cleanDescription,
                'title' => $cleanDescription,
                'amount' => $cleanAmount,
                'category' => 'other',
                'expense_date' => Carbon::today()->toDateString(),
                'expense_time' => Carbon::now()->format('H:i:s'),
            ]);

            session()->flash('success', seller_trans('expense_added'));
        }

        $this->reset(['description', 'amount', 'editingExpenseId']);
        $this->showExpenseModal = false;
    }

    public function deleteExpense(int $id): void
    {
        $expense = Expense::where('user_id', auth()->id())->findOrFail($id);
        $expense->delete();

        session()->flash('success', seller_trans('expense_deleted'));
    }

    public function closeModal(): void
    {
        $this->resetValidation();
        $this->reset(['description', 'amount', 'editingExpenseId']);
        $this->showExpenseModal = false;
    }

    public function render()
    {
        $userId = auth()->id();
        $query = Expense::query()->where('user_id', $userId)->with('user');

        if ($this->dateFilter === 'today') {
            $query->whereDate('expense_date', Carbon::today());
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $expenses = $query->latest('id')->paginate(12);

        // Dynamically compute today's total expenses and count strictly for THIS seller
        $todayTotal = (float) Expense::where('user_id', $userId)
            ->whereDate('expense_date', Carbon::today())
            ->sum('amount');
        $todayCount = (int) Expense::where('user_id', $userId)
            ->whereDate('expense_date', Carbon::today())
            ->count();

        return view('livewire.seller.expense-manager', [
            'expenses' => $expenses,
            'todayTotal' => $todayTotal,
            'todayCount' => $todayCount,
            'currency' => CartSetting::currency(),
            'locale' => app()->getLocale(),
        ]);
    }
}
