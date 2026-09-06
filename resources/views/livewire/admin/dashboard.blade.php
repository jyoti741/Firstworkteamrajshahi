<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Top Greeting & Real-time Indicator -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight">
                {{ $greeting }}
            </h1>
            <p class="text-xs text-[#8D7B70] mt-0.5 font-medium">
                {{ now()->format('l, d F Y') }} • {{ \App\Models\CartSetting::cartName() }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($currentBusinessDay && $currentBusinessDay->isOpen())
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]">
                    <span class="w-2 h-2 rounded-full bg-[#1E8E3E] animate-pulse"></span>
                    <span>Cart Open</span>
                </span>
            @else
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                    <span>Closed</span>
                </span>
            @endif
        </div>
    </div>

    <!-- 1. TODAY'S PERFORMANCE CARD -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs relative overflow-hidden">
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="text-xl">📅</span>
                <h2 class="text-sm sm:text-base font-extrabold text-[#2B1E16] uppercase tracking-wider">Today's Summary
                </h2>
            </div>
            <span
                class="text-xs font-bold px-2.5 py-1 rounded-full bg-[#F8F3EA] text-[#554338] border border-[#EFE7DE]">
                {{ $todayOrdersCount }} orders
            </span>
        </div>

        <!-- Metrics Grid (Sales, Items Sold, Orders) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 py-3">
            <!-- Sales -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3.5 flex flex-col justify-between overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider truncate">Today's Sales</span>
                    <span class="text-sm">🛒</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#1E8E3E] tracking-tight mt-1 truncate">
                    {{ $currency }}{{ number_format($todaySales, 0) }}
                </div>
                <span class="text-[10px] text-[#8D7B70] mt-0.5 font-medium">Completed customer revenue</span>
            </div>

            <!-- Items Sold -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3.5 flex flex-col justify-between overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider truncate">Items Sold</span>
                    <span class="text-sm">📦</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#F26522] tracking-tight mt-1 truncate">
                    {{ number_format($todayItemsSold) }}
                </div>
                <span class="text-[10px] text-[#8D7B70] mt-0.5 font-medium">Food units prepared & sold</span>
            </div>

            <!-- Completed Orders -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3.5 flex flex-col justify-between overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider truncate">Orders</span>
                    <span class="text-sm">📋</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-[#2B1E16] tracking-tight mt-1 truncate">
                    {{ $todayOrdersCount }}
                </div>
                <span class="text-[10px] text-[#8D7B70] mt-0.5 font-medium">Sales transactions</span>
            </div>
        </div>
    </div>

    <!-- 2. QUICK ACTIONS (4 Grid) -->
    <div class="space-y-2">
        <h3 class="text-xs font-bold text-[#8D7B70] uppercase tracking-wider px-1">
            Quick Actions
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- 1. View Sales -->
            <a href="{{ route('admin.sales') }}"
                class="bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] hover:border-[#F26522]/40 rounded-2xl p-3.5 sm:p-4 flex flex-col items-center justify-center text-center shadow-2xs transition-all touch-press group">
                <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🛒</span>
                <span class="font-extrabold text-xs sm:text-sm text-[#2B1E16] mt-1.5">View Sales</span>
                <span
                    class="text-[10px] text-[#8D7B70] mt-0.5">{{ $currency }}{{ number_format($todaySales, 0) }}</span>
            </a>

            <!-- 2. Assets & Liabilities -->
            <a href="{{ route('admin.assets-liabilities') }}"
                class="bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] hover:border-[#1E8E3E]/40 rounded-2xl p-3.5 sm:p-4 flex flex-col items-center justify-center text-center shadow-2xs transition-all touch-press group">
                <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🟢</span>
                <span class="font-extrabold text-xs sm:text-sm text-[#2B1E16] mt-1.5">Assets & Liabilities</span>
                <span class="text-[10px] text-[#8D7B70] mt-0.5">Manage Assets</span>
            </a>

            <!-- 3. Food Items -->
            <a href="{{ route('admin.products') }}"
                class="bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] hover:border-[#F26522]/40 rounded-2xl p-3.5 sm:p-4 flex flex-col items-center justify-center text-center shadow-2xs transition-all touch-press group">
                <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">🍔</span>
                <span class="font-extrabold text-xs sm:text-sm text-[#2B1E16] mt-1.5">Food Items</span>
                <span class="text-[10px] text-[#8D7B70] mt-0.5">Manage Menu & Prices</span>
            </a>

            <!-- 4. Reports -->
            <a href="{{ route('admin.reports') }}"
                class="bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] hover:border-[#1E8E3E]/40 rounded-2xl p-3.5 sm:p-4 flex flex-col items-center justify-center text-center shadow-2xs transition-all touch-press group">
                <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform">📊</span>
                <span class="font-extrabold text-xs sm:text-sm text-[#2B1E16] mt-1.5">Reports</span>
                <span class="text-[10px] text-[#8D7B70] mt-0.5">Daily Activity Report</span>
            </a>
        </div>
    </div>

    <!-- 3. THIS MONTH'S PERFORMANCE CARD -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="text-lg">📈</span>
                <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16]">This Month</h3>
            </div>
            <span class="text-xs text-[#8D7B70] font-medium">{{ now()->format('F Y') }}</span>
        </div>

        <div class="grid grid-cols-3 gap-2.5 pt-3 text-center sm:text-left">
            <!-- Month Sales -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3">
                <span
                    class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">Month Sales</span>
                <span class="text-sm sm:text-lg font-black text-[#1E8E3E] mt-0.5 block">
                    {{ $currency }}{{ number_format($monthSales, 0) }}
                </span>
            </div>

            <!-- Month Items Sold -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3">
                <span
                    class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">Items Sold</span>
                <span class="text-sm sm:text-lg font-black text-[#F26522] mt-0.5 block">
                    {{ number_format($monthItemsSold) }}
                </span>
            </div>

            <!-- Month Orders -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3">
                <span
                    class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">Total Orders</span>
                <span class="text-sm sm:text-lg font-black text-[#2B1E16] mt-0.5 block">
                    {{ number_format($monthOrders) }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4. BEST-SELLING ITEMS RANKING -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16]">Best Sellers</h3>
            </div>
            <a href="{{ route('admin.sales') }}" class="text-xs font-bold text-[#F26522] hover:text-[#E05310]">
                View All →
            </a>
        </div>

        <div class="space-y-2 pt-1">
            @forelse($bestSellingItems as $item)
                <div
                    class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 sm:p-3 flex items-center justify-between gap-2.5 text-xs sm:text-sm">
                    <div class="flex items-center gap-2 sm:gap-2.5 min-w-0 flex-1">
                        <span class="w-4 sm:w-5 text-center font-black text-[#F26522] text-xs shrink-0">
                            {{ $loop->iteration }}.
                        </span>
                        <span class="text-lg sm:text-xl shrink-0">
                            {{ $item->product?->image_emoji ?? '🍔' }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-[#2B1E16] truncate block">{{ $item->product_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-right shrink-0">
                        <span class="font-semibold text-[#8D7B70] text-xs whitespace-nowrap">{{ $item->total_qty }} sold</span>
                        <span
                            class="font-black text-[#1E8E3E] text-xs sm:text-sm whitespace-nowrap">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] py-4 text-center">No sales recorded yet.</p>
            @endforelse
        </div>
    </div>

    <!-- 5. CART SHIFT & TIMINGS -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div
                    class="w-10 h-10 rounded-2xl {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }} flex items-center justify-center text-lg shrink-0">
                    {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? '🟢' : '🔴' }}
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#2B1E16] flex items-center gap-2">
                        <span>Cart Shift Status:</span>
                        <span
                            class="{{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }}">
                            {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'Open (Active)' : 'Closed' }}
                        </span>
                    </h4>
                    <p class="text-[11px] text-[#8D7B70] font-medium mt-0.5">Today's recorded operational hours</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($currentBusinessDay && $currentBusinessDay->isOpen())
                    <button type="button" wire:click="$set('showDayModal', true)"
                        class="px-3.5 py-2 bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA] rounded-xl text-xs font-bold transition-all cursor-pointer touch-press shadow-2xs">
                        Close Shift
                    </button>
                @endif

                <button type="button" wire:click="$set('showAllShiftRecordsModal', true)"
                    class="px-3.5 py-2 bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] text-[#2B1E16] rounded-xl text-xs font-bold transition-all cursor-pointer touch-press shadow-2xs flex items-center gap-1.5">
                    <span>📋</span> All Records
                </button>
            </div>
        </div>

        <!-- Opening Time & Closing Time Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
            <!-- Opening Time Box -->
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider block">Opening
                        Time</span>
                    <span class="text-sm sm:text-base font-black text-[#1E8E3E] mt-0.5 block">
                        {{ $currentBusinessDay?->opened_at ? $currentBusinessDay->opened_at->format('h:i A') : 'Not Opened' }}
                    </span>
                    <span class="text-[10px] text-[#8D7B70] mt-0.5 block">
                        @if($currentBusinessDay?->openedBy)
                            By <strong class="text-[#2B1E16]">{{ $currentBusinessDay->openedBy->name }}</strong>
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
            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider block">Closing
                        Time</span>
                    <span
                        class="text-sm sm:text-base font-black {{ $currentBusinessDay?->closed_at ? 'text-[#DC2626]' : 'text-[#F26522]' }} mt-0.5 block">
                        {{ $currentBusinessDay?->closed_at ? $currentBusinessDay->closed_at->format('h:i A') : 'Currently Online' }}
                    </span>
                    <span class="text-[10px] text-[#8D7B70] mt-0.5 block">
                        @if($currentBusinessDay?->closed_at && $currentBusinessDay?->closedBy)
                            By <strong class="text-[#2B1E16]">{{ $currentBusinessDay->closedBy->name }}</strong>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🔒</span>
                        <h3 class="font-black text-base text-[#2B1E16]">Close Today's Business Day</h3>
                    </div>
                    <button type="button" wire:click="$set('showDayModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer">✕</button>
                </div>

                <form wire:submit="closeBusinessDay" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Final Cash in Register
                            ({{ $currency }})</label>
                        <input type="number" step="0.01" wire:model="closingCashAmount" placeholder="e.g. 8500"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2.5 text-base text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Closing Notes</label>
                        <textarea wire:model="dayNotes" rows="2" placeholder="Shift notes or remarks..."
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3 py-2 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showDayModal', false)"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#DC2626] hover:bg-[#B91C1C] cursor-pointer shadow-2xs">
                            Confirm & Close Day
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- All Shift Records Modal (Cart Open & Close History) -->
    @if($showAllShiftRecordsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/60 backdrop-blur-xs"
            wire:keydown.escape="$set('showAllShiftRecordsModal', false)">
            <div
                class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="p-4 sm:p-5 border-b border-[#EFE7DE] flex items-center justify-between gap-3 shrink-0 bg-[#F8F3EA]/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#FFF0E6] text-[#F26522] border border-[#FAD7C0] flex items-center justify-center text-lg shrink-0">
                            📋
                        </div>
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-[#2B1E16] flex items-center gap-2">
                                Cart Shift & Operational History
                            </h3>
                            <p class="text-xs text-[#8D7B70] font-medium">
                                Complete records of all cart open/close timings, staff on duty, floats, and sales.
                            </p>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showAllShiftRecordsModal', false)"
                        class="w-8 h-8 rounded-xl bg-white border border-[#EFE7DE] text-[#8D7B70] hover:text-[#2B1E16] flex items-center justify-center text-sm font-bold cursor-pointer transition-colors shrink-0">
                        ✕
                    </button>
                </div>

                <!-- Modal Body (Scrollable Table & Summary) -->
                <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                    <!-- Quick Stats Pill Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3">
                            <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider block">Total Recorded Shifts</span>
                            <span class="text-lg font-black text-[#2B1E16] mt-0.5 block">{{ $allBusinessDays->count() }}</span>
                        </div>
                        <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3">
                            <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider block">Current Status</span>
                            <span class="text-sm sm:text-base font-black {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }} mt-0.5 block">
                                {{ $currentBusinessDay && $currentBusinessDay->isOpen() ? '🟢 Open (Active)' : '🔴 Closed' }}
                            </span>
                        </div>
                        <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3">
                            <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider block">Closed Shifts</span>
                            <span class="text-lg font-black text-[#8D7B70] mt-0.5 block">{{ $allBusinessDays->where('status', 'closed')->count() }}</span>
                        </div>
                        <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3">
                            <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider block">Total Shift Sales</span>
                            <span class="text-lg font-black text-[#1E8E3E] mt-0.5 block">{{ $currency }}{{ number_format($allBusinessDays->sum('sales_sum_total_amount'), 0) }}</span>
                        </div>
                    </div>

                    <!-- View Toggle Tabs -->
                    <div class="flex items-center gap-1.5 p-1 bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl w-fit">
                        <button type="button" wire:click="$set('recordsViewTab', 'all_events')"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $recordsViewTab === 'all_events' ? 'bg-white text-[#2B1E16] shadow-2xs' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                            ⚡ All Open & Close Status Logs ({{ $allStatusLogs->count() }})
                        </button>
                        <button type="button" wire:click="$set('recordsViewTab', 'daily_summary')"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $recordsViewTab === 'daily_summary' ? 'bg-white text-[#2B1E16] shadow-2xs' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                            📅 Daily Shift Summary ({{ $allBusinessDays->count() }})
                        </button>
                    </div>

                    @if($recordsViewTab === 'all_events')
                        <!-- 1. All Open & Close Status Events Log (EVERY SINGLE OPEN & CLOSE) -->
                        <div class="overflow-x-auto rounded-2xl border border-[#EFE7DE]">
                            <table class="w-full min-w-[700px] text-left text-xs text-[#554338]">
                                <thead class="text-[11px] uppercase tracking-wider text-[#8D7B70] font-bold bg-[#F8F3EA] border-b border-[#EFE7DE]">
                                    <tr>
                                        <th class="px-4 py-3 whitespace-nowrap">Event Time & Date</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Status Action</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Staff / Cashier</th>
                                        <th class="px-4 py-3 text-right whitespace-nowrap">Opening Float</th>
                                        <th class="px-4 py-3 text-right whitespace-nowrap">Closing Cash</th>
                                        <th class="px-4 py-3 text-right whitespace-nowrap">Shift Sales</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Notes / Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#EFE7DE] bg-white">
                                    @forelse($allStatusLogs as $log)
                                        <tr class="hover:bg-[#F8F3EA]/50 transition-colors {{ $log->isOpened() ? 'bg-[#EAF7EE]/20' : '' }}">
                                            <td class="px-4 py-3 font-bold text-[#2B1E16] whitespace-nowrap">
                                                <div class="flex items-center gap-1.5">
                                                    <span>{{ $log->occurred_at ? $log->occurred_at->format('h:i:s A') : '—' }}</span>
                                                    <span class="text-[10px] text-[#8D7B70]">({{ $log->occurred_at ? $log->occurred_at->format('d M Y') : '—' }})</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($log->isOpened())
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase inline-flex items-center gap-1 bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#1E8E3E]"></span>
                                                        🌅 Cart Opened
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase inline-flex items-center gap-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626]"></span>
                                                        🌙 Cart Closed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap font-bold text-[#2B1E16]">
                                                {{ $log->user->name ?? 'System / Admin' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold text-[#1E8E3E] whitespace-nowrap">
                                                {{ $log->isOpened() && $log->opening_cash_float > 0 ? $currency . number_format($log->opening_cash_float, 0) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-[#2B1E16] whitespace-nowrap">
                                                {{ $log->isClosed() && $log->closing_cash_amount !== null ? $currency . number_format($log->closing_cash_amount, 0) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-black text-[#1E8E3E] whitespace-nowrap">
                                                {{ $log->isClosed() && $log->sales_total !== null ? $currency . number_format($log->sales_total, 0) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-[#8D7B70] text-[11px] max-w-xs truncate">
                                                {{ $log->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-[#8D7B70]">
                                                No cart status logs recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- 2. Daily Shift Summary Table -->
                        <div class="overflow-x-auto rounded-2xl border border-[#EFE7DE]">
                            <table class="w-full min-w-[700px] text-left text-xs text-[#554338]">
                                <thead class="text-[11px] uppercase tracking-wider text-[#8D7B70] font-bold bg-[#F8F3EA] border-b border-[#EFE7DE]">
                                    <tr>
                                        <th class="px-4 py-3 whitespace-nowrap">Date</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Status</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Opening (🌅)</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Closing (🌙)</th>
                                        <th class="px-4 py-3 text-right whitespace-nowrap">Opening Float</th>
                                        <th class="px-4 py-3 text-right whitespace-nowrap">Shift Sales</th>
                                        <th class="px-4 py-3 text-right whitespace-nowrap">Closing Cash</th>
                                        <th class="px-4 py-3 whitespace-nowrap">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#EFE7DE] bg-white">
                                    @forelse($allBusinessDays as $day)
                                        <tr class="hover:bg-[#F8F3EA]/50 transition-colors {{ $day->isOpen() ? 'bg-[#EAF7EE]/30' : '' }}">
                                            <td class="px-4 py-3 font-bold text-[#2B1E16] whitespace-nowrap">
                                                <div class="flex items-center gap-1.5">
                                                    <span>{{ $day->date->format('d M Y') }}</span>
                                                    <span class="text-[10px] text-[#8D7B70]">({{ $day->date->format('D') }})</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase inline-flex items-center gap-1 {{ $day->isOpen() ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $day->isOpen() ? 'bg-[#1E8E3E]' : 'bg-[#DC2626]' }}"></span>
                                                    {{ $day->isOpen() ? 'Open' : 'Closed' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-bold text-[#1E8E3E]">
                                                    {{ $day->opened_at ? $day->opened_at->format('h:i A') : '—' }}
                                                </div>
                                                <div class="text-[10px] text-[#8D7B70]">
                                                    By: <strong class="text-[#2B1E16]">{{ $day->openedBy->name ?? 'System' }}</strong>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-bold {{ $day->closed_at ? 'text-[#DC2626]' : 'text-[#F26522]' }}">
                                                    {{ $day->closed_at ? $day->closed_at->format('h:i A') : ($day->isOpen() ? 'In Progress' : '—') }}
                                                </div>
                                                <div class="text-[10px] text-[#8D7B70]">
                                                    @if($day->closedBy)
                                                        By: <strong class="text-[#2B1E16]">{{ $day->closedBy->name }}</strong>
                                                    @elseif($day->isOpen())
                                                        Active Shift
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold text-[#554338] whitespace-nowrap">
                                                {{ $currency }}{{ number_format($day->opening_cash_float, 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-black text-[#1E8E3E] whitespace-nowrap">
                                                {{ $currency }}{{ number_format($day->sales_sum_total_amount ?? 0, 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-[#2B1E16] whitespace-nowrap">
                                                {{ $day->closing_cash_amount ? $currency . number_format($day->closing_cash_amount, 0) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-[#8D7B70] text-[11px] max-w-xs truncate">
                                                {{ $day->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-[#8D7B70]">
                                                No cart shift logs recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-[#EFE7DE] bg-[#F8F3EA]/50 flex items-center justify-between gap-3 shrink-0">
                    <div>
                        @if($currentBusinessDay && !$currentBusinessDay->isOpen())
                            <button type="button" wire:click="reopenBusinessDay"
                                class="px-3.5 py-2 bg-[#1E8E3E] hover:bg-[#167030] text-white rounded-xl text-xs font-bold transition-all cursor-pointer touch-press shadow-2xs flex items-center gap-1.5">
                                <span>🟢</span> Reopen Today's Shift
                            </button>
                        @endif
                    </div>

                    <button type="button" wire:click="$set('showAllShiftRecordsModal', false)"
                        class="px-5 py-2 bg-white hover:bg-[#EFE7DE] text-[#2B1E16] border border-[#EFE7DE] rounded-xl text-xs font-bold cursor-pointer transition-colors shadow-2xs">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>