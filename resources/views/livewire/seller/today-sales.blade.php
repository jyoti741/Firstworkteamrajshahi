<div class="space-y-5 max-w-5xl mx-auto">

    <!-- Page Title & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-zinc-900 border border-zinc-800 rounded-2xl p-4 shadow-md">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xl">📋</span>
                <h2 class="text-base sm:text-lg font-bold text-white">{{ seller_trans('today_sales_records') }}</h2>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">{{ bn_date(now(), $locale, 'l, F j, Y') }}</p>
        </div>

        <a href="{{ route('seller.quick-sell') }}" 
           class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-zinc-950 font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-md shadow-orange-500/20 touch-press">
            <span>🛒</span> {{ seller_trans('back_to_quick_sell') }}
        </a>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between">
            <span class="text-[11px] sm:text-xs text-zinc-400 font-semibold truncate">{{ seller_trans('total_revenue') }}</span>
            <span class="text-base sm:text-lg md:text-2xl font-black text-emerald-400 mt-0.5 sm:mt-1 tracking-tight">
                {{ bn_curr($totalRevenue, $locale, 0) }}
            </span>
            <span class="text-[9px] sm:text-[10px] text-zinc-500 mt-0.5">{{ bn_num($totalOrders, $locale) }} {{ seller_trans('completed_orders') }}</span>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between">
            <span class="text-[11px] sm:text-xs text-zinc-400 font-semibold truncate">{{ seller_trans('items_sold') }}</span>
            <span class="text-base sm:text-lg md:text-2xl font-black text-amber-400 mt-0.5 sm:mt-1 tracking-tight">
                {{ bn_num($totalItems, $locale) }}
            </span>
            <span class="text-[9px] sm:text-[10px] text-zinc-500 mt-0.5">{{ seller_trans('units_prepared') }}</span>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between">
            <span class="text-[11px] sm:text-xs text-zinc-400 font-semibold truncate">{{ seller_trans('cash_collected') }}</span>
            <span class="text-base sm:text-lg md:text-2xl font-black text-zinc-100 mt-0.5 sm:mt-1 tracking-tight">
                {{ bn_curr($cashTotal, $locale, 0) }}
            </span>
            <span class="text-[9px] sm:text-[10px] text-zinc-500 mt-0.5">{{ seller_trans('cash_drawer') }}</span>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between">
            <span class="text-[11px] sm:text-xs text-zinc-400 font-semibold truncate">{{ seller_trans('digital_payments') }}</span>
            <span class="text-base sm:text-lg md:text-2xl font-black text-pink-400 mt-0.5 sm:mt-1 tracking-tight">
                {{ bn_curr($bkashTotal + $nagadTotal, $locale, 0) }}
            </span>
            <span class="text-[9px] sm:text-[10px] text-zinc-500 mt-0.5">{{ seller_trans('mfs_received') }}</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-3.5 flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
            <input type="text" 
                   wire:model.live.debounce.200ms="search" 
                   placeholder="{{ seller_trans('search_placeholder') }}" 
                   class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-zinc-500 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select wire:model.live="paymentFilter" class="flex-1 sm:flex-none bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                <option value="all">{{ seller_trans('all_payment_methods') }}</option>
                <option value="cash">{{ seller_trans('cash_only') }}</option>
                <option value="bkash">{{ seller_trans('bkash_only') }}</option>
                <option value="nagad">{{ seller_trans('nagad_only') }}</option>
                <option value="card">{{ seller_trans('card_only') }}</option>
            </select>

            <select wire:model.live="statusFilter" class="flex-1 sm:flex-none bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                <option value="all">{{ seller_trans('all_statuses') }}</option>
                <option value="completed">{{ seller_trans('completed') }}</option>
                <option value="cancelled">{{ seller_trans('cancelled') }}</option>
            </select>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="space-y-2.5">
        @forelse($sales as $sale)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 shadow-sm hover:border-zinc-700 transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono font-bold text-xs text-zinc-300">{{ $sale->invoice_no }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sale->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                {{ seller_trans($sale->status) }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $sale->payment_method === 'cash' ? 'bg-zinc-800 text-zinc-300' : ($sale->payment_method === 'bkash' ? 'bg-pink-900/40 text-pink-300' : 'bg-orange-900/40 text-orange-300') }}">
                                {{ seller_trans($sale->payment_method) }}
                            </span>
                        </div>

                        <!-- Items Breakdown -->
                        <div class="text-xs text-zinc-200 font-medium pt-1">
                            @foreach($sale->items as $item)
                                @php
                                    $itemDisplayName = $item->product ? $item->product->displayName($locale) : $item->product_name;
                                @endphp
                                <div class="flex items-center gap-1.5 sm:gap-2 py-0.5">
                                    <span class="text-amber-400 font-bold text-[11px] sm:text-xs">×{{ bn_num($item->quantity, $locale) }}</span>
                                    <span class="text-xs">{{ $itemDisplayName }}</span>
                                    <span class="text-zinc-500 text-[10px] sm:text-[11px]">({{ bn_curr($item->unit_price, $locale, 0) }} {{ seller_trans('each') }})</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-[10px] sm:text-[11px] text-zinc-500 pt-1">
                            {{ seller_trans('recorded_at') }} {{ bn_time($sale->created_at, $locale, 'h:i:s A') }} {{ seller_trans('by') }} <span class="text-zinc-400">{{ $sale->user->name }}</span>
                        </div>
                    </div>

                    <!-- Right Price and Void Action -->
                    <div class="flex flex-col items-end gap-1.5 sm:gap-2 shrink-0">
                        <div class="text-sm sm:text-base font-black {{ $sale->status === 'completed' ? 'text-emerald-400' : 'text-zinc-500 line-through' }}">
                            {{ bn_curr($sale->total_amount, $locale, 0) }}
                        </div>

                        @if($sale->status === 'completed')
                            <button type="button" 
                                    wire:click="cancelSale({{ $sale->id }})"
                                    wire:confirm="{{ seller_trans('void_restore_confirm') }}"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-rose-400 hover:text-white bg-rose-950/30 hover:bg-rose-900/60 border border-rose-800/40 transition-colors cursor-pointer touch-press">
                                {{ seller_trans('void_cancel') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 text-center">
                <span class="text-3xl">🧾</span>
                <h4 class="text-sm font-bold text-zinc-300 mt-2">{{ seller_trans('no_matching_sales') }}</h4>
                <p class="text-xs text-zinc-500 mt-1">{{ seller_trans('try_reset_filter') }}</p>
            </div>
        @endforelse

        <div class="pt-2">
            {{ $sales->links() }}
        </div>
    </div>
</div>
