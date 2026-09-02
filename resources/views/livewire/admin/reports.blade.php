<div class="space-y-3.5 sm:space-y-5 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div>
            <h1 class="text-sm sm:text-lg font-black text-white tracking-tight flex items-center gap-1.5 sm:gap-2">
                <span class="text-sm sm:text-base">📊</span> Business Reports
            </h1>
            <p class="text-[9px] sm:text-xs text-zinc-400 mt-0.5">Clear overview of sales, expenses, and profitability.</p>
        </div>

        <button type="button" 
                wire:click="exportReportCsv"
                class="px-2 sm:px-2.5 py-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-zinc-800 rounded-lg text-[9px] sm:text-xs font-semibold flex items-center gap-1 shadow cursor-pointer touch-press">
            <span>📥</span> <span class="hidden sm:inline">Export CSV</span>
        </button>
    </div>

    <!-- 1. THREE SIMPLE SUMMARY SECTIONS (Today, This Week, This Month) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-4">
        <!-- 1. Today Card -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <div class="flex items-center justify-between pb-1.5 sm:pb-2.5 border-b border-zinc-800">
                <h2 class="font-bold text-[11px] sm:text-xs text-zinc-300 flex items-center gap-1">
                    <span>📅</span> Today
                </h2>
                <span class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $todayProfit >= 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                    {{ $todayProfit >= 0 ? 'Profit' : 'Loss' }}
                </span>
            </div>

            <div class="space-y-1 sm:space-y-1.5 pt-2 sm:pt-2.5 text-[10px] sm:text-xs">
                <div class="flex justify-between items-center text-[10px] sm:text-xs">
                    <span class="text-zinc-400">Sales:</span>
                    <span class="font-bold text-emerald-400 text-[11px] sm:text-xs">{{ $currency }}{{ number_format($todaySales, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] sm:text-xs">
                    <span class="text-zinc-400">Expenses:</span>
                    <span class="font-bold text-rose-400 text-[11px] sm:text-xs">{{ $currency }}{{ number_format($todayExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-1 sm:pt-1.5 border-t border-zinc-800 font-black text-[11px] sm:text-xs">
                    <span class="text-zinc-200">Profit:</span>
                    <span class="{{ $todayProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} text-xs sm:text-sm">
                        {{ $todayProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($todayProfit), 0) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. This Week Card -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <div class="flex items-center justify-between pb-1.5 sm:pb-2.5 border-b border-zinc-800">
                <h2 class="font-bold text-[11px] sm:text-xs text-zinc-300 flex items-center gap-1">
                    <span>📆</span> This Week
                </h2>
                <span class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $weekProfit >= 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                    {{ $weekProfit >= 0 ? 'Profit' : 'Loss' }}
                </span>
            </div>

            <div class="space-y-1 sm:space-y-1.5 pt-2 sm:pt-2.5 text-[10px] sm:text-xs">
                <div class="flex justify-between items-center text-[10px] sm:text-xs">
                    <span class="text-zinc-400">Sales:</span>
                    <span class="font-bold text-emerald-400 text-[11px] sm:text-xs">{{ $currency }}{{ number_format($weekSales, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] sm:text-xs">
                    <span class="text-zinc-400">Expenses:</span>
                    <span class="font-bold text-rose-400 text-[11px] sm:text-xs">{{ $currency }}{{ number_format($weekExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-1 sm:pt-1.5 border-t border-zinc-800 font-black text-[11px] sm:text-xs">
                    <span class="text-zinc-200">Profit:</span>
                    <span class="{{ $weekProfit >= 0 ? 'text-amber-400' : 'text-rose-400' }} text-xs sm:text-sm">
                        {{ $weekProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($weekProfit), 0) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. This Month Card -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-lg flex flex-col justify-between">
            <div class="flex items-center justify-between pb-1.5 sm:pb-2.5 border-b border-zinc-800">
                <h2 class="font-bold text-[11px] sm:text-xs text-zinc-300 flex items-center gap-1">
                    <span>📈</span> This Month
                </h2>
                <span class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $monthProfit >= 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                    {{ $monthProfit >= 0 ? 'Profit' : 'Loss' }}
                </span>
            </div>

            <div class="space-y-1 sm:space-y-1.5 pt-2 sm:pt-2.5 text-[10px] sm:text-xs">
                <div class="flex justify-between items-center text-[10px] sm:text-xs">
                    <span class="text-zinc-400">Sales:</span>
                    <span class="font-bold text-emerald-400 text-[11px] sm:text-xs">{{ $currency }}{{ number_format($monthSales, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] sm:text-xs">
                    <span class="text-zinc-400">Expenses:</span>
                    <span class="font-bold text-rose-400 text-[11px] sm:text-xs">{{ $currency }}{{ number_format($monthExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-1 sm:pt-1.5 border-t border-zinc-800 font-black text-[11px] sm:text-xs">
                    <span class="text-zinc-200">Profit:</span>
                    <span class="{{ $monthProfit >= 0 ? 'text-amber-400' : 'text-rose-400' }} text-xs sm:text-sm">
                        {{ $monthProfit >= 0 ? '+' : '-' }}{{ $currency }}{{ number_format(abs($monthProfit), 0) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SALES VS EXPENSES CHART & DAILY PERFORMANCE -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 shadow-xl space-y-3.5 sm:space-y-4"
         x-data="{
             chart: null,
             initChart() {
                 const ctx = document.getElementById('salesExpensesChart');
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
                             legend: {
                                 display: false
                             },
                             tooltip: {
                                 backgroundColor: '#09090b',
                                 borderColor: '#27272a',
                                 borderWidth: 1,
                                 padding: 8,
                                 cornerRadius: 8,
                                 titleFont: { weight: 'bold', size: 10 },
                                 bodyFont: { size: 10 },
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
                                 grid: {
                                     display: false
                                 },
                                 ticks: {
                                     color: '#71717a',
                                     font: { weight: '600', size: 9 }
                                 }
                             },
                             y: {
                                 grid: {
                                     color: 'rgba(39, 39, 42, 0.5)'
                                 },
                                 ticks: {
                                     color: '#71717a',
                                     font: { size: 9 },
                                     callback: function(value) {
                                         if (value >= 1000) {
                                             return '{{ $currency }}' + (value / 1000) + 'k';
                                         }
                                         return '{{ $currency }}' + value;
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

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1.5 sm:pb-2 border-b border-zinc-800">
            <div>
                <h3 class="font-black text-xs sm:text-sm text-white flex items-center gap-1">
                    <span class="text-xs sm:text-sm">📊</span> Sales vs Expenses
                </h3>
                <div class="flex items-center gap-2.5 text-[9px] sm:text-[11px] text-zinc-400 mt-0.5">
                    <span class="flex items-center gap-1 font-bold text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-sm bg-emerald-500 inline-block shadow-sm"></span> Sales
                    </span>
                    <span class="flex items-center gap-1 font-bold text-rose-400">
                        <span class="w-1.5 h-1.5 rounded-sm bg-rose-500 inline-block shadow-sm"></span> Expenses
                    </span>
                </div>
            </div>

            <!-- 7 Days | 30 Days Toggle -->
            <div class="flex items-center bg-zinc-950 p-0.5 rounded-lg border border-zinc-800 text-[9px] sm:text-xs font-bold w-fit">
                <button type="button" 
                        wire:click="setChartDays(7)" 
                        class="px-2 py-0.5 rounded-md transition-all touch-press cursor-pointer {{ $chartDays === 7 ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Last 7 Days
                </button>
                <button type="button" 
                        wire:click="setChartDays(30)" 
                        class="px-2 py-0.5 rounded-md transition-all touch-press cursor-pointer {{ $chartDays === 30 ? 'bg-amber-500 text-zinc-950 font-black shadow-sm' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Last 30 Days
                </button>
            </div>
        </div>

        <!-- Interactive Chart.js Canvas -->
        <div class="w-full h-48 sm:h-64 relative">
            <canvas id="salesExpensesChart" class="w-full h-full"></canvas>
        </div>

        <!-- Daily Financial Performance Ledger Table -->
        <div class="space-y-1.5 pt-1.5 border-t border-zinc-800">
            <div class="flex items-center justify-between">
                <h4 class="text-[9px] sm:text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                    Daily Financial Breakdown ({{ count($chartPoints) }} Days)
                </h4>
            </div>

            <div class="overflow-x-auto rounded-xl border border-zinc-800/80">
                <table class="w-full text-left text-[10px] sm:text-xs">
                    <thead class="bg-zinc-950 text-zinc-400 font-bold border-b border-zinc-800 text-[9px] sm:text-[10px]">
                        <tr>
                            <th class="py-1.5 px-2">Date</th>
                            <th class="py-1.5 px-2">Sales</th>
                            <th class="py-1.5 px-2">Expenses</th>
                            <th class="py-1.5 px-2">Net Profit</th>
                            <th class="py-1.5 px-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50 bg-zinc-900/40">
                        @foreach(array_reverse($chartPoints) as $pt)
                            <tr class="hover:bg-zinc-800/40 transition-colors">
                                <td class="py-1.5 px-2 font-medium text-zinc-300 whitespace-nowrap text-[10px] sm:text-xs">
                                    {{ $pt['full_label'] }}
                                </td>
                                <td class="py-1.5 px-2 font-bold text-emerald-400 whitespace-nowrap text-[10px] sm:text-xs">
                                    {{ $currency }}{{ number_format($pt['sales'], 0) }}
                                </td>
                                <td class="py-1.5 px-2 font-bold text-rose-400 whitespace-nowrap text-[10px] sm:text-xs">
                                    {{ $currency }}{{ number_format($pt['expenses'], 0) }}
                                </td>
                                <td class="py-1.5 px-2 font-black whitespace-nowrap text-[10px] sm:text-xs {{ $pt['profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $pt['profit'] >= 0 ? '+' : '' }}{{ $currency }}{{ number_format($pt['profit'], 0) }}
                                </td>
                                <td class="py-1.5 px-2 text-right whitespace-nowrap">
                                    @if($pt['sales'] == 0 && $pt['expenses'] == 0)
                                        <span class="px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-semibold bg-zinc-800 text-zinc-500">No Activity</span>
                                    @elseif($pt['profit'] >= 0)
                                        <span class="px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Profitable</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Loss</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. BEST-SELLING ITEMS RANKING -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl space-y-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1">
                <span class="text-sm sm:text-base">🏆</span>
                <h3 class="font-black text-xs sm:text-sm text-white">Best Sellers</h3>
            </div>
            <span class="text-[9px] sm:text-[10px] text-zinc-400">Ranked by units sold</span>
        </div>

        <div class="space-y-1 pt-0.5">
            @forelse($bestSellingItems as $item)
                <div class="bg-zinc-950/80 border border-zinc-800/70 rounded-lg sm:rounded-xl p-2 sm:p-2.5 flex items-center justify-between gap-2 text-[10px] sm:text-xs">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="w-4 text-center font-black text-amber-400 text-[10px] sm:text-xs">
                            {{ $loop->iteration }}.
                        </span>
                        <span class="text-sm sm:text-base">
                            {{ $item->product?->image_emoji ?? '🍔' }}
                        </span>
                        <div>
                            <span class="font-bold text-zinc-100 text-[10px] sm:text-xs">{{ $item->product_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-right shrink-0">
                        <span class="font-semibold text-zinc-400 text-[9px] sm:text-[10px]">{{ $item->total_qty }} sold</span>
                        <span class="font-black text-emerald-400 text-[10px] sm:text-xs">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-[10px] sm:text-xs text-zinc-500 py-3 text-center">No sales recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
