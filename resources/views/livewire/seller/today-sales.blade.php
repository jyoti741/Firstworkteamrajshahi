<div class="space-y-4 max-w-2xl mx-auto">

    <!-- Page Title & Header Card -->
    <div
        class="flex items-center justify-between gap-3 bg-white border border-[#EFE7DE] rounded-2xl p-3.5 sm:p-4 shadow-2xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xl">📋</span>
                <h2 class="text-sm sm:text-base font-extrabold text-[#2B1E16]">{{ seller_trans('today_sales_records') }}
                </h2>
            </div>
            <p class="text-[11px] text-[#8D7B70] mt-0.5 font-medium">{{ bn_date(now(), $locale, 'l, F j, Y') }}</p>
        </div>

        <a href="{{ route('seller.quick-sell') }}"
            class="px-3.5 py-2 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-2xs touch-press transition-colors">
            <span>🛒</span> <span>{{ seller_trans('back_to_quick_sell') }}</span>
        </a>
    </div>

    <!-- Summary Metrics Grid (4 Equal White Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5">
        <!-- Total Revenue -->
        <div class="bg-white border border-[#EFE7DE] rounded-2xl p-3 flex flex-col justify-between shadow-2xs">
            <span
                class="text-[10px] sm:text-[11px] text-[#8D7B70] font-bold uppercase tracking-wide truncate">{{ seller_trans('total_revenue') }}</span>
            <span class="text-base sm:text-xl font-black text-[#1E8E3E] mt-0.5 tracking-tight">
                {{ bn_curr($totalRevenue, $locale, 0) }}
            </span>
            <span
                class="text-[9px] sm:text-[10px] text-[#8D7B70] mt-0.5 font-medium">{{ bn_num($totalOrders, $locale) }}
                {{ seller_trans('completed_orders') }}</span>
        </div>

        <!-- Items Sold -->
        <div class="bg-white border border-[#EFE7DE] rounded-2xl p-3 flex flex-col justify-between shadow-2xs">
            <span
                class="text-[10px] sm:text-[11px] text-[#8D7B70] font-bold uppercase tracking-wide truncate">{{ seller_trans('items_sold') }}</span>
            <span class="text-base sm:text-xl font-black text-[#F26522] mt-0.5 tracking-tight">
                {{ bn_num($totalItems, $locale) }}
            </span>
            <span
                class="text-[9px] sm:text-[10px] text-[#8D7B70] mt-0.5 font-medium">{{ seller_trans('units_prepared') }}</span>
        </div>

        <!-- Cash Collected -->
        <div class="bg-white border border-[#EFE7DE] rounded-2xl p-3 flex flex-col justify-between shadow-2xs">
            <span
                class="text-[10px] sm:text-[11px] text-[#8D7B70] font-bold uppercase tracking-wide truncate">{{ seller_trans('cash_collected') }}</span>
            <span class="text-base sm:text-xl font-black text-[#2B1E16] mt-0.5 tracking-tight">
                {{ bn_curr($cashTotal, $locale, 0) }}
            </span>
            <span
                class="text-[9px] sm:text-[10px] text-[#8D7B70] mt-0.5 font-medium">{{ seller_trans('cash_drawer') }}</span>
        </div>

        <!-- Digital Payments (bKash + Nagad) -->
        <div class="bg-white border border-[#EFE7DE] rounded-2xl p-3 flex flex-col justify-between shadow-2xs">
            <span
                class="text-[10px] sm:text-[11px] text-[#8D7B70] font-bold uppercase tracking-wide truncate">{{ seller_trans('digital_payments') }}</span>
            <span class="text-base sm:text-xl font-black text-[#BE185D] mt-0.5 tracking-tight">
                {{ bn_curr($bkashTotal + $nagadTotal, $locale, 0) }}
            </span>
            <span
                class="text-[9px] sm:text-[10px] text-[#8D7B70] mt-0.5 font-medium">{{ seller_trans('mfs_received') }}</span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div
        class="bg-white border border-[#EFE7DE] rounded-2xl p-3 flex flex-col sm:flex-row items-center gap-2.5 shadow-2xs">
        <div class="relative flex-1 w-full">
            <input type="text" wire:model.live.debounce.200ms="search"
                placeholder="{{ seller_trans('search_placeholder') }}"
                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2 text-xs text-[#2B1E16] placeholder-[#8D7B70] focus:ring-2 focus:ring-[#F26522] focus:outline-none font-medium">
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select wire:model.live="paymentFilter"
                class="flex-1 sm:flex-none bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3 py-2 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none font-semibold cursor-pointer">
                <option value="all">{{ seller_trans('all_payment_methods') }}</option>
                <option value="cash">{{ seller_trans('cash_only') }}</option>
                <option value="bkash">{{ seller_trans('bkash_only') }}</option>
                <option value="nagad">{{ seller_trans('nagad_only') }}</option>
                <option value="card">{{ seller_trans('card_only') }}</option>
            </select>

            <select wire:model.live="statusFilter"
                class="flex-1 sm:flex-none bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3 py-2 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none font-semibold cursor-pointer">
                <option value="all">{{ seller_trans('all_statuses') }}</option>
                <option value="completed">{{ seller_trans('completed') }}</option>
                <option value="cancelled">{{ seller_trans('cancelled') }}</option>
            </select>
        </div>
    </div>

    <!-- Transactions Stream Cards -->
    <div class="space-y-2.5">
        @forelse($sales as $sale)
            <div
                class="bg-white border border-[#EFE7DE] rounded-2xl p-3.5 sm:p-4 shadow-2xs hover:border-[#F26522]/30 transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <!-- Invoice, Status Badge, and Branded Payment Pill -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-mono font-bold text-xs text-[#8D7B70]">{{ $sale->invoice_no }}</span>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sale->status === 'completed' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                                {{ seller_trans($sale->status) }}
                            </span>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sale->payment_method === 'cash' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : ($sale->payment_method === 'bkash' ? 'bg-[#FDF2F8] text-[#BE185D] border border-pink-200' : 'bg-[#FFF7ED] text-[#C2410C] border border-orange-200') }}">
                                {{ seller_trans($sale->payment_method) }}
                            </span>
                        </div>

                        <!-- Items Breakdown with Orange Counts -->
                        <div class="text-xs text-[#2B1E16] font-semibold pt-1">
                            @foreach($sale->items as $item)
                                @php
                                    $itemDisplayName = $item->product ? $item->product->displayName($locale) : $item->product_name;
                                @endphp
                                <div class="flex items-center gap-1.5 py-0.5">
                                    <span
                                        class="text-[#F26522] font-extrabold text-[11px] sm:text-xs">×{{ bn_num($item->quantity, $locale) }}</span>
                                    <span class="text-xs text-[#2B1E16]">{{ $itemDisplayName }}</span>
                                    <span
                                        class="text-[#8D7B70] text-[10px] sm:text-[11px] font-normal">({{ bn_curr($item->unit_price, $locale, 0) }}
                                        {{ seller_trans('each') }})</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-[10px] sm:text-[11px] text-[#8D7B70] pt-1 font-medium">
                            {{ seller_trans('recorded_at') }} {{ bn_time($sale->created_at, $locale, 'h:i:s A') }}
                            {{ seller_trans('by') }} <span
                                class="text-[#554338] font-semibold">{{ $sale->user->name }}</span>
                        </div>
                    </div>

                    <!-- Right Price and Void Action -->
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <div
                            class="text-sm sm:text-base font-black {{ $sale->status === 'completed' ? 'text-[#1E8E3E]' : 'text-[#8D7B70] line-through' }}">
                            {{ bn_curr($sale->total_amount, $locale, 0) }}
                        </div>

                        @if($sale->status === 'completed')
                            <button type="button" wire:click="cancelSale({{ $sale->id }})"
                                wire:confirm="{{ seller_trans('void_restore_confirm') }}"
                                class="px-2.5 py-1 rounded-xl text-[10px] font-bold text-[#DC2626] hover:bg-[#FEF2F2] border border-[#FECACA] transition-colors cursor-pointer touch-press shadow-2xs">
                                {{ seller_trans('void_cancel') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-[#EFE7DE] rounded-2xl p-8 text-center shadow-2xs">
                <span class="text-3xl">🧾</span>
                <h4 class="font-extrabold text-sm text-[#2B1E16] mt-2">{{ seller_trans('no_matching_sales') }}</h4>
                <p class="text-xs text-[#8D7B70] mt-1 font-medium">{{ seller_trans('try_reset_filter') }}</p>
            </div>
        @endforelse

        <div class="pt-2">
            {{ $sales->links() }}
        </div>
    </div>
</div>