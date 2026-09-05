<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div>
            <h1
                class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-1.5 sm:gap-2">
                <span>📊</span> Business Reports
            </h1>
            <p class="text-xs text-[#8D7B70] mt-0.5 font-medium">Clear overview of sales, expenses, and profitability.
            </p>
        </div>

        <button type="button" wire:click="exportReportCsv"
            class="px-3.5 py-2 bg-white hover:bg-[#F8F3EA] text-[#2B1E16] border border-[#EFE7DE] rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-2xs cursor-pointer touch-press">
            <span>📥</span> <span class="hidden sm:inline">Export CSV</span>
        </button>
    </div>

    <!-- 1. THREE SIMPLE SUMMARY SECTIONS (Today, This Week, This Month) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <!-- 1. Today Card -->
        <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-[#EFE7DE]">
                <h2 class="font-extrabold text-xs sm:text-sm text-[#2B1E16] flex items-center gap-1.5">
                    <span>📅</span> Today
                </h2>
                <span
                    class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $todayProfit >= 0 ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                    {{ $todayProfit >= 0 ? 'Profit' : 'Loss' }}
                </span>
            </div>

            <div class="space-y-1.5 pt-2.5 text-xs">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-[#8D7B70] font-medium">Sales:</span>
                    <span
                        class="font-extrabold text-[#1E8E3E]">{{ $currency }}{{ number_format($todaySales, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-[#8D7B70] font-medium">Expenses:</span>
                    <span
                        class="font-extrabold text-[#DC2626]">{{ $currency }}{{ number_format($todayExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-[#EFE7DE] font-black text-xs">
                    <span class="text-[#2B1E16]">Profit:</span>
                    <span class="{{ $todayProfit >= 0 ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }} text-sm font-black">
                        {{ $todayProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($todayProfit), 0) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. This Week Card -->
        <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-[#EFE7DE]">
                <h2 class="font-extrabold text-xs sm:text-sm text-[#2B1E16] flex items-center gap-1.5">
                    <span>📆</span> This Week
                </h2>
                <span
                    class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $weekProfit >= 0 ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                    {{ $weekProfit >= 0 ? 'Profit' : 'Loss' }}
                </span>
            </div>

            <div class="space-y-1.5 pt-2.5 text-xs">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-[#8D7B70] font-medium">Sales:</span>
                    <span class="font-extrabold text-[#1E8E3E]">{{ $currency }}{{ number_format($weekSales, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-[#8D7B70] font-medium">Expenses:</span>
                    <span
                        class="font-extrabold text-[#DC2626]">{{ $currency }}{{ number_format($weekExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-[#EFE7DE] font-black text-xs">
                    <span class="text-[#2B1E16]">Profit:</span>
                    <span class="{{ $weekProfit >= 0 ? 'text-[#F26522]' : 'text-[#DC2626]' }} text-sm font-black">
                        {{ $weekProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($weekProfit), 0) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. This Month Card -->
        <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-[#EFE7DE]">
                <h2 class="font-extrabold text-xs sm:text-sm text-[#2B1E16] flex items-center gap-1.5">
                    <span>📈</span> This Month
                </h2>
                <span
                    class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $monthProfit >= 0 ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                    {{ $monthProfit >= 0 ? 'Profit' : 'Loss' }}
                </span>
            </div>

            <div class="space-y-1.5 pt-2.5 text-xs">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-[#8D7B70] font-medium">Sales:</span>
                    <span
                        class="font-extrabold text-[#1E8E3E]">{{ $currency }}{{ number_format($monthSales, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-[#8D7B70] font-medium">Expenses:</span>
                    <span
                        class="font-extrabold text-[#DC2626]">{{ $currency }}{{ number_format($monthExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-[#EFE7DE] font-black text-xs">
                    <span class="text-[#2B1E16]">Profit:</span>
                    <span class="{{ $monthProfit >= 0 ? 'text-[#F26522]' : 'text-[#DC2626]' }} text-sm font-black">
                        {{ $monthProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($monthProfit), 0) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. BEST-SELLING ITEMS RANKING -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                <span class="text-base sm:text-lg">🏆</span>
                <h3 class="font-extrabold text-xs sm:text-sm text-[#2B1E16]">Best Sellers</h3>
            </div>
            <span class="text-[10px] sm:text-xs text-[#8D7B70] font-medium">Ranked by units sold</span>
        </div>

        <div class="space-y-1.5 pt-1">
            @forelse($bestSellingItems as $item)
                <div
                    class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 sm:p-3 flex items-center justify-between gap-2.5 text-xs">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="w-4 text-center font-black text-[#F26522] text-xs shrink-0">
                            {{ $loop->iteration }}.
                        </span>
                        <div class="w-6 h-6 rounded-lg overflow-hidden shrink-0 flex items-center justify-center bg-white border border-[#EFE7DE]">
                            @if($item->product?->image_url)
                                <img src="{{ $item->product->image_url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm select-none">{{ $item->product?->image_emoji ?? '🍔' }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-[#2B1E16] text-xs truncate block">{{ $item->product_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 text-right shrink-0">
                        <span class="font-semibold text-[#8D7B70] text-xs whitespace-nowrap">{{ $item->total_qty }}
                            sold</span>
                        <span
                            class="font-black text-[#1E8E3E] text-xs whitespace-nowrap">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] py-3 text-center">No sales recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>