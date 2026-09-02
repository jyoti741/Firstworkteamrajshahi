<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Page Header & Filter Tabs -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>🛒</span> Sales
                </h1>
                <p class="text-xs text-zinc-400">Review sales performance and transaction receipts.</p>
            </div>

            <button type="button" 
                    wire:click="exportCsv"
                    class="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-zinc-800 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow cursor-pointer">
                <span>📥</span> <span class="hidden sm:inline">Export CSV</span>
            </button>
        </div>

        <!-- Simple Filter Tabs: Today | Yesterday | This Week | This Month -->
        <div class="flex items-center bg-zinc-900 p-1.5 rounded-2xl border border-zinc-800 text-xs font-bold overflow-x-auto scrollbar-none gap-1 shadow-md">
            <button type="button" 
                    wire:click="setDateFilter('today')" 
                    class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'today' ? 'bg-amber-500 text-zinc-950 shadow-md shadow-amber-500/20 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                Today
            </button>
            <button type="button" 
                    wire:click="setDateFilter('yesterday')" 
                    class="flex-1 py-2 px-3 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'yesterday' ? 'bg-amber-500 text-zinc-950 shadow-md shadow-amber-500/20 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                Yesterday
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

    <!-- Summary Headline Card -->
    <div class="bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <h2 class="font-bold text-sm sm:text-base text-zinc-300">
                {{ match($dateFilter) { 'yesterday' => "Yesterday's Sales", 'this_week' => "This Week's Sales", 'this_month' => "This Month's Sales", 'all' => "All Time Sales", default => "Today's Sales" } }}
            </h2>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-zinc-800 text-zinc-400">
                {{ $totalOrdersCount }} transactions
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4">
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Total Sales</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1 block">
                    {{ $currency }}{{ number_format($totalSalesAmount, 0) }}
                </span>
            </div>

            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Items Sold</span>
                <span class="text-2xl sm:text-3xl font-black text-amber-400 mt-1 block">
                    {{ number_format($totalItemsCount) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Food Items Sold Breakdown List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-base text-white flex items-center gap-2">
                <span>🍔</span> Items Breakdown
            </h3>
            <span class="text-xs text-zinc-400">{{ $itemBreakdown->count() }} items sold</span>
        </div>

        <div class="space-y-2 pt-1">
            @forelse($itemBreakdown as $item)
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3.5 flex items-center justify-between gap-3 text-xs sm:text-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">
                            {{ $item->product?->image_emoji ?? '🍔' }}
                        </span>
                        <div>
                            <span class="font-bold text-zinc-100">{{ $item->product_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-right shrink-0">
                        <span class="font-bold text-zinc-400 text-xs sm:text-sm">{{ $item->total_qty }} sold</span>
                        <span class="font-black text-emerald-400 text-xs sm:text-sm">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-500 py-6 text-center">No food items sold in this time range.</p>
            @endforelse
        </div>
    </div>

    <!-- Individual Transactions List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-base text-white flex items-center gap-2">
                <span>🧾</span> Transactions History
            </h3>
            <input type="text" 
                   wire:model.live.debounce.250ms="search" 
                   placeholder="🔍 Search..." 
                   class="bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 w-36 sm:w-48">
        </div>

        <div class="space-y-3">
            @forelse($sales as $sale)
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-4 flex flex-col gap-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono font-bold text-xs text-zinc-200">{{ $sale->invoice_no }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sale->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase bg-zinc-800 text-zinc-300">
                                    {{ $sale->payment_method }}
                                </span>
                            </div>
                            <p class="text-[11px] text-zinc-400 mt-1">
                                {{ $sale->created_at->format('d M Y, h:i A') }} • Staff: <strong class="text-zinc-300">{{ $sale->user->name }}</strong>
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-base sm:text-lg font-black {{ $sale->status === 'completed' ? 'text-emerald-400' : 'text-zinc-500 line-through' }}">
                                {{ $currency }}{{ number_format($sale->total_amount, 0) }}
                            </div>
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="text-xs text-zinc-300 bg-zinc-900/60 p-2.5 rounded-xl border border-zinc-800/50">
                        @foreach($sale->items as $item)
                            <span class="font-medium">{{ $item->product_name }}</span> <span class="text-amber-400 font-bold">×{{ $item->quantity }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-zinc-800/60">
                        <button type="button" 
                                wire:click="viewReceipt({{ $sale->id }})"
                                class="px-3 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold rounded-lg cursor-pointer">
                            Receipt
                        </button>

                        @if($sale->status === 'completed')
                            <button type="button" 
                                    wire:click="cancelSale({{ $sale->id }})"
                                    wire:confirm="Void and refund this sale #{{ $sale->invoice_no }}? Stock will be reversed."
                                    class="px-3 py-1 bg-rose-950/30 hover:bg-rose-900/60 text-rose-400 border border-rose-800/40 text-xs font-semibold rounded-lg cursor-pointer">
                                Void Sale
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-500 text-center py-6">No transactions found.</p>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Receipt Modal -->
    @if($viewingSale)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-sm p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <div class="font-bold text-sm text-white">Invoice #{{ $viewingSale->invoice_no }}</div>
                    <button type="button" wire:click="closeReceipt" class="text-zinc-400 hover:text-white">✕</button>
                </div>

                <div class="text-xs space-y-3">
                    <div class="text-center pb-2 border-b border-dashed border-zinc-800">
                        <h4 class="font-bold text-base text-white">{{ \App\Models\CartSetting::cartName() }}</h4>
                        <p class="text-[11px] text-zinc-400">{{ $viewingSale->created_at->format('d M Y, h:i A') }}</p>
                        <p class="text-[11px] text-zinc-400">Cashier: {{ $viewingSale->user->name }}</p>
                    </div>

                    <div class="space-y-1.5">
                        @foreach($viewingSale->items as $item)
                            <div class="flex justify-between">
                                <span>{{ $item->product_name }} ×{{ $item->quantity }}</span>
                                <span class="font-bold text-white">{{ $currency }}{{ number_format($item->subtotal, 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2 border-t border-zinc-800 flex justify-between text-sm font-black">
                        <span>Total</span>
                        <span class="text-emerald-400">{{ $currency }}{{ number_format($viewingSale->total_amount, 0) }}</span>
                    </div>

                    <div class="flex justify-between text-[11px] text-zinc-400">
                        <span>Payment Method</span>
                        <span class="uppercase font-semibold text-zinc-200">{{ $viewingSale->payment_method }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-zinc-800">
                    <button type="button" wire:click="closeReceipt" class="w-full py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-bold">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
