<div class="space-y-3 sm:space-y-4">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between gap-2">
        <div>
            <h2 class="text-base sm:text-lg font-black text-[#2B1E16] flex items-center gap-2">
                <span>💸</span>
                <span>{{ seller_trans('expenses') }}</span>
            </h2>
            <p class="text-[11px] sm:text-xs text-[#8D7B70] font-medium">
                {{ bn_date(now(), $locale, 'D, d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('seller.quick-sell') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#554338] bg-white border border-[#EFE7DE] hover:bg-[#F8F3EA] shadow-2xs transition-all touch-press">
                <span>🛒</span>
                <span class="hidden sm:inline">{{ seller_trans('back_to_quick_sell') }}</span>
                <span class="sm:hidden">POS</span>
            </a>

            <button type="button" wire:click="openAddModal"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-extrabold text-white bg-[#F26522] hover:bg-[#D95314] shadow-xs transition-all touch-press cursor-pointer">
                <span>➕</span>
                <span>{{ seller_trans('add_expense') }}</span>
            </button>
        </div>
    </div>

    <!-- Today's Total Expenses Dynamic Hero Card -->
    <div
        class="p-4 sm:p-5 rounded-3xl bg-linear-to-br from-[#FFF5ED] via-[#FFF0E6] to-[#FFE6D5] border border-[#FED7AA] shadow-xs relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 text-7xl opacity-10 pointer-events-none select-none">💸</div>

        <div class="flex items-center justify-between gap-3 relative z-10">
            <div>
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-[#C2410C]">
                    {{ seller_trans('today_expenses') }}
                </span>
                <div
                    class="text-2xl sm:text-3xl md:text-4xl font-black text-[#2B1E16] mt-0.5 tracking-tight flex items-baseline gap-1">
                    <span>{{ bn_curr($todayTotal, $locale) }}</span>
                </div>
                <p class="text-[11px] sm:text-xs text-[#8D7B70] mt-1 font-medium flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#EA580C] animate-pulse"></span>
                    <span>{{ bn_num($todayCount, $locale) }}
                        {{ app()->getLocale() === 'bn' ? 'টি খরচ রেকর্ড হয়েছে' : 'expenses recorded today' }}</span>
                </p>
            </div>

            <button type="button" wire:click="openAddModal"
                class="hidden sm:flex flex-col items-center justify-center p-3 rounded-2xl bg-white/90 hover:bg-white border border-[#FED7AA] shadow-2xs hover:shadow-xs transition-all touch-press cursor-pointer text-center">
                <span class="text-xl">➕</span>
                <span class="text-[11px] font-bold text-[#F26522] mt-0.5">{{ seller_trans('add_expense') }}</span>
            </button>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-2.5 sm:p-3 rounded-2xl border border-[#EFE7DE] shadow-2xs space-y-2">
        <div class="flex items-center gap-2">
            <!-- Date Filter Toggle (Today / All) -->
            <div class="inline-flex bg-[#F8F3EA] p-1 rounded-xl border border-[#EFE7DE] shrink-0">
                <button type="button" wire:click="setDateFilter('today')"
                    class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $dateFilter === 'today' ? 'bg-[#F26522] text-white shadow-2xs font-extrabold' : 'text-[#554338] hover:text-[#2B1E16]' }}">
                    {{ app()->getLocale() === 'bn' ? 'আজ' : 'Today' }}
                </button>
                <button type="button" wire:click="setDateFilter('all')"
                    class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $dateFilter === 'all' ? 'bg-[#F26522] text-white shadow-2xs font-extrabold' : 'text-[#554338] hover:text-[#2B1E16]' }}">
                    {{ app()->getLocale() === 'bn' ? 'সব' : 'All' }}
                </button>
            </div>

            <!-- Search Input -->
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#8D7B70]">
                    🔍
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ app()->getLocale() === 'bn' ? 'খরচের বিবরণ দিয়ে খুঁজুন...' : 'Search expenses by description...' }}"
                    class="w-full pl-9 pr-3 py-1.5 rounded-xl text-xs bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] placeholder-[#8D7B70] focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#F26522]/30 focus:border-[#F26522] transition-all">
            </div>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="space-y-2.5">
        @forelse($expenses as $expense)
            <div wire:key="expense-{{ $expense->id }}"
                class="bg-white p-3.5 sm:p-4 rounded-2xl border border-[#EFE7DE] shadow-2xs hover:border-[#FED7AA] transition-all flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-xl bg-[#FFF0E6] text-[#F26522] flex items-center justify-center text-xs shrink-0">
                            🧾
                        </span>
                        <h4 class="font-bold text-sm text-[#2B1E16] truncate">
                            {{ $expense->description ?: $expense->title }}
                        </h4>
                    </div>

                    <div class="text-[11px] text-[#8D7B70] mt-1 pl-9 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                        <span>{{ bn_time($expense->created_at, $locale) }}</span>
                        <span>•</span>
                        <span>{{ bn_date($expense->expense_date, $locale, 'd M Y') }}</span>
                        @if($expense->user)
                            <span>•</span>
                            <span class="font-medium text-[#554338]">{{ $expense->user->name }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <!-- Amount Badge -->
                    <div
                        class="px-3 py-1.5 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] font-black text-sm sm:text-base">
                        {{ bn_curr($expense->amount, $locale) }}
                    </div>

                    <!-- Edit Button -->
                    <button type="button" wire:click="editExpense({{ $expense->id }})"
                        class="p-2 rounded-xl text-[#8D7B70] hover:text-[#2B1E16] hover:bg-[#F8F3EA] border border-transparent hover:border-[#EFE7DE] transition-all touch-press cursor-pointer"
                        title="{{ seller_trans('edit_expense') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>

                    <!-- Delete Button -->
                    <button type="button" wire:click="deleteExpense({{ $expense->id }})"
                        wire:confirm="{{ seller_trans('delete_expense_confirm') }}"
                        class="p-2 rounded-xl text-[#8D7B70] hover:text-[#DC2626] hover:bg-[#FEF2F2] border border-transparent hover:border-[#FECACA] transition-all touch-press cursor-pointer"
                        title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-[#EFE7DE] shadow-2xs text-center">
                <div class="w-14 h-14 rounded-2xl bg-[#FFF0E6] text-2xl flex items-center justify-center mx-auto mb-3">
                    💸
                </div>
                <h4 class="font-extrabold text-base text-[#2B1E16]">
                    {{ seller_trans('no_expenses_recorded') }}
                </h4>
                <p class="text-xs text-[#8D7B70] mt-1 max-w-sm mx-auto">
                    {{ app()->getLocale() === 'bn' ? 'বাজার খরচ, পরিবহন, গ্যাস বা প্যাকেজিংয়ের খরচ সহজেই লিখে রাখুন।' : 'Log ingredients, packaging, gas, transport, or any quick expenses here.' }}
                </p>
                <button type="button" wire:click="openAddModal"
                    class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-[#F26522] hover:bg-[#D95314] shadow-xs transition-all touch-press cursor-pointer">
                    <span>➕</span>
                    <span>{{ seller_trans('add_expense') }}</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($expenses->hasPages())
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    @endif

    <!-- Add / Edit Expense Modal Dialog -->
    @if($showExpenseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" role="dialog"
            aria-modal="true">
            <div
                class="relative w-full max-w-md bg-white rounded-3xl border border-[#EFE7DE] shadow-2xl p-5 sm:p-6 space-y-4">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-8 h-8 rounded-xl bg-[#FFF0E6] text-[#F26522] flex items-center justify-center text-sm font-bold">
                            💸
                        </span>
                        <h3 class="text-base font-extrabold text-[#2B1E16]">
                            {{ $editingExpenseId ? seller_trans('edit_expense') : seller_trans('add_expense') }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeModal"
                        class="p-1.5 rounded-xl text-[#8D7B70] hover:text-[#2B1E16] hover:bg-[#F8F3EA] cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="saveExpense" class="space-y-4">
                    <!-- Description Field -->
                    <div class="space-y-1.5">
                        <label for="expense_description" class="block text-xs font-bold text-[#554338]">
                            {{ seller_trans('description') }} <span class="text-[#DC2626]">*</span>
                        </label>
                        <input type="text" id="expense_description" wire:model="description"
                            placeholder="{{ seller_trans('expense_description_placeholder') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] placeholder-[#8D7B70] focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#F26522]/30 focus:border-[#F26522] transition-all">
                        @error('description')
                            <p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount Field -->
                    <div class="space-y-1.5">
                        <label for="expense_amount" class="block text-xs font-bold text-[#554338]">
                            {{ seller_trans('amount') }} ({{ $currency }}) <span class="text-[#DC2626]">*</span>
                        </label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sm font-bold text-[#8D7B70]">
                                {{ $currency }}
                            </span>
                            <input type="number" step="any" min="0.5" id="expense_amount" wire:model="amount"
                                placeholder="0.00"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] placeholder-[#8D7B70] focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#F26522]/30 focus:border-[#F26522] transition-all">
                        </div>
                        @error('amount')
                            <p class="text-[11px] font-bold text-[#DC2626] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA] border border-[#EFE7DE] transition-all touch-press cursor-pointer">
                            {{ seller_trans('cancel') }}
                        </button>

                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-xl text-xs font-black text-white bg-[#F26522] hover:bg-[#D95314] shadow-xs transition-all touch-press cursor-pointer flex items-center gap-1.5">
                            <span wire:loading.remove wire:target="saveExpense">
                                {{ $editingExpenseId ? seller_trans('save') : seller_trans('save') }}
                            </span>
                            <span wire:loading wire:target="saveExpense">
                                {{ app()->getLocale() === 'bn' ? 'সংরক্ষণ হচ্ছে...' : 'Saving...' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>