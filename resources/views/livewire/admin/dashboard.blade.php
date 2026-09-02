<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Top Greeting & Real-time Indicator -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                {{ $greeting }}
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 mt-0.5">
                {{ now()->format('l, d F Y') }} • {{ \App\Models\CartSetting::cartName() }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($currentBusinessDay && $currentBusinessDay->isOpen())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Cart Open</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <span>Closed</span>
                </span>
            @endif
        </div>
    </div>

    <!-- 1. TODAY'S PERFORMANCE CARD (Core Smartphone Card) -->
    <div class="bg-gradient-to-br from-zinc-900 via-zinc-900 to-zinc-950 border {{ $isTodayProfit ? 'border-emerald-500/30' : 'border-rose-500/30' }} rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-zinc-800/80">
            <div class="flex items-center gap-2">
                <span class="text-xl">📅</span>
                <h2 class="text-base sm:text-lg font-black text-white uppercase tracking-wider">Today</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-zinc-800 text-zinc-300">
                {{ $todayOrdersCount }} orders
            </span>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 gap-4 py-4">
            <!-- Sales -->
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-3.5 flex flex-col justify-between">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Sales</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight mt-1">
                    {{ $currency }}{{ number_format($todaySales, 0) }}
                </div>
            </div>

            <!-- Expenses -->
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-3.5 flex flex-col justify-between">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Expenses</span>
                <div class="text-2xl sm:text-3xl font-black text-rose-400 tracking-tight mt-1">
                    {{ $currency }}{{ number_format($todayExpenses, 0) }}
                </div>
            </div>

            <!-- Profit / Loss -->
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-3.5 flex flex-col justify-between">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                    {{ $isTodayProfit ? 'Profit' : 'Loss' }}
                </span>
                <div class="text-2xl sm:text-3xl font-black {{ $isTodayProfit ? 'text-emerald-400' : 'text-rose-400' }} tracking-tight mt-1">
                    {{ $isTodayProfit ? '+' : '-' }}{{ $currency }}{{ number_format(abs($todayProfit), 0) }}
                </div>
            </div>

            <!-- Items Sold -->
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-3.5 flex flex-col justify-between">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Items Sold</span>
                <div class="text-2xl sm:text-3xl font-black text-amber-400 tracking-tight mt-1">
                    {{ number_format($todayItemsSold) }}
                </div>
            </div>
        </div>

        <!-- Profit / Loss Banner -->
        @if($isTodayProfit)
            <div class="mt-2 py-2.5 px-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-between text-xs sm:text-sm font-bold text-emerald-300">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span>🟢 You're making a profit today</span>
                </div>
                <span class="text-emerald-400 font-extrabold">+{{ $currency }}{{ number_format($todayProfit, 0) }}</span>
            </div>
        @else
            <div class="mt-2 py-2.5 px-4 rounded-xl bg-rose-500/15 border border-rose-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs sm:text-sm font-bold text-rose-300">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>🔴 Loss Today</span>
                </div>
                <div class="text-xs text-rose-300/90 flex items-center gap-2">
                    <span>Sales: {{ $currency }}{{ number_format($todaySales, 0) }}</span>
                    <span>•</span>
                    <span>Expenses: {{ $currency }}{{ number_format($todayExpenses, 0) }}</span>
                    <span>•</span>
                    <span class="text-rose-400 font-black">Loss: {{ $currency }}{{ number_format($todayLoss, 0) }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- 2. QUICK ACTIONS (2x2 Grid of Large Touch Buttons) -->
    <div class="space-y-2">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider px-1">
            Quick Actions
        </h3>

        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <!-- 1. View Sales -->
            <a href="{{ route('admin.sales') }}" 
               class="bg-zinc-900 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-amber-500/40 rounded-2xl p-4 sm:p-5 flex flex-col items-center justify-center text-center shadow-lg transition-all touch-press group">
                <span class="text-3xl sm:text-4xl group-hover:scale-110 transition-transform">🛒</span>
                <span class="font-bold text-sm sm:text-base text-zinc-100 mt-2">View Sales</span>
                <span class="text-[11px] text-zinc-500 mt-0.5">Today: {{ $currency }}{{ number_format($todaySales, 0) }}</span>
            </a>

            <!-- 2. Expenses -->
            <a href="{{ route('admin.expenses') }}" 
               class="bg-zinc-900 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-rose-500/40 rounded-2xl p-4 sm:p-5 flex flex-col items-center justify-center text-center shadow-lg transition-all touch-press group">
                <span class="text-3xl sm:text-4xl group-hover:scale-110 transition-transform">💸</span>
                <span class="font-bold text-sm sm:text-base text-zinc-100 mt-2">Expenses</span>
                <span class="text-[11px] text-zinc-500 mt-0.5">Today: {{ $currency }}{{ number_format($todayExpenses, 0) }}</span>
            </a>

            <!-- 3. Food Items -->
            <a href="{{ route('admin.products') }}" 
               class="bg-zinc-900 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-orange-500/40 rounded-2xl p-4 sm:p-5 flex flex-col items-center justify-center text-center shadow-lg transition-all touch-press group">
                <span class="text-3xl sm:text-4xl group-hover:scale-110 transition-transform">🍔</span>
                <span class="font-bold text-sm sm:text-base text-zinc-100 mt-2">Food Items</span>
                <span class="text-[11px] text-zinc-500 mt-0.5">Manage Menu & Prices</span>
            </a>

            <!-- 4. Reports -->
            <a href="{{ route('admin.reports') }}" 
               class="bg-zinc-900 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-blue-500/40 rounded-2xl p-4 sm:p-5 flex flex-col items-center justify-center text-center shadow-lg transition-all touch-press group">
                <span class="text-3xl sm:text-4xl group-hover:scale-110 transition-transform">📊</span>
                <span class="font-bold text-sm sm:text-base text-zinc-100 mt-2">Reports</span>
                <span class="text-[11px] text-zinc-500 mt-0.5">P&L & Monthly Summary</span>
            </a>
        </div>
    </div>

    <!-- 3. THIS MONTH'S PERFORMANCE CARD -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <div class="flex items-center gap-2">
                <span class="text-lg">📈</span>
                <h3 class="font-bold text-sm sm:text-base text-zinc-100">This Month</h3>
            </div>
            <span class="text-xs text-zinc-500">{{ now()->format('F Y') }}</span>
        </div>

        <div class="grid grid-cols-3 gap-3 pt-4 text-center sm:text-left">
            <!-- Month Sales -->
            <div class="bg-zinc-950/60 border border-zinc-800/60 rounded-2xl p-3">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block">Sales</span>
                <span class="text-base sm:text-xl font-black text-emerald-400 mt-0.5 block">
                    {{ $currency }}{{ number_format($monthSales, 0) }}
                </span>
            </div>

            <!-- Month Expenses -->
            <div class="bg-zinc-950/60 border border-zinc-800/60 rounded-2xl p-3">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block">Expenses</span>
                <span class="text-base sm:text-xl font-black text-rose-400 mt-0.5 block">
                    {{ $currency }}{{ number_format($monthExpenses, 0) }}
                </span>
            </div>

            <!-- Month Profit -->
            <div class="bg-zinc-950/60 border border-zinc-800/60 rounded-2xl p-3">
                <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block">Profit</span>
                <span class="text-base sm:text-xl font-black {{ $monthProfit >= 0 ? 'text-amber-400' : 'text-rose-400' }} mt-0.5 block">
                    {{ $monthProfit >= 0 ? '+' : '' }}{{ $currency }}{{ number_format($monthProfit, 0) }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4. BEST-SELLING ITEMS RANKING -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <h3 class="font-black text-base text-white">Best Sellers</h3>
            </div>
            <a href="{{ route('admin.sales') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                View All →
            </a>
        </div>

        <div class="space-y-2 pt-1">
            @forelse($bestSellingItems as $item)
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                    <div class="flex items-center gap-3">
                        <span class="w-6 text-center font-black text-amber-400 text-sm">
                            {{ $loop->iteration }}.
                        </span>
                        <span class="text-xl">
                            {{ $item->product?->image_emoji ?? '🍔' }}
                        </span>
                        <div>
                            <span class="font-bold text-zinc-100">{{ $item->product_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-right shrink-0">
                        <span class="font-bold text-zinc-400">{{ $item->total_qty }} sold</span>
                        <span class="font-black text-emerald-400">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-500 py-4 text-center">No sales recorded yet.</p>
            @endforelse
        </div>
    </div>

    <!-- 5. CART SHIFT & TIMINGS (Opening Time & Closing Time) -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }} flex items-center justify-center text-lg shrink-0">
                    {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? '🟢' : '🔴' }}
                </div>
                <div>
                    <h4 class="font-black text-sm sm:text-base text-white flex items-center gap-2">
                        <span>Cart Shift Status:</span>
                        <span class="{{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'Open (Active)' : 'Closed' }}
                        </span>
                    </h4>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Today's recorded operational hours</p>
                </div>
            </div>

            <div>
                @if($currentBusinessDay && $currentBusinessDay->isOpen())
                    <button type="button" 
                            wire:click="$set('showDayModal', true)"
                            class="px-3.5 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 rounded-xl text-xs font-bold transition-all cursor-pointer touch-press">
                        Close Shift
                    </button>
                @else
                    <button type="button" 
                            wire:click="reopenBusinessDay"
                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 rounded-xl text-xs font-black transition-all cursor-pointer touch-press shadow-md shadow-emerald-600/20">
                        Turn ON (Reopen)
                    </button>
                @endif
            </div>
        </div>

        <!-- Opening Time & Closing Time Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <!-- Opening Time Box -->
            <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3.5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block">Opening Time</span>
                    <span class="text-base sm:text-lg font-black text-emerald-400 mt-0.5 block">
                        {{ $currentBusinessDay?->opened_at ? $currentBusinessDay->opened_at->format('h:i A') : 'Not Opened' }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-0.5 block">
                        @if($currentBusinessDay?->openedBy)
                            By <strong class="text-zinc-200">{{ $currentBusinessDay->openedBy->name }}</strong>
                        @else
                            System / Default
                        @endif
                    </span>
                </div>
                <div class="text-2xl">
                    🌅
                </div>
            </div>

            <!-- Closing Time Box -->
            <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3.5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block">Closing Time</span>
                    <span class="text-base sm:text-lg font-black {{ $currentBusinessDay?->closed_at ? 'text-rose-400' : 'text-amber-400' }} mt-0.5 block">
                        {{ $currentBusinessDay?->closed_at ? $currentBusinessDay->closed_at->format('h:i A') : 'Currently Online' }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-0.5 block">
                        @if($currentBusinessDay?->closed_at && $currentBusinessDay?->closedBy)
                            By <strong class="text-zinc-200">{{ $currentBusinessDay->closedBy->name }}</strong>
                        @elseif($currentBusinessDay?->isOpen())
                            Shift in progress
                        @else
                            Closed
                        @endif
                    </span>
                </div>
                <div class="text-2xl">
                    🌙
                </div>
            </div>
        </div>
    </div>

    <!-- Close Business Day Modal -->
    @if($showDayModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🔒</span>
                        <h3 class="font-bold text-base text-white">Close Today's Business Day</h3>
                    </div>
                    <button type="button" wire:click="$set('showDayModal', false)" class="text-zinc-400 hover:text-white">✕</button>
                </div>

                <form wire:submit="closeBusinessDay" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Final Cash in Register ({{ $currency }})</label>
                        <input type="number" 
                               step="0.01" 
                               wire:model="closingCashAmount" 
                               placeholder="e.g. 8500" 
                               class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2.5 text-base text-white font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none" 
                               required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Closing Notes</label>
                        <textarea wire:model="dayNotes" rows="2" placeholder="Shift notes or remarks..." class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-800">
                        <button type="button" wire:click="$set('showDayModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white bg-zinc-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500">
                            Confirm & Close Day
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
