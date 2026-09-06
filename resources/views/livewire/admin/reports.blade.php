<div class="space-y-6 max-w-4xl mx-auto pb-10">

    <!-- Header & Date Navigation Bar -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#EFE7DE]">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2">
                    <span>📊</span> Daily Activity Report
                </h1>
                <p class="text-xs text-[#8D7B70] font-medium">Complete record of sales, operating expenses, and assets for any chosen date.</p>
            </div>

            <button type="button" wire:click="exportReportCsv"
                class="px-3.5 py-2 bg-[#F8F3EA] hover:bg-[#EFE7DE] text-[#2B1E16] border border-[#EFE7DE] rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer touch-press shrink-0">
                <span>📥</span> <span>Export CSV</span>
            </button>
        </div>

        <!-- Date Selector Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pt-1">
            <!-- Prev / Today / Next Quick Controls -->
            <div class="flex items-center gap-1.5">
                <button type="button" wire:click="goToPreviousDay"
                    class="px-3 py-2 bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] rounded-xl text-xs font-bold text-[#554338] hover:text-[#2B1E16] transition-colors cursor-pointer touch-press flex items-center gap-1">
                    <span>◀</span> <span class="hidden sm:inline">Previous</span>
                </button>

                <button type="button" wire:click="goToToday"
                    class="px-3 py-2 {{ $selectedDate === \Carbon\Carbon::today()->toDateString() ? 'bg-[#F26522] text-white font-black' : 'bg-[#F8F3EA] text-[#554338] hover:text-[#2B1E16]' }} border border-[#EFE7DE] rounded-xl text-xs font-bold transition-all cursor-pointer touch-press">
                    Today
                </button>

                <button type="button" wire:click="goToNextDay"
                    class="px-3 py-2 bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] rounded-xl text-xs font-bold text-[#554338] hover:text-[#2B1E16] transition-colors cursor-pointer touch-press flex items-center gap-1">
                    <span class="hidden sm:inline">Next</span> <span>▶</span>
                </button>
            </div>

            <!-- Date Picker & Formatted Banner -->
            <div class="flex flex-wrap items-center gap-2 flex-1 sm:justify-end">
                <div class="px-2.5 py-1.5 rounded-xl bg-[#EAF7EE] border border-[#CDEED5] text-[11px] font-bold text-[#1E8E3E] flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#1E8E3E] animate-pulse"></span>
                    <span>Today: <strong>{{ $currency }}{{ number_format($todaySalesAmount, 0) }}</strong> • <strong>{{ $todayItemsSold }}</strong> {{ $todayItemsSold === 1 ? 'item' : 'items' }} sold</span>
                </div>

                <span class="text-xs font-bold text-[#2B1E16] hidden md:inline">
                    {{ $dateCarbon->format('l, d F Y') }}
                </span>
                <input type="date" wire:model.live="selectedDate"
                    class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3 py-2 text-xs sm:text-sm font-bold text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 1: SALES SECTION                                                  -->
    <!-- ========================================================================= -->
    <section class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-4">
        <!-- Section Header -->
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="text-xl">🛒</span>
                <div>
                    <h2 class="font-extrabold text-base sm:text-lg text-[#2B1E16]">
                        Sales Records
                    </h2>
                    <p class="text-[11px] text-[#8D7B70] font-medium">All completed customer sales transactions on {{ $dateCarbon->format('d M Y') }}.</p>
                </div>
            </div>

            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]">
                {{ $sales->count() }} {{ $sales->count() === 1 ? 'sale' : 'sales' }}
            </span>
        </div>

        <!-- Sales Highlights: Total Sales & Total Items Sold -->
        <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider">
                        {{ $isToday ? "Today's Total Sales" : "Total Sales (" . $dateCarbon->format('d M') . ")" }}
                    </span>
                    <span class="text-sm">🛒</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#1E8E3E] mt-1 tracking-tight">
                    {{ $currency }}{{ number_format($totalSalesAmount, 0) }}
                </div>
                <span class="text-[10px] text-[#8D7B70] mt-0.5 font-medium">
                    {{ $sales->count() }} {{ $sales->count() === 1 ? 'transaction' : 'transactions' }}
                </span>
            </div>

            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider">
                        {{ $isToday ? "Today's Items Sold" : "Items Sold (" . $dateCarbon->format('d M') . ")" }}
                    </span>
                    <span class="text-sm">📦</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#F26522] mt-1 tracking-tight">
                    {{ number_format($totalItemsSold) }}
                </div>
                <span class="text-[10px] text-[#8D7B70] mt-0.5 font-medium">
                    {{ $totalItemsSold === 1 ? 'item sold' : 'total items sold' }}
                </span>
            </div>
        </div>

        <!-- Payment Methods Breakdown: Cash, bKash, Nagad -->
        <div class="grid grid-cols-3 gap-2 sm:gap-3">
            <!-- Cash -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 sm:p-3 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider">Cash</span>
                    <span class="text-xs sm:text-sm">💵</span>
                </div>
                <div class="text-base sm:text-lg font-black text-[#2B1E16] mt-1">
                    {{ $cashCount }} <span class="text-xs font-semibold text-[#8D7B70]">{{ $cashCount === 1 ? 'sale' : 'sales' }}</span>
                </div>
                <span class="text-[11px] font-bold text-[#1E8E3E] mt-0.5 block truncate">
                    {{ $currency }}{{ number_format($cashAmount, 0) }}
                </span>
            </div>

            <!-- bKash -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 sm:p-3 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider">bKash</span>
                    <span class="text-xs sm:text-sm">📱</span>
                </div>
                <div class="text-base sm:text-lg font-black text-[#D12053] mt-1">
                    {{ $bkashCount }} <span class="text-xs font-semibold text-[#8D7B70]">{{ $bkashCount === 1 ? 'sale' : 'sales' }}</span>
                </div>
                <span class="text-[11px] font-bold text-[#D12053] mt-0.5 block truncate">
                    {{ $currency }}{{ number_format($bkashAmount, 0) }}
                </span>
            </div>

            <!-- Nagad -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 sm:p-3 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider">Nagad</span>
                    <span class="text-xs sm:text-sm">📲</span>
                </div>
                <div class="text-base sm:text-lg font-black text-[#F97316] mt-1">
                    {{ $nagadCount }} <span class="text-xs font-semibold text-[#8D7B70]">{{ $nagadCount === 1 ? 'sale' : 'sales' }}</span>
                </div>
                <span class="text-[11px] font-bold text-[#F97316] mt-0.5 block truncate">
                    {{ $currency }}{{ number_format($nagadAmount, 0) }}
                </span>
            </div>
        </div>

        @if($sales->isEmpty())
            <div class="py-8 text-center bg-[#F8F3EA]/60 rounded-2xl border border-dashed border-[#EFE7DE] p-4">
                <span class="text-2xl block mb-1">🛒</span>
                <p class="text-xs sm:text-sm font-bold text-[#2B1E16]">No sales recorded on this date.</p>
                <p class="text-xs text-[#8D7B70]">Transactions completed via POS will appear here.</p>
            </div>
        @else
            <!-- Records List -->
            <div class="space-y-3">
                @foreach($sales as $sale)
                    <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5 space-y-2.5">
                        <!-- Top Row: Invoice, Payment Method, Time, and Total Amount -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono font-bold text-xs text-[#2B1E16]">{{ $sale->invoice_no }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-white border border-[#EFE7DE] text-[#554338]">
                                        {{ strtoupper($sale->payment_method) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-[#8D7B70] font-medium">
                                    ⏰ {{ $sale->created_at?->format('h:i A') }} • Staff: <strong class="text-[#2B1E16]">{{ $sale->user?->name ?? 'Staff' }}</strong>
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-base sm:text-lg font-black text-[#1E8E3E]">
                                    {{ $currency }}{{ number_format($sale->total_amount, 0) }}
                                </span>
                            </div>
                        </div>

                        <!-- Item details breakdown: item name, quantity, price/amount -->
                        <div class="bg-white rounded-xl p-2.5 border border-[#EFE7DE] space-y-1 text-xs">
                            @foreach($sale->items as $item)
                                <div class="flex items-center justify-between text-[#554338]">
                                    <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                        <span class="font-bold text-[#2B1E16] truncate">{{ $item->product_name }}</span>
                                        <span class="text-[#F26522] font-black shrink-0">×{{ $item->quantity }}</span>
                                    </div>
                                    <span class="font-bold text-[#2B1E16] shrink-0">
                                        {{ $currency }}{{ number_format($item->subtotal, 0) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total Sales at the Bottom of Sales Section -->
            <div class="pt-4 border-t-2 border-[#EFE7DE] bg-[#F8F3EA] p-4 rounded-2xl flex flex-col gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🛒</span>
                        <span class="font-extrabold text-sm sm:text-base text-[#2B1E16]">Total Sales:</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-white border border-[#EFE7DE] text-[#554338]">
                            {{ $totalItemsSold }} {{ $totalItemsSold === 1 ? 'item' : 'items' }} sold
                        </span>
                    </div>

                    <div class="text-xl sm:text-2xl font-black text-[#1E8E3E]">
                        {{ $currency }}{{ number_format($totalSalesAmount, 0) }}
                    </div>
                </div>

                <!-- Payment Methods Breakdown Summary -->
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-[#EFE7DE] text-xs">
                    <span class="text-[#8D7B70] font-bold text-[11px] uppercase tracking-wider">Methods:</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-[#EFE7DE] font-bold text-[#2B1E16]">
                        💵 Cash: <strong>{{ $cashCount }}</strong> ({{ $currency }}{{ number_format($cashAmount, 0) }})
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-[#EFE7DE] font-bold text-[#D12053]">
                        📱 bKash: <strong>{{ $bkashCount }}</strong> ({{ $currency }}{{ number_format($bkashAmount, 0) }})
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-[#EFE7DE] font-bold text-[#F97316]">
                        📲 Nagad: <strong>{{ $nagadCount }}</strong> ({{ $currency }}{{ number_format($nagadAmount, 0) }})
                    </span>
                    @if($cardCount > 0)
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-[#EFE7DE] font-bold text-[#2563EB]">
                            💳 Card: <strong>{{ $cardCount }}</strong> ({{ $currency }}{{ number_format($cardAmount, 0) }})
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 2: EXPENSES SECTION                                               -->
    <!-- ========================================================================= -->
    <section class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-4">
        <!-- Section Header -->
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="text-xl">💸</span>
                <div>
                    <h2 class="font-extrabold text-base sm:text-lg text-[#2B1E16]">
                        Expenses
                    </h2>
                    <p class="text-[11px] text-[#8D7B70] font-medium">All operating costs recorded on {{ $dateCarbon->format('d M Y') }}.</p>
                </div>
            </div>

            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                {{ $expenses->count() }} {{ $expenses->count() === 1 ? 'expense' : 'expenses' }}
            </span>
        </div>

        @if($expenses->isEmpty())
            <div class="py-8 text-center bg-[#F8F3EA]/60 rounded-2xl border border-dashed border-[#EFE7DE] p-4">
                <span class="text-2xl block mb-1">💸</span>
                <p class="text-xs sm:text-sm font-bold text-[#2B1E16]">No expenses recorded on this date.</p>
                <p class="text-xs text-[#8D7B70]">Operating costs added on the Expense page will appear here.</p>
            </div>
        @else
            <!-- Records List -->
            <div class="space-y-2.5">
                @foreach($expenses as $expense)
                    <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="font-bold text-xs sm:text-sm text-[#2B1E16] truncate">{{ $expense->title }}</h4>
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
                                <span class="text-base sm:text-lg font-black text-[#DC2626]">
                                    {{ $currency }}{{ number_format($expense->amount, 0) }}
                                </span>
                            </div>
                        </div>

                        @if($expense->notes)
                            <p class="text-xs text-[#554338] bg-white p-2 rounded-xl border border-[#EFE7DE] break-words">
                                {{ $expense->notes }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Total Expenses at the Bottom of Expenses Section -->
            <div class="pt-4 border-t-2 border-[#EFE7DE] bg-[#F8F3EA] p-4 rounded-2xl flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-base">💸</span>
                    <span class="font-extrabold text-sm sm:text-base text-[#2B1E16]">Total Expenses:</span>
                </div>

                <div class="text-xl sm:text-2xl font-black text-[#DC2626]">
                    {{ $currency }}{{ number_format($totalExpensesAmount, 0) }}
                </div>
            </div>
        @endif
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 3: ASSETS SECTION                                                 -->
    <!-- ========================================================================= -->
    <section class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-4">
        <!-- Section Header -->
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="text-xl">🟢</span>
                <div>
                    <h2 class="font-extrabold text-base sm:text-lg text-[#2B1E16]">
                        Assets Added
                    </h2>
                    <p class="text-[11px] text-[#8D7B70] font-medium">Business asset investments acquired on {{ $dateCarbon->format('d M Y') }} (kept strictly separate from expenses).</p>
                </div>
            </div>

            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]">
                {{ $assets->count() }} {{ $assets->count() === 1 ? 'asset' : 'assets' }}
            </span>
        </div>

        @if($assets->isEmpty())
            <div class="py-8 text-center bg-[#F8F3EA]/60 rounded-2xl border border-dashed border-[#EFE7DE] p-4">
                <span class="text-2xl block mb-1">🟢</span>
                <p class="text-xs sm:text-sm font-bold text-[#2B1E16]">No assets added on this date.</p>
                <p class="text-xs text-[#8D7B70]">Assets recorded on the Assets & Liabilities page will appear here.</p>
            </div>
        @else
            <!-- Records List -->
            <div class="space-y-2.5">
                @foreach($assets as $asset)
                    <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#1E8E3E] shrink-0"></span>
                                <h4 class="font-bold text-xs sm:text-sm text-[#2B1E16] truncate">{{ $asset->name }}</h4>
                            </div>

                            <p class="text-[11px] text-[#8D7B70] font-medium flex items-center gap-2 flex-wrap">
                                <span>📅 {{ $asset->formatted_date }}</span>
                                <span>⏰ {{ $asset->formatted_time }}</span>
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-base sm:text-lg font-black text-[#1E8E3E]">
                                {{ $currency }}{{ number_format($asset->amount, 0) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total Assets at the Bottom of Assets Section -->
            <div class="pt-4 border-t-2 border-[#EFE7DE] bg-[#F8F3EA] p-4 rounded-2xl flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-base">🟢</span>
                    <span class="font-extrabold text-sm sm:text-base text-[#2B1E16]">Total Assets Added:</span>
                </div>

                <div class="text-xl sm:text-2xl font-black text-[#1E8E3E]">
                    {{ $currency }}{{ number_format($totalAssetsAdded, 0) }}
                </div>
            </div>
        @endif
    </section>

</div>