<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Page Header & Filter Tabs -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2">
                    <span>🛒</span> Sales
                </h1>
                <p class="text-xs text-[#8D7B70] font-medium">Review sales performance and transaction receipts.</p>
            </div>

            <button type="button" wire:click="exportCsv"
                class="px-3.5 py-2 bg-white hover:bg-[#F8F3EA] text-[#2B1E16] border border-[#EFE7DE] rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <span>📥</span> <span class="hidden sm:inline">Export CSV</span>
            </button>
        </div>

        <!-- Simple Filter Tabs -->
        <div
            class="flex items-center bg-white p-1 rounded-2xl border border-[#EFE7DE] text-xs font-bold overflow-x-auto scrollbar-none gap-1 shadow-2xs">
            <button type="button" wire:click="setDateFilter('today')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'today' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                Today
            </button>
            <button type="button" wire:click="setDateFilter('yesterday')"
                class="shrink-0 py-2 px-3.5 rounded-xl whitespace-nowrap text-center transition-all touch-press {{ $dateFilter === 'yesterday' ? 'bg-[#F26522] text-white shadow-2xs font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                Yesterday
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

    <!-- Summary Headline Card -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <h2 class="font-extrabold text-sm sm:text-base text-[#2B1E16]">
                {{ match ($dateFilter) { 'yesterday' => "Yesterday's Sales", 'this_week' => "This Week's Sales", 'this_month' => "This Month's Sales", 'all' => "All Time Sales", default => "Today's Sales"} }}
            </h2>
            <span
                class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#F8F3EA] text-[#554338] border border-[#EFE7DE]">
                {{ $totalOrdersCount }} transactions
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-3">
            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3.5 overflow-hidden">
                <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">Total
                    Sales</span>
                <div class="text-2xl sm:text-3xl font-black text-[#1E8E3E] truncate">
                    {{ $currency }}{{ number_format($totalSalesAmount, 0) }}
                </div>
                <p class="text-xs text-[#8D7B70] mt-0.5 font-medium truncate">Total recorded in this period.</p>
            </div>

            <div class="bg-[#F8F3EA] border border-[#EFE7DE]/80 rounded-2xl p-3.5">
                <span class="text-[10px] sm:text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">Items
                    Sold</span>
                <span class="text-xl sm:text-2xl font-black text-[#F26522] mt-0.5 block">
                    {{ number_format($totalItemsCount) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Food Items Sold Breakdown List -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                <span>🍔</span> Items Breakdown
            </h3>
            <span class="text-xs text-[#8D7B70] font-medium">{{ $itemBreakdown->count() }} items sold</span>
        </div>

        <div class="space-y-2 pt-1">
            @forelse($itemBreakdown as $item)
                <div
                    class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 sm:p-3 flex items-center justify-between gap-2.5 text-xs sm:text-sm">
                    <div class="flex items-center gap-2 sm:gap-2.5 min-w-0 flex-1">
                        <div class="w-6 h-6 rounded-lg overflow-hidden shrink-0 flex items-center justify-center bg-white border border-[#EFE7DE]">
                            @if($item->product?->image_url)
                                <img src="{{ $item->product->image_url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm select-none">{{ $item->product?->image_emoji ?? '🍔' }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-[#2B1E16] truncate block">{{ $item->product_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 text-right shrink-0">
                        <span class="font-semibold text-[#8D7B70] text-[11px] sm:text-xs whitespace-nowrap">{{ $item->total_qty }} sold</span>
                        <span
                            class="font-black text-[#1E8E3E] text-xs sm:text-sm whitespace-nowrap">{{ $currency }}{{ number_format($item->total_revenue, 0) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] py-6 text-center">No food items sold in this time range.</p>
            @endforelse
        </div>
    </div>

    <!-- Transaction Receipts List -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                <span>🧾</span> Transactions History
            </h3>
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="🔍 Search receipts..."
                class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3 py-1.5 text-xs text-[#2B1E16] placeholder-[#8D7B70] focus:outline-none focus:ring-2 focus:ring-[#F26522] w-full sm:w-48 font-medium">
        </div>

        <div class="space-y-2.5">
            @forelse($sales as $sale)
                <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5 flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-mono font-bold text-xs text-[#8D7B70]">{{ $sale->invoice_no }}</span>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sale->status === 'completed' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white border border-[#EFE7DE] text-[#554338]">
                                    {{ $sale->payment_method }}
                                </span>
                            </div>
                            <p class="text-[11px] text-[#8D7B70] mt-0.5 font-medium">
                                {{ $sale->created_at->format('d M Y, h:i A') }} • Staff: <strong
                                    class="text-[#2B1E16]">{{ $sale->user->name }}</strong>
                            </p>
                        </div>

                        <div class="text-right">
                            <div
                                class="text-sm sm:text-base font-black {{ $sale->status === 'completed' ? 'text-[#1E8E3E]' : 'text-[#8D7B70] line-through' }}">
                                {{ $currency }}{{ number_format($sale->total_amount, 0) }}
                            </div>
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="text-xs text-[#2B1E16] bg-white p-2.5 rounded-xl border border-[#EFE7DE]">
                        @foreach($sale->items as $item)
                            <span class="font-semibold">{{ $item->product_name }}</span> <span
                                class="text-[#F26522] font-black">×{{ $item->quantity }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="viewReceipt({{ $sale->id }})"
                            class="px-3 py-1 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] text-xs font-bold rounded-lg cursor-pointer">
                            Receipt
                        </button>

                        @if($sale->status === 'completed')
                            <button type="button" wire:click="cancelSale({{ $sale->id }})"
                                wire:confirm="Void and refund this sale #{{ $sale->invoice_no }}? Stock will be reversed."
                                class="px-3 py-1 bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA] text-xs font-bold rounded-lg cursor-pointer">
                                Void Sale
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#8D7B70] text-center py-6">No transactions found.</p>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Receipt Modal -->
    @if($viewingSale)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-sm p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="font-extrabold text-sm text-[#2B1E16]">Invoice #{{ $viewingSale->invoice_no }}</div>
                    <button type="button" wire:click="closeReceipt"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer">✕</button>
                </div>

                <div class="text-xs space-y-3">
                    <div class="text-center pb-2 border-b border-dashed border-[#EFE7DE]">
                        <h4 class="font-extrabold text-base text-[#2B1E16]">{{ \App\Models\CartSetting::cartName() }}</h4>
                        <p class="text-[11px] text-[#8D7B70]">{{ $viewingSale->created_at->format('d M Y, h:i A') }}</p>
                        <p class="text-[11px] text-[#8D7B70]">Cashier: {{ $viewingSale->user->name }}</p>
                    </div>

                    <div class="space-y-1.5">
                        @foreach($viewingSale->items as $item)
                            <div class="flex justify-between text-[#554338]">
                                <span>{{ $item->product_name }} ×{{ $item->quantity }}</span>
                                <span
                                    class="font-bold text-[#2B1E16]">{{ $currency }}{{ number_format($item->subtotal, 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2 border-t border-[#EFE7DE] flex justify-between text-sm font-black">
                        <span class="text-[#2B1E16]">Total</span>
                        <span
                            class="text-[#1E8E3E]">{{ $currency }}{{ number_format($viewingSale->total_amount, 0) }}</span>
                    </div>

                    <div class="flex justify-between text-[11px] text-[#8D7B70]">
                        <span>Payment Method</span>
                        <span class="uppercase font-bold text-[#2B1E16]">{{ $viewingSale->payment_method }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-[#EFE7DE]">
                    <button type="button" wire:click="closeReceipt"
                        class="w-full py-2.5 bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] text-[#2B1E16] rounded-xl text-xs font-bold cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>