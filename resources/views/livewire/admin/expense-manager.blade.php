<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>💸</span> Expenses
                </h1>
                <p class="text-xs text-zinc-400">Track and manage daily cart operating expenses.</p>
            </div>

            <button type="button" 
                    wire:click="openAddModal"
                    class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-zinc-950 font-black rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-lg shadow-orange-500/20 touch-press cursor-pointer">
                <span>+</span> Add Expense
            </button>
        </div>

        <!-- Simple Filter Tabs -->
        <div class="flex items-center bg-zinc-900 p-1.5 rounded-2xl border border-zinc-800 text-xs font-bold overflow-x-auto scrollbar-none gap-1 shadow-md">
            <button type="button" 
                    wire:click="setDateFilter('today')" 
                    class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'today' ? 'bg-amber-500 text-zinc-950 shadow-md shadow-amber-500/20 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                Today
            </button>
            <button type="button" 
                    wire:click="setDateFilter('this_week')" 
                    class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'this_week' ? 'bg-amber-500 text-zinc-950 shadow-md shadow-amber-500/20 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                This Week
            </button>
            <button type="button" 
                    wire:click="setDateFilter('this_month')" 
                    class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'this_month' ? 'bg-amber-500 text-zinc-950 shadow-md shadow-amber-500/20 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                This Month
            </button>
            <button type="button" 
                    wire:click="setDateFilter('all')" 
                    class="py-2 px-3 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'all' ? 'bg-amber-500 text-zinc-950 shadow-md shadow-amber-500/20 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                All Time
            </button>
        </div>
    </div>

    <!-- Expense Total Headline Card -->
    <div class="bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <h2 class="font-bold text-sm sm:text-base text-zinc-300">
                {{ match($dateFilter) { 'this_week' => "This Week's Expenses", 'this_month' => "This Month's Expenses", 'all' => "All Time Expenses", default => "Today's Expenses" } }}
            </h2>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300">
                Operating Cost
            </span>
        </div>

        <div class="pt-4">
            <div class="text-3xl sm:text-4xl font-black text-rose-400">
                {{ $currency }}{{ number_format($totalExpensesAmount, 0) }}
            </div>
            <p class="text-xs text-zinc-400 mt-1">Total recorded in this time range.</p>
        </div>
    </div>

    <!-- Simple Category Spending Breakdown (Clean List) -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-base text-white flex items-center gap-2">
                <span>📋</span> Expense Categories
            </h3>
            <span class="text-xs text-zinc-400">{{ $categoryBreakdown->count() }} categories</span>
        </div>

        <div class="space-y-2 pt-1">
            @forelse($categoryBreakdown as $cat)
                @php
                    $catLabel = match($cat->category) {
                        'ingredients', 'raw_materials' => 'Ingredients',
                        'transportation', 'transport' => 'Transportation',
                        'packaging' => 'Packaging',
                        'gas' => 'Gas',
                        'utilities' => 'Utilities & Power',
                        'salaries' => 'Staff Salaries',
                        'rent' => 'Cart Rent',
                        default => 'Other / Miscellaneous',
                    };
                    $catEmoji = match($cat->category) {
                        'ingredients', 'raw_materials' => '🥩',
                        'transportation', 'transport' => '🛺',
                        'packaging' => '📦',
                        'gas' => '🔥',
                        'utilities' => '💡',
                        'salaries' => '👥',
                        'rent' => '🎪',
                        default => '💸',
                    };
                @endphp
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">{{ $catEmoji }}</span>
                        <div>
                            <span class="font-bold text-zinc-100">{{ $catLabel }}</span>
                            <span class="text-[11px] text-zinc-500 ml-1">({{ $cat->count }} entries)</span>
                        </div>
                    </div>

                    <div class="font-black text-rose-400 text-sm sm:text-base">
                        {{ $currency }}{{ number_format($cat->total_amount, 0) }}
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-500 py-6 text-center">No expenses recorded for this period.</p>
            @endforelse
        </div>

        <!-- Prominent + Add Expense Trigger -->
        <div class="pt-2">
            <button type="button" 
                    wire:click="openAddModal"
                    class="w-full py-3 bg-zinc-950 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-amber-500/40 text-amber-400 font-bold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer">
                <span>+</span> Add Expense
            </button>
        </div>
    </div>

    <!-- Individual Expense Records List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-base text-white flex items-center gap-2">
                <span>📝</span> Recent Logged Expenses
            </h3>
            <input type="text" 
                   wire:model.live.debounce.250ms="search" 
                   placeholder="🔍 Search..." 
                   class="bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 w-36 sm:w-48">
        </div>

        <div class="space-y-3">
            @forelse($expenses as $expense)
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-4 flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-sm text-zinc-100">{{ $expense->title }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-800 text-zinc-300">
                                    {{ $expense->category_label }}
                                </span>
                            </div>
                            <p class="text-[11px] text-zinc-400 mt-1">
                                {{ $expense->expense_date->format('d M Y') }} • Logged by: <strong class="text-zinc-300">{{ $expense->user->name }}</strong>
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="text-base sm:text-lg font-black text-rose-400">
                                {{ $currency }}{{ number_format($expense->amount, 0) }}
                            </span>
                        </div>
                    </div>

                    @if($expense->notes)
                        <p class="text-xs text-zinc-400 bg-zinc-900/60 p-2 rounded-xl border border-zinc-800/50">
                            {{ $expense->notes }}
                        </p>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-zinc-800/60 text-xs">
                        <button type="button" 
                                wire:click="editExpense({{ $expense->id }})"
                                class="px-3 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-lg font-semibold cursor-pointer">
                            Edit
                        </button>
                        <button type="button" 
                                wire:click="deleteExpense({{ $expense->id }})"
                                wire:confirm="Delete this expense record?"
                                class="px-3 py-1 bg-rose-950/30 hover:bg-rose-900 text-rose-400 rounded-lg font-semibold cursor-pointer">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-500 text-center py-6">No individual expense logs found.</p>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Short Add/Edit Expense Modal Form -->
    @if($showExpenseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">💸</span>
                        <h3 class="font-bold text-base text-white">{{ $editingExpenseId ? 'Edit Expense' : 'Add Expense' }}</h3>
                    </div>
                    <button type="button" wire:click="$set('showExpenseModal', false)" class="text-zinc-400 hover:text-white">✕</button>
                </div>

                <form wire:submit="saveExpense" class="space-y-4">
                    <!-- Category Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Category</label>
                        <select wire:model="category" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white focus:ring-2 focus:ring-amber-500 focus:outline-none cursor-pointer">
                            <option value="ingredients">🥩 Ingredients</option>
                            <option value="transportation">🛺 Transportation</option>
                            <option value="packaging">📦 Packaging</option>
                            <option value="gas">🔥 Gas</option>
                            <option value="utilities">💡 Utilities & Power</option>
                            <option value="salaries">👥 Staff Salaries</option>
                            <option value="rent">🎪 Cart Space Rent</option>
                            <option value="other">💸 Other / Miscellaneous</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Amount ({{ $currency }})</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-lg font-bold text-zinc-500">{{ $currency }}</span>
                            <input type="number" 
                                   step="0.01" 
                                   wire:model="amount" 
                                   placeholder="0" 
                                   class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl pl-9 pr-4 py-3 text-lg text-white font-black focus:ring-2 focus:ring-amber-500 focus:outline-none" 
                                   required>
                        </div>
                        @error('amount') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Note (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Note (Optional)</label>
                        <input type="text" 
                               wire:model="notes" 
                               placeholder="e.g. 50 buns, rickshaw fare, cylinder" 
                               class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-white placeholder-zinc-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-800">
                        <button type="button" 
                                wire:click="$set('showExpenseModal', false)" 
                                class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-zinc-400 hover:text-white bg-zinc-800 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-zinc-950 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 shadow-lg shadow-amber-500/20 touch-press cursor-pointer">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
