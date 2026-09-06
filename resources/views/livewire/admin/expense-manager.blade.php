<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2">
                    <span>💸</span> Expenses & Business Register
                </h1>
                <p class="text-xs text-[#8D7B70] font-medium">Record cart operating expenses and close calculation periods for profit/loss tracking.</p>
            </div>

            <button type="button" wire:click="openAddModal"
                class="px-4 py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-2xs touch-press cursor-pointer shrink-0">
                <span class="text-base font-bold leading-none">+</span>
                <span>Add Expense</span>
            </button>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center bg-white p-1 rounded-2xl border border-[#EFE7DE] text-xs font-bold overflow-x-auto scrollbar-none gap-1 shadow-2xs">
            <button type="button" wire:click="setDateFilter('all')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'all' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                All Records
            </button>
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
        </div>
    </div>

    <!-- Running Business Register: Current Open Period Card -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1E8E3E] animate-pulse"></span>
                <h2 class="font-extrabold text-sm sm:text-base text-[#2B1E16]">
                    Current Open Period
                </h2>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]">
                    Active Register
                </span>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-xl bg-[#EAF7EE] border border-[#CDEED5] text-[11px] font-bold text-[#1E8E3E]">
                    Today: <strong>{{ $currency }}{{ number_format($todaySalesAmount, 0) }}</strong> ({{ $todayItemsSold }} sold)
                </span>
                <span class="text-xs text-[#8D7B70] font-medium hidden sm:inline">
                    @if($openPeriodStart)
                        Started: <strong class="text-[#2B1E16]">{{ $openPeriodStart->format('d M · h:i A') }}</strong>
                    @else
                        Started: <strong class="text-[#2B1E16]">Register Start</strong>
                    @endif
                </span>
            </div>
        </div>

        <!-- 3 Period KPI Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Period Sales -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 sm:p-3.5">
                <div class="flex items-center justify-between text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider mb-1">
                    <span>Period Sales</span>
                    <span>🛒</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#1E8E3E] truncate">
                    {{ $currency }}{{ number_format($runningSales, 0) }}
                </div>
                <span class="text-[11px] text-[#8D7B70] mt-0.5 block font-medium">
                    <strong class="text-[#2B1E16]">{{ $runningItemsSold }}</strong> {{ $runningItemsSold === 1 ? 'item sold' : 'items sold' }} • {{ $runningSalesCount }} {{ $runningSalesCount === 1 ? 'sale' : 'sales' }}
                </span>
                <div class="flex items-center gap-1.5 mt-1.5 text-[10px] font-bold text-[#554338] flex-wrap">
                    <span class="px-1.5 py-0.5 rounded bg-white border border-[#EFE7DE]">Cash: {{ $runningCashCount }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-white border border-[#EFE7DE] text-[#D12053]">bKash: {{ $runningBkashCount }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-white border border-[#EFE7DE] text-[#F97316]">Nagad: {{ $runningNagadCount }}</span>
                </div>
            </div>

            <!-- Period Expenses -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 sm:p-3.5">
                <div class="flex items-center justify-between text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider mb-1">
                    <span>Period Expenses</span>
                    <span>💸</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#DC2626] truncate">
                    {{ $currency }}{{ number_format($runningExpenses, 0) }}
                </div>
                <span class="text-[11px] text-[#8D7B70] mt-0.5 block font-medium">
                    {{ $runningExpensesCount }} {{ $runningExpensesCount === 1 ? 'expense' : 'expenses' }} recorded
                </span>
            </div>

            <!-- Running Profit / Loss -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 sm:p-3.5">
                <div class="flex items-center justify-between text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider mb-1">
                    <span>Period {{ $runningProfit >= 0 ? 'Profit' : 'Loss' }}</span>
                    <span>{{ $runningProfit >= 0 ? '📈' : '📉' }}</span>
                </div>
                <div class="text-xl sm:text-2xl font-black {{ $runningProfit >= 0 ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }} truncate">
                    {{ $runningProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($runningProfit), 0) }}
                </div>
                <span class="text-[11px] font-semibold {{ $runningProfit >= 0 ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }} mt-0.5 block">
                    {{ $runningProfit >= 0 ? 'Net Profit so far' : 'Net Loss so far' }}
                </span>
            </div>
        </div>

        <!-- Close & Calculate Action Trigger -->
        <div class="pt-2">
            <button type="button" wire:click="closeAndCalculate"
                wire:confirm="Close this period now? The system will calculate Sales, Expenses, and Profit/Loss, save a permanent closing record, and start a fresh open period."
                class="w-full py-3.5 bg-[#2B1E16] hover:bg-[#1A120D] text-white font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition-all touch-press cursor-pointer">
                <span class="text-base">🔒</span>
                <span>Close & Calculate Period</span>
            </button>
        </div>
    </div>

    <!-- Category Spending Breakdown (Clean List) -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                <span>📋</span> Expense Categories
            </h3>
            <span class="text-xs text-[#8D7B70] font-medium">All Time: {{ $currency }}{{ number_format($allTotalExpenses, 0) }}</span>
        </div>

        <!-- Prominent + Add Expense Trigger -->
        <div class="pt-1">
            <button type="button" wire:click="openAddModal"
                class="w-full py-3 bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] text-[#F26522] font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer">
                <span class="font-black text-base leading-none">+</span>
                <span>Add Expense</span>
            </button>
        </div>
    </div>

    <!-- Individual Expense Records List -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2 border-b border-[#EFE7DE]">
            <div>
                <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                    <span>📝</span> Expense Records
                </h3>
                <span class="text-[11px] text-[#8D7B70] font-medium">Total listed: {{ $currency }}{{ number_format($totalExpensesAmount, 0) }}</span>
            </div>

            <input type="text" wire:model.live.debounce.250ms="search" placeholder="🔍 Search expenses..."
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
                            <p class="text-[11px] text-[#8D7B70] mt-1 font-medium flex items-center gap-2 flex-wrap">
                                <span>📅 {{ $expense->formatted_date }}</span>
                                <span>⏰ {{ $expense->formatted_time }}</span>
                                <span>• Logged by: <strong class="text-[#2B1E16]">{{ $expense->user?->name ?? 'Admin' }}</strong></span>
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
                            class="px-3 py-1 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] rounded-lg font-bold cursor-pointer touch-press">
                            Edit
                        </button>
                        <button type="button" wire:click="deleteExpense({{ $expense->id }})"
                            wire:confirm="Delete this expense record ({{ $expense->title }})?"
                            class="px-3 py-1 bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA] rounded-lg font-bold cursor-pointer touch-press">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] text-center py-6">No expense records found.</p>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $expenses->links() }}
        </div>

        <!-- Prominent Bottom Close & Calculate Trigger -->
        <div class="pt-4 border-t-2 border-[#EFE7DE] flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#F8F3EA] p-4 rounded-2xl">
            <div class="space-y-0.5">
                <div class="font-extrabold text-sm text-[#2B1E16] flex items-center gap-1.5">
                    <span>🔒</span>
                    <span>Ready to Close this Period?</span>
                </div>
                <p class="text-xs text-[#8D7B70] font-medium">Calculates sales, expenses, and profit/loss since previous closing.</p>
            </div>

            <button type="button" wire:click="closeAndCalculate"
                wire:confirm="Close this period now? The system will calculate Sales, Expenses, and Profit/Loss, save a permanent closing record, and start a fresh open period."
                class="w-full sm:w-auto px-5 py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-2xs touch-press cursor-pointer">
                <span>🔒</span>
                <span>Close & Calculate</span>
            </button>
        </div>
    </div>

    <!-- Closing History Section -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <div class="flex items-center justify-between pb-2 border-b border-[#EFE7DE]">
            <div>
                <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                    <span>📜</span> Closing History
                </h3>
                <p class="text-xs text-[#8D7B70] font-medium">Permanent record of previous period calculations.</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#F8F3EA] text-[#554338] border border-[#EFE7DE]">
                {{ $closingHistory->count() }} {{ $closingHistory->count() === 1 ? 'close' : 'closes' }}
            </span>
        </div>

        @if($closingHistory->isEmpty())
            <div class="py-8 text-center bg-[#F8F3EA]/50 rounded-2xl border border-dashed border-[#EFE7DE] p-4 space-y-1">
                <span class="text-2xl block">🔒</span>
                <p class="text-xs font-bold text-[#2B1E16]">No closing records saved yet.</p>
                <p class="text-xs text-[#8D7B70]">Tap "Close & Calculate" when ready to close your first period.</p>
            </div>
        @else
            <div class="space-y-2.5">
                @foreach($closingHistory as $closing)
                    <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-[#FED7AA] transition-colors">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-black text-xs sm:text-sm text-[#2B1E16]">
                                    {{ $closing->formatted_closed_at }}
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $closing->is_profit ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                                    {{ $closing->is_profit ? 'Profit' : 'Loss' }}
                                </span>
                            </div>

                            <p class="text-xs text-[#554338] font-semibold flex items-center gap-2 flex-wrap">
                                <span>Sales <strong class="text-[#1E8E3E]">{{ $currency }}{{ number_format($closing->total_sales, 0) }}</strong></span>
                                <span class="text-[#8D7B70]">|</span>
                                <span>Expenses <strong class="text-[#DC2626]">{{ $currency }}{{ number_format($closing->total_expenses, 0) }}</strong></span>
                                <span class="text-[#8D7B70]">|</span>
                                <span>
                                    {{ $closing->is_profit ? 'Profit' : 'Loss' }}
                                    <strong class="{{ $closing->is_profit ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }}">
                                        {{ $currency }}{{ number_format(abs($closing->net_profit), 0) }}
                                    </strong>
                                </span>
                            </p>

                            <p class="text-[11px] text-[#8D7B70]">
                                Period: {{ $closing->formatted_period }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <button type="button" wire:click="viewClosingSummary({{ $closing->id }})"
                                class="px-3 py-1.5 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] rounded-xl text-xs font-bold transition-colors cursor-pointer touch-press">
                                View Summary
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
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
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer p-1">✕</button>
                </div>

                <form wire:submit="saveExpense" class="space-y-4">
                    <!-- Expense Name -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Expense Name</label>
                        <input type="text" wire:model="title" placeholder="e.g. 50 Buns, Rickshaw Fare, Gas Cylinder"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-sm text-[#2B1E16] placeholder-[#8D7B70] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('title') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Category</label>
                        <select wire:model="category"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
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
                            <span class="absolute left-4 top-2.5 text-lg font-bold text-[#8D7B70]">{{ $currency }}</span>
                            <input type="number" step="0.01" wire:model="amount" placeholder="0"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl pl-9 pr-4 py-2.5 text-lg text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                        </div>
                        @error('amount') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date & Time Row -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Date</label>
                            <input type="date" wire:model="expense_date"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                            @error('expense_date') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Time</label>
                            <input type="time" wire:model="expense_time"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                            @error('expense_time') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Note (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Note (Optional)</label>
                        <input type="text" wire:model="notes" placeholder="e.g. Receipt details or store location"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] placeholder-[#8D7B70] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showExpenseModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Closing Summary Modal (Shown immediately upon Close & Calculate) -->
    @if($showClosingSummary && $latestClosing)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🔒</span>
                        <div>
                            <h3 class="font-extrabold text-base text-[#2B1E16]">
                                Closing Summary
                            </h3>
                            <p class="text-[11px] text-[#8D7B70] font-medium">
                                {{ $latestClosing->formatted_closed_at }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showClosingSummary', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer p-1">✕</button>
                </div>

                <!-- Closing Metrics Card -->
                <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between text-sm py-1 border-b border-[#EFE7DE]">
                        <span class="text-[#554338] font-bold">Sales:</span>
                        <span class="font-black text-[#1E8E3E] text-base">
                            {{ $currency }}{{ number_format($latestClosing->total_sales, 0) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm py-1 border-b border-[#EFE7DE]">
                        <span class="text-[#554338] font-bold">Expenses:</span>
                        <span class="font-black text-[#DC2626] text-base">
                            {{ $currency }}{{ number_format($latestClosing->total_expenses, 0) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm pt-1">
                        <span class="font-extrabold text-[#2B1E16]">
                            {{ $latestClosing->is_profit ? 'Profit:' : 'Loss:' }}
                        </span>
                        <span class="font-black text-xl {{ $latestClosing->is_profit ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }}">
                            {{ $latestClosing->is_profit ? '+' : '-' }}{{ $currency }}{{ number_format(abs($latestClosing->net_profit), 0) }}
                        </span>
                    </div>
                </div>

                <div class="bg-[#EAF7EE] border border-[#CDEED5] rounded-xl p-3 text-xs text-[#1E8E3E] font-semibold space-y-1">
                    <p class="flex items-center gap-1.5 font-bold">
                        <span>✓</span>
                        <span>This closing is permanently saved in history.</span>
                    </p>
                    <p class="text-[11px] text-[#554338]">
                        A new open period has started. Future calculations will start from this closing timestamp.
                    </p>
                </div>

                <button type="button" wire:click="$set('showClosingSummary', false)"
                    class="w-full py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                    Continue to Open Period
                </button>
            </div>
        </div>
    @endif

</div>