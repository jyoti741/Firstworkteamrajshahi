<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2">
                    <span>💸</span> Expenses
                </h1>
                <p class="text-xs text-[#8D7B70] font-medium">Track and manage daily cart operating expenses.</p>
            </div>

            <button type="button" wire:click="openAddModal"
                class="px-4 py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-2xs touch-press cursor-pointer">
                <span>+</span> Add Expense
            </button>
        </div>

        <!-- Simple Filter Tabs -->
        <div
            class="flex items-center bg-white p-1 rounded-2xl border border-[#EFE7DE] text-xs font-bold overflow-x-auto scrollbar-none gap-1 shadow-2xs">
            <button type="button" wire:click="setDateFilter('today')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'today' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                Today
            </button>
            <button type="button" wire:click="setDateFilter('this_week')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'this_week' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                This Week
            </button>
            <button type="button" wire:click="setDateFilter('this_month')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'this_month' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                This Month
            </button>
            <button type="button" wire:click="setDateFilter('all')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'all' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                All Time
            </button>
        </div>
    </div>

    <!-- Expense Total Headline Card -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <h2 class="font-extrabold text-sm sm:text-base text-[#2B1E16]">
                {{ match ($dateFilter) { 'this_week' => "This Week's Expenses", 'this_month' => "This Month's Expenses", 'all' => "All Time Expenses", default => "Today's Expenses"} }}
            </h2>
            <span
                class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                Operating Cost
            </span>
        </div>

        <div class="pt-3 overflow-hidden">
            <div class="text-2xl sm:text-3xl font-black text-[#DC2626] truncate">
                {{ $currency }}{{ number_format($totalExpensesAmount, 0) }}
            </div>
            <p class="text-xs text-[#8D7B70] mt-0.5 font-medium truncate">Total recorded in this time range.</p>
        </div>
    </div>

    <!-- Simple Category Spending Breakdown (Clean List) -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                <span>📋</span> Expense Categories
            </h3>
            <span class="text-xs text-[#8D7B70] font-medium">{{ $categoryBreakdown->count() }} categories</span>
        </div>

        <div class="space-y-2 pt-1">
            @forelse($categoryBreakdown as $cat)
                @php
                    $catLabel = match ($cat->category) {
                        'ingredients', 'raw_materials' => 'Ingredients',
                        'transportation', 'transport' => 'Transportation',
                        'packaging' => 'Packaging',
                        'gas' => 'Gas',
                        'utilities' => 'Utilities & Power',
                        'salaries' => 'Staff Salaries',
                        'rent' => 'Cart Rent',
                        default => 'Other / Miscellaneous',
                    };
                    $catEmoji = match ($cat->category) {
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
                <div
                    class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 flex items-center justify-between gap-2.5 text-xs sm:text-sm">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <span class="text-xl shrink-0">{{ $catEmoji }}</span>
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-[#2B1E16] truncate">{{ $catLabel }}</span>
                            <span class="text-[11px] text-[#8D7B70] ml-1 font-medium whitespace-nowrap">({{ $cat->count }} entries)</span>
                        </div>
                    </div>

                    <div class="font-black text-[#DC2626] text-xs sm:text-sm shrink-0">
                        {{ $currency }}{{ number_format($cat->total_amount, 0) }}
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] py-6 text-center">No expenses recorded for this period.</p>
            @endforelse
        </div>

        <!-- Prominent + Add Expense Trigger -->
        <div class="pt-2">
            <button type="button" wire:click="openAddModal"
                class="w-full py-3 bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] text-[#F26522] font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer">
                <span>+</span> Add Expense
            </button>
        </div>
    </div>

    <!-- Individual Expense Records List -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                <span>📝</span> Recent Logged Expenses
            </h3>
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="🔍 Search..."
                class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3 py-1.5 text-xs text-[#2B1E16] placeholder-[#8D7B70] focus:outline-none focus:ring-2 focus:ring-[#F26522] w-full sm:w-48 font-medium">
        </div>

        <div class="space-y-2.5">
            @forelse($expenses as $expense)
                <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5 flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-xs sm:text-sm text-[#2B1E16] truncate">{{ $expense->title }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white border border-[#EFE7DE] text-[#554338] shrink-0">
                                    {{ $expense->category_label }}
                                </span>
                            </div>
                            <p class="text-[11px] text-[#8D7B70] mt-0.5 font-medium truncate">
                                {{ $expense->expense_date->format('d M Y') }} • Logged by: <strong
                                    class="text-[#2B1E16]">{{ $expense->user->name }}</strong>
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-sm sm:text-base font-black text-[#DC2626]">
                                {{ $currency }}{{ number_format($expense->amount, 0) }}
                            </span>
                        </div>
                    </div>

                    @if($expense->notes)
                        <p class="text-xs text-[#554338] bg-white p-2 rounded-xl border border-[#EFE7DE] break-words">
                            {{ $expense->notes }}
                        </p>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-[#EFE7DE] text-xs">
                        <button type="button" wire:click="editExpense({{ $expense->id }})"
                            class="px-3 py-1 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] rounded-lg font-bold cursor-pointer">
                            Edit
                        </button>
                        <button type="button" wire:click="deleteExpense({{ $expense->id }})"
                            wire:confirm="Delete this expense record?"
                            class="px-3 py-1 bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA] rounded-lg font-bold cursor-pointer">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] text-center py-6">No individual expense logs found.</p>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Short Add/Edit Expense Modal Form -->
    @if($showExpenseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">💸</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingExpenseId ? 'Edit Expense' : 'Add Expense' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showExpenseModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveExpense" class="space-y-4">
                    <!-- Category Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Category</label>
                        <select wire:model="category"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
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
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Amount ({{ $currency }})</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-lg font-bold text-[#8D7B70]">{{ $currency }}</span>
                            <input type="number" step="0.01" wire:model="amount" placeholder="0"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl pl-9 pr-4 py-3 text-lg text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                        </div>
                        @error('amount') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Note (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Note (Optional)</label>
                        <input type="text" wire:model="notes" placeholder="e.g. 50 buns, rickshaw fare, cylinder"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] placeholder-[#8D7B70] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showExpenseModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>