<div class="space-y-4 sm:space-y-6 max-w-7xl mx-auto">

    <!-- Top Seller Selector & Profile Banner -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-4">
        <!-- Header row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl {{ $selectedSeller ? ($selectedSeller->role === 'admin' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30') : 'bg-gradient-to-br from-amber-500 to-orange-600 text-zinc-950 font-black' }} flex items-center justify-center text-xl sm:text-2xl shadow-lg shrink-0">
                    {{ $selectedSeller ? ($selectedSeller->role === 'admin' ? '👑' : '👤') : '🌐' }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-base sm:text-xl font-black text-white tracking-tight">
                            {{ $selectedSeller ? $selectedSeller->name : 'All Registered Sellers' }}
                        </h1>
                        @if($selectedSeller)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $selectedSeller->is_active ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                {{ $selectedSeller->is_active ? 'Active' : 'Disabled' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $selectedSeller->role === 'admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' }}">
                                {{ $selectedSeller->role === 'admin' ? 'Owner / Admin' : 'Seller Staff' }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Global Aggregated View
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5">
                        @if($selectedSeller)
                            <span>{{ $selectedSeller->email }}</span>
                            @if($selectedSeller->phone)
                                <span class="text-zinc-600">•</span>
                                <span>{{ $selectedSeller->phone }}</span>
                            @endif
                        @else
                            <span>Showing combined performance across all {{ count($allSellers) }} staff accounts</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Manage Accounts Button -->
            <a href="{{ route('admin.sellers') }}" 
               class="px-3 py-1.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 border border-zinc-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition-all touch-press w-fit">
                <span>👥</span>
                <span>Manage Accounts</span>
            </a>
        </div>

        <!-- 1-Tap Dynamic Seller Pill Selector -->
        <div>
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <span>🔍</span> Filter By Seller:
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none pb-1">
                <!-- All Sellers Pill -->
                <button type="button" 
                        wire:click="selectSeller(null)"
                        class="px-3 py-2 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer shrink-0 flex items-center gap-1.5 {{ is_null($sellerId) ? 'bg-amber-500 text-zinc-950 shadow-md font-black' : 'bg-zinc-950 text-zinc-400 hover:text-zinc-200 border border-zinc-800' }}">
                    <span>🌐</span>
                    <span>All Sellers</span>
                </button>

                @foreach($allSellers as $seller)
                    <button type="button" 
                            wire:click="selectSeller({{ $seller->id }})"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer shrink-0 flex items-center gap-1.5 {{ $sellerId === $seller->id ? 'bg-amber-500 text-zinc-950 shadow-md font-black' : 'bg-zinc-950 text-zinc-400 hover:text-zinc-200 border border-zinc-800' }}">
                        <span>{{ $seller->role === 'admin' ? '👑' : '👤' }}</span>
                        <span>{{ $seller->name }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Date Range Filter Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-zinc-800">
            <div class="flex items-center gap-1.5 bg-zinc-950 p-1 rounded-xl border border-zinc-800 text-xs font-bold w-fit">
                <button type="button" 
                        wire:click="setPeriod('today')"
                        class="px-3 py-1.5 rounded-lg transition-all touch-press cursor-pointer {{ $period === 'today' ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Today
                </button>
                <button type="button" 
                        wire:click="setPeriod('week')"
                        class="px-3 py-1.5 rounded-lg transition-all touch-press cursor-pointer {{ $period === 'week' ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    This Week
                </button>
                <button type="button" 
                        wire:click="setPeriod('month')"
                        class="px-3 py-1.5 rounded-lg transition-all touch-press cursor-pointer {{ $period === 'month' ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    This Month
                </button>
                <button type="button" 
                        wire:click="setPeriod('custom')"
                        class="px-3 py-1.5 rounded-lg transition-all touch-press cursor-pointer {{ $period === 'custom' ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Custom
                </button>
            </div>

            @if($period === 'custom')
                <div class="flex items-center gap-2 text-xs">
                    <input type="date" wire:model.live="startDate" 
                           class="bg-zinc-950 border border-zinc-800 rounded-xl px-2.5 py-1.5 text-zinc-200 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                    <span class="text-zinc-500">to</span>
                    <input type="date" wire:model.live="endDate" 
                           class="bg-zinc-950 border border-zinc-800 rounded-xl px-2.5 py-1.5 text-zinc-200 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                </div>
            @else
                <div class="text-[11px] text-zinc-400 font-semibold flex items-center gap-1.5">
                    <span>📅 Period:</span>
                    <span class="text-zinc-200">{{ ucfirst($period) }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. KPI METRICS CARDS (6-CARD GRID) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3.5">
        <!-- Total Sales -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">Total Sales</span>
            <div class="mt-2">
                <div class="font-black text-emerald-400 text-base sm:text-lg lg:text-xl truncate">
                    {{ $currency }}{{ number_format($totalSales, 0) }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-zinc-500">{{ $totalOrdersCount }} orders</span>
            </div>
        </div>

        <!-- Total Items Sold -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">Items Sold</span>
            <div class="mt-2">
                <div class="font-black text-amber-400 text-base sm:text-lg lg:text-xl truncate">
                    {{ number_format($totalItemsSold) }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-zinc-500">food units</span>
            </div>
        </div>

        <!-- Total Costs / Expenses -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">Total Cost</span>
            <div class="mt-2">
                <div class="font-black text-rose-400 text-base sm:text-lg lg:text-xl truncate">
                    {{ $currency }}{{ number_format($totalExpenses, 0) }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-zinc-500">shifts & expenses</span>
            </div>
        </div>

        <!-- Net Profit / Loss -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">Net Profit</span>
            <div class="mt-2">
                <div class="font-black {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} text-base sm:text-lg lg:text-xl truncate">
                    {{ $totalProfit >= 0 ? '+' : '' }}{{ $currency }}{{ number_format($totalProfit, 0) }}
                </div>
                <span class="text-[9px] sm:text-[10px] font-bold {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">{{ $profitMargin }}% margin</span>
            </div>
        </div>

        <!-- Total Receipts / Orders -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">Transactions</span>
            <div class="mt-2">
                <div class="font-black text-blue-400 text-base sm:text-lg lg:text-xl truncate">
                    {{ $totalOrdersCount }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-zinc-500">completed sales</span>
            </div>
        </div>

        <!-- Avg Order Value -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">Avg Ticket</span>
            <div class="mt-2">
                <div class="font-black text-purple-400 text-base sm:text-lg lg:text-xl truncate">
                    {{ $currency }}{{ number_format($averageOrderValue, 0) }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-zinc-500">per order</span>
            </div>
        </div>
    </div>

    <!-- 3. SALES VS EXPENSES CHART FOR THIS SELLER -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-6 shadow-xl space-y-4"
         x-data="{
             chart: null,
             initChart() {
                 const ctx = document.getElementById('sellerPerformanceChart');
                 if (!ctx) return;
                 
                 if (this.chart) {
                     this.chart.destroy();
                 }

                 const points = @js($chartPoints);
                 const labels = points.map(p => p.label);
                 const salesData = points.map(p => p.sales);
                 const expensesData = points.map(p => p.expenses);

                 this.chart = new Chart(ctx, {
                     type: 'bar',
                     data: {
                         labels: labels,
                         datasets: [
                             {
                                 label: 'Sales ({{ $currency }})',
                                 data: salesData,
                                 backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                 borderColor: '#10b981',
                                 borderWidth: 1.5,
                                 borderRadius: 6,
                                 borderSkipped: false,
                             },
                             {
                                 label: 'Expenses ({{ $currency }})',
                                 data: expensesData,
                                 backgroundColor: 'rgba(244, 63, 94, 0.85)',
                                 borderColor: '#f43f5e',
                                 borderWidth: 1.5,
                                 borderRadius: 6,
                                 borderSkipped: false,
                             }
                         ]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         interaction: {
                             intersect: false,
                             mode: 'index',
                         },
                         plugins: {
                             legend: { display: false },
                             tooltip: {
                                 backgroundColor: '#09090b',
                                 borderColor: '#27272a',
                                 borderWidth: 1,
                                 padding: 10,
                                 cornerRadius: 10,
                                 titleFont: { weight: 'bold', size: 11 },
                                 bodyFont: { size: 11 },
                                 callbacks: {
                                     label: function(context) {
                                         return ' ' + context.dataset.label + ': ' + Number(context.raw).toLocaleString();
                                     },
                                     afterBody: function(contexts) {
                                         const idx = contexts[0].dataIndex;
                                         const pt = points[idx];
                                         if (pt) {
                                             const profit = pt.sales - pt.expenses;
                                             return '\n Profit: {{ $currency }}' + Number(profit).toLocaleString();
                                         }
                                         return '';
                                     }
                                 }
                             }
                         },
                         scales: {
                             x: {
                                 grid: { display: false },
                                 ticks: { color: '#71717a', font: { weight: '600', size: 10 } }
                             },
                             y: {
                                 grid: { color: 'rgba(39, 39, 42, 0.5)' },
                                 ticks: {
                                     color: '#71717a',
                                     font: { size: 10 },
                                     callback: function(value) {
                                         return value >= 1000 ? '{{ $currency }}' + (value / 1000) + 'k' : '{{ $currency }}' + value;
                                     }
                                 }
                             }
                         }
                     }
                 });
             }
         }"
         x-init="initChart()"
         x-effect="initChart()">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2 border-b border-zinc-800">
            <div>
                <h3 class="font-black text-sm sm:text-base text-white flex items-center gap-1.5 sm:gap-2">
                    <span>📊</span> {{ $selectedSeller ? $selectedSeller->name."'s" : 'All Sellers' }} Sales vs Costs
                </h3>
                <div class="flex items-center gap-3 text-[10px] sm:text-xs text-zinc-400 mt-0.5">
                    <span class="flex items-center gap-1.5 font-bold text-emerald-400">
                        <span class="w-2 h-2 rounded-sm bg-emerald-500 inline-block shadow-sm"></span> Sales
                    </span>
                    <span class="flex items-center gap-1.5 font-bold text-rose-400">
                        <span class="w-2 h-2 rounded-sm bg-rose-500 inline-block shadow-sm"></span> Costs
                    </span>
                </div>
            </div>

            <!-- 7 / 30 Days Toggle -->
            <div class="flex items-center bg-zinc-950 p-1 rounded-xl border border-zinc-800 text-[10px] sm:text-xs font-bold w-fit">
                <button type="button" 
                        wire:click="setChartDays(7)" 
                        class="px-2.5 py-1 rounded-lg transition-all touch-press cursor-pointer {{ $chartDays === 7 ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Last 7 Days
                </button>
                <button type="button" 
                        wire:click="setChartDays(30)" 
                        class="px-2.5 py-1 rounded-lg transition-all touch-press cursor-pointer {{ $chartDays === 30 ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Last 30 Days
                </button>
            </div>
        </div>

        <!-- Canvas -->
        <div class="w-full h-56 sm:h-72 relative">
            <canvas id="sellerPerformanceChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- 4. TWO COLUMNS: PAYMENT METHOD BREAKDOWN & BEST SELLING ITEMS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Payment Methods Breakdown -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <h3 class="font-black text-sm sm:text-base text-white flex items-center gap-2">
                    <span>💳</span> Payment Methods
                </h3>
                <span class="text-xs text-zinc-400 font-semibold">{{ $currency }}{{ number_format($totalSales, 0) }} Total</span>
            </div>

            <div class="space-y-3 pt-1">
                @foreach($paymentBreakdown as $payKey => $pay)
                    <div class="bg-zinc-950/70 border border-zinc-800/80 rounded-2xl p-3 space-y-1.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-base">
                                    @if($payKey === 'cash') 💵 @elseif($payKey === 'bkash') 📱 @elseif($payKey === 'nagad') 📲 @elseif($payKey === 'card') 💳 @else 🪙 @endif
                                </span>
                                <span class="font-bold text-zinc-200">{{ $pay['label'] }}</span>
                                <span class="text-[10px] text-zinc-500">({{ $pay['count'] }} orders)</span>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-emerald-400">{{ $currency }}{{ number_format($pay['amount'], 0) }}</span>
                                <span class="text-[10px] text-zinc-400 ml-1">({{ $pay['percentage'] }}%)</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full h-2 bg-zinc-900 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full transition-all duration-500" 
                                 style="width: {{ $pay['percentage'] }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Best-Selling Food Items for this seller -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏆</span>
                    <h3 class="font-black text-sm sm:text-base text-white">Top Food Items</h3>
                </div>
                <span class="text-xs text-zinc-400 font-semibold">Ranked by units</span>
            </div>

            <div class="space-y-2 pt-1">
                @forelse($bestSellingItems as $item)
                    <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3 flex items-center justify-between gap-3 text-xs sm:text-sm">
                        <div class="flex items-center gap-3">
                            <span class="w-5 text-center font-black text-amber-400 text-xs sm:text-sm">
                                {{ $loop->iteration }}.
                            </span>
                            <span class="text-xl">
                                {{ $item->product?->image_emoji ?? '🍔' }}
                            </span>
                            <div>
                                <span class="font-bold text-zinc-100 text-xs sm:text-sm">{{ $item->product_name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-right shrink-0">
                            <span class="font-bold text-zinc-400 text-[11px] sm:text-xs">{{ $item->total_qty }} sold</span>
                            <span class="font-black text-emerald-400 text-xs sm:text-sm">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-500 py-6 text-center">No sales recorded for this period.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 5. CLOSED CART / SHIFT HISTORY FOR THIS SELLER -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-6 shadow-xl space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
            <div class="flex items-center gap-2">
                <span class="text-lg">🛒</span>
                <h3 class="font-black text-sm sm:text-base text-white">Closed Cart & Shift History</h3>
            </div>
            <span class="text-xs text-zinc-400 font-semibold">{{ count($recentShifts) }} shifts listed</span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-zinc-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-zinc-950 text-zinc-400 font-bold border-b border-zinc-800">
                    <tr>
                        <th class="py-2.5 px-3">Date</th>
                        <th class="py-2.5 px-3">Shift Status</th>
                        <th class="py-2.5 px-3">Opened By</th>
                        <th class="py-2.5 px-3">Closed By</th>
                        <th class="py-2.5 px-3">Opening Float</th>
                        <th class="py-2.5 px-3">Closing Cost</th>
                        <th class="py-2.5 px-3">Closing Cash</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 bg-zinc-900/40">
                    @forelse($recentShifts as $shift)
                        <tr class="hover:bg-zinc-800/40 transition-colors">
                            <td class="py-2.5 px-3 font-semibold text-zinc-200 whitespace-nowrap">
                                {{ $shift->date->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $shift->isOpen() ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $shift->isOpen() ? 'Open' : 'Closed' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-zinc-300 whitespace-nowrap">
                                {{ $shift->openedBy?->name ?? '—' }}
                                @if($shift->opened_at)
                                    <span class="text-[10px] text-zinc-500 block">{{ $shift->opened_at->format('h:i A') }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-zinc-300 whitespace-nowrap">
                                {{ $shift->closedBy?->name ?? '—' }}
                                @if($shift->closed_at)
                                    <span class="text-[10px] text-zinc-500 block">{{ $shift->closed_at->format('h:i A') }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 font-medium text-zinc-300 whitespace-nowrap">
                                {{ $currency }}{{ number_format($shift->opening_cash_float, 0) }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-rose-400 whitespace-nowrap">
                                {{ $currency }}{{ number_format($shift->closing_cost, 0) }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-emerald-400 whitespace-nowrap">
                                {{ $currency }}{{ number_format($shift->closing_cash_amount, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-zinc-500 text-xs">
                                No shift records found for this seller.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. TWO COLUMNS: RECENT SALES & RECENT EXPENSES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Recent Completed Sales -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <h3 class="font-black text-sm sm:text-base text-white flex items-center gap-2">
                    <span>🧾</span> Recent Sales Ledger
                </h3>
                <span class="text-xs text-zinc-400">{{ count($recentSales) }} recent sales</span>
            </div>

            <div class="space-y-2">
                @forelse($recentSales as $sale)
                    <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3 flex items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-200">{{ $sale->invoice_no }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-zinc-800 text-zinc-400">
                                    {{ $sale->payment_method }}
                                </span>
                            </div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">
                                <span>{{ $sale->created_at->format('d M, h:i A') }}</span>
                                <span class="text-zinc-600">•</span>
                                <span>{{ $sale->user?->name ?? 'Staff' }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-black text-emerald-400 text-sm">
                                {{ $currency }}{{ number_format($sale->total_amount, 0) }}
                            </div>
                            <div class="text-[10px] text-zinc-400">
                                {{ $sale->total_items_count }} items
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-500 py-6 text-center">No recent sales found.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Costs / Expenses -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <h3 class="font-black text-sm sm:text-base text-white flex items-center gap-2">
                    <span>💸</span> Recorded Costs & Expenses
                </h3>
                <span class="text-xs text-zinc-400">{{ count($recentExpenses) }} records</span>
            </div>

            <div class="space-y-2">
                @forelse($recentExpenses as $expense)
                    <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3 flex items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-200">{{ $expense->title }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-rose-950/40 text-rose-300 border border-rose-800/40">
                                    {{ $expense->category_label }}
                                </span>
                            </div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">
                                <span>{{ $expense->expense_date->format('d M Y') }}</span>
                                <span class="text-zinc-600">•</span>
                                <span>{{ $expense->user?->name ?? 'Staff' }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-black text-rose-400 text-sm">
                                {{ $currency }}{{ number_format($expense->amount, 0) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-500 py-6 text-center">No cost/expense records found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
