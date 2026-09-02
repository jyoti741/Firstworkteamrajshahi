<div class="space-y-4 max-w-4xl mx-auto" x-data="{
    vibrate(ms = 40) {
        if (window.navigator && window.navigator.vibrate) {
            window.navigator.vibrate(ms);
        }
    }
}">

    <!-- Top Greeting & Payment Method Bar -->
    <div class="flex items-center justify-between pt-1 gap-2">
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight">
                {{ $greeting }}
            </h1>
            <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5">
                {{ bn_date(now(), $locale, 'l, d M') }} • {{ seller_trans('cashier') }}: <strong class="text-zinc-200">{{ auth()->user()->name }}</strong>
            </p>
        </div>

        <!-- Payment Method Toggle (Cash / bKash / Nagad) -->
        <div class="flex items-center bg-zinc-900 p-1 rounded-2xl border border-zinc-800 text-xs font-bold shadow-md shrink-0">
            <button type="button" 
                    @click="vibrate(20)"
                    wire:click="setPaymentMethod('cash')" 
                    class="px-2 sm:px-2.5 py-1.5 rounded-xl transition-all touch-press cursor-pointer {{ $paymentMethod === 'cash' ? 'bg-emerald-500 text-zinc-950 font-black shadow' : 'text-zinc-400 hover:text-zinc-200' }}">
                💵 {{ seller_trans('cash') }}
            </button>
            <button type="button" 
                    @click="vibrate(20)"
                    wire:click="setPaymentMethod('bkash')" 
                    class="px-2 sm:px-2.5 py-1.5 rounded-xl transition-all touch-press cursor-pointer {{ $paymentMethod === 'bkash' ? 'bg-pink-600 text-white font-black shadow' : 'text-zinc-400 hover:text-zinc-200' }}">
                📱 {{ seller_trans('bkash') }}
            </button>
            <button type="button" 
                    @click="vibrate(20)"
                    wire:click="setPaymentMethod('nagad')" 
                    class="px-2 sm:px-2.5 py-1.5 rounded-xl transition-all touch-press cursor-pointer {{ $paymentMethod === 'nagad' ? 'bg-orange-600 text-white font-black shadow' : 'text-zinc-400 hover:text-zinc-200' }}">
                🔶 {{ seller_trans('nagad') }}
            </button>
        </div>
    </div>

    <!-- Live Toast Message -->
    @if($feedbackMessage)
        <div class="py-2.5 px-4 bg-amber-500/20 border border-amber-500/40 rounded-2xl text-xs sm:text-sm text-amber-300 font-bold flex items-center justify-between shadow-lg animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping shrink-0"></span>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button type="button" wire:click="$set('feedbackMessage', null)" class="text-amber-300 hover:text-white font-black cursor-pointer ml-2">✕</button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- CART OPEN / CLOSE STATUS & TURN ON-OFF SWITCH -->
    <!-- ========================================== -->
    <div class="bg-zinc-900 border {{ $isCartOpen ? 'border-emerald-500/40 shadow-emerald-500/5' : 'border-rose-500/40 shadow-rose-500/5' }} rounded-3xl p-4 sm:p-5 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 transition-all">
        <!-- Status Indicator & Timings -->
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl {{ $isCartOpen ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }} flex items-center justify-center text-2xl shrink-0">
                {{ $isCartOpen ? '🟢' : '🔴' }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-black text-base {{ $isCartOpen ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $isCartOpen ? seller_trans('cart_is_open') : seller_trans('cart_is_closed') }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $isCartOpen ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                        {{ $isCartOpen ? seller_trans('taking_orders') : seller_trans('off_shift') }}
                    </span>
                </div>
                
                <!-- Opening Time / Closing Time Details -->
                <div class="text-xs text-zinc-400 mt-1 flex items-center gap-2 flex-wrap">
                    @if($isCartOpen)
                        <span>🌅 {{ seller_trans('opened_at') }}: <strong class="text-zinc-200 font-bold">{{ $currentBusinessDay?->opened_at ? bn_time($currentBusinessDay->opened_at, $locale) : bn_time(now(), $locale) }}</strong></span>
                        @if($currentBusinessDay?->openedBy)
                            <span>{{ seller_trans('by') }} <strong class="text-zinc-300">{{ $currentBusinessDay->openedBy->name }}</strong></span>
                        @endif
                    @else
                        <span>🌙 {{ seller_trans('closed_at') }}: <strong class="text-zinc-200 font-bold">{{ $currentBusinessDay?->closed_at ? bn_time($currentBusinessDay->closed_at, $locale) : bn_time(now(), $locale) }}</strong></span>
                        @if($currentBusinessDay?->closedBy)
                            <span>{{ seller_trans('by') }} <strong class="text-zinc-300">{{ $currentBusinessDay->closedBy->name }}</strong></span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Interactive Turn ON / Turn OFF Switch -->
        <div class="flex items-center justify-end">
            @if($isCartOpen)
                <button type="button" 
                        @click="vibrate(40)"
                        wire:click="openCloseModal"
                        class="w-full sm:w-auto px-4 py-2.5 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/40 rounded-2xl text-xs font-black transition-all flex items-center justify-center gap-2 touch-press cursor-pointer shadow-md">
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                    <span>{{ seller_trans('close_cart') }}</span>
                </button>
            @else
                <button type="button" 
                        @click="vibrate(40)"
                        wire:click="openCart"
                        class="w-full sm:w-auto px-5 py-2.5 bg-green-500 hover:bg-green-400 active:bg-green-600 text-zinc-950 border border-green-300/80 rounded-2xl text-xs sm:text-sm font-black transition-all flex items-center justify-center gap-2 touch-press cursor-pointer shadow-lg shadow-green-500/30">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-950 animate-ping"></span>
                    <span>{{ seller_trans('turn_on_cart') }}</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Today's Summary Card (Clean & Prominent with Section Sums) -->
    <div class="bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-3">
        <div class="grid grid-cols-2 gap-2.5 sm:gap-4">
            <!-- Today's Sales -->
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-400 uppercase tracking-wider truncate">{{ seller_trans('today_sales') }}</span>
                <div class="text-xl sm:text-2xl md:text-3xl font-black text-emerald-400 tracking-tight mt-0.5 sm:mt-1">
                    {{ bn_curr($todaySalesTotal, $locale, 0) }}
                </div>
            </div>

            <!-- Items Sold -->
            <div class="bg-zinc-950/70 border border-zinc-800/70 rounded-2xl p-3 sm:p-3.5 flex flex-col justify-between">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-400 uppercase tracking-wider truncate">{{ seller_trans('items_sold') }}</span>
                <div class="text-xl sm:text-2xl md:text-3xl font-black text-amber-400 tracking-tight mt-0.5 sm:mt-1">
                    {{ bn_num($todayItemsTotal, $locale) }}
                </div>
            </div>
        </div>

        <!-- Payment Breakdown Sums (Cash, bKash, Nagad) -->
        <div class="grid grid-cols-3 gap-2 sm:gap-3 pt-1 border-t border-zinc-800/60">
            <!-- Cash Sum -->
            <div class="bg-zinc-950/50 border border-zinc-800/50 rounded-xl p-2 sm:p-2.5 flex flex-col justify-between">
                <div class="flex items-center gap-1 text-[10px] sm:text-xs font-bold text-emerald-400 truncate">
                    <span>💵</span>
                    <span>{{ $locale === 'bn' ? 'ক্যাশ' : 'Cash' }}</span>
                </div>
                <div class="text-xs sm:text-sm md:text-base font-black text-zinc-100 mt-0.5">
                    {{ bn_curr($cashSalesTotal, $locale, 0) }}
                </div>
            </div>

            <!-- bKash Sum -->
            <div class="bg-zinc-950/50 border border-pink-900/30 rounded-xl p-2 sm:p-2.5 flex flex-col justify-between">
                <div class="flex items-center gap-1 text-[10px] sm:text-xs font-bold text-pink-400 truncate">
                    <span>📱</span>
                    <span>{{ $locale === 'bn' ? 'বিকাশ' : 'bKash' }}</span>
                </div>
                <div class="text-xs sm:text-sm md:text-base font-black text-pink-300 mt-0.5">
                    {{ bn_curr($bkashSalesTotal, $locale, 0) }}
                </div>
            </div>

            <!-- Nagad Sum -->
            <div class="bg-zinc-950/50 border border-orange-900/30 rounded-xl p-2 sm:p-2.5 flex flex-col justify-between">
                <div class="flex items-center gap-1 text-[10px] sm:text-xs font-bold text-orange-400 truncate">
                    <span>🔶</span>
                    <span>{{ $locale === 'bn' ? 'নগদ' : 'Nagad' }}</span>
                </div>
                <div class="text-xs sm:text-sm md:text-base font-black text-orange-300 mt-0.5">
                    {{ bn_curr($nagadSalesTotal, $locale, 0) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Pills (Horizontal Touch Bar) -->
    @if($categories->count() > 1)
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
            <button type="button" 
                    @click="vibrate(15)"
                    wire:click="selectCategory(null)" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all touch-press cursor-pointer {{ is_null($selectedCategoryId) ? 'bg-amber-500 text-zinc-950 shadow-md font-black' : 'bg-zinc-900 text-zinc-400 hover:text-zinc-200 border border-zinc-800' }}">
                {{ seller_trans('all_items') }} ({{ bn_num($products->count(), $locale) }})
            </button>
            @foreach($categories as $category)
                <button type="button" 
                        @click="vibrate(15)"
                        wire:click="selectCategory({{ $category->id }})" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all touch-press cursor-pointer flex items-center gap-1.5 {{ $selectedCategoryId === $category->id ? 'bg-amber-500 text-zinc-950 shadow-md font-black' : 'bg-zinc-900 text-zinc-400 hover:text-zinc-200 border border-zinc-800' }}">
                    <span>{{ $category->icon }}</span>
                    <span>{{ $category->displayName($locale) }}</span>
                </button>
            @endforeach
        </div>
    @endif

    <!-- QUICK SELL FOOD ITEMS LIST (Large Touch Cards with Cash, bKash, Nagad Options) -->
    <div class="space-y-3">
        @forelse($products as $product)
            @php
                $todayStats = $productTodaySales->get($product->id, ['count' => 0, 'revenue' => 0, 'cash_count' => 0, 'bkash_count' => 0, 'nagad_count' => 0]);
                $soldCount = $todayStats['count'];
                $cashCount = $todayStats['cash_count'] ?? 0;
                $bkashCount = $todayStats['bkash_count'] ?? 0;
                $nagadCount = $todayStats['nagad_count'] ?? 0;
                $isHighlighted = ($lastSoldProductId === $product->id);
                $foodDisplayName = $product->displayName($locale);
            @endphp
            <div class="bg-zinc-900 border {{ $isHighlighted ? 'border-amber-500 ring-2 ring-amber-500/30' : 'border-zinc-800 hover:border-zinc-700' }} rounded-3xl p-4 sm:p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-3.5 transition-all">
                
                <!-- Left: Food Info (Emoji, Name, Price, Sold Count & Payment Breakdown) -->
                <div class="flex items-center gap-3.5">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-3xl shrink-0">
                        {{ $product->image_emoji ?? '🍔' }}
                    </div>

                    <div>
                        <h3 class="font-black text-base sm:text-lg text-white leading-tight">
                            {{ $foodDisplayName }}
                        </h3>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <span class="text-base font-black text-amber-400">
                                {{ bn_curr($product->price, $locale, 0) }}
                            </span>
                            <span class="text-zinc-600">•</span>
                            <span class="text-xs font-bold text-zinc-400">
                                {{ seller_trans('sold') }}: <strong class="text-zinc-200">{{ bn_num($soldCount, $locale) }}</strong>
                            </span>
                            @if($soldCount > 0)
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-zinc-400 bg-zinc-950 px-2 py-0.5 rounded-lg border border-zinc-800">
                                    @if($cashCount > 0) <span class="text-emerald-400">💵 {{ bn_num($cashCount, $locale) }}</span> @endif
                                    @if($bkashCount > 0) <span class="text-pink-400">📱 {{ bn_num($bkashCount, $locale) }}</span> @endif
                                    @if($nagadCount > 0) <span class="text-orange-400">🔶 {{ bn_num($nagadCount, $locale) }}</span> @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Action Touch Buttons (Cash, bKash, Nagad options, [-] correction, and classic [+ SELL] button) -->
                <div class="flex flex-wrap items-center gap-2 justify-end w-full md:w-auto">
                    <!-- Cash, bKash, Nagad Quick Payment Options -->
                    <div class="flex items-center gap-1 bg-zinc-950/80 p-1 rounded-2xl border border-zinc-800">
                        <button type="button" 
                                @click="vibrate(30)"
                                wire:click="recordSale({{ $product->id }}, 1, 'cash')"
                                title="{{ $locale === 'bn' ? 'ক্যাশ এ বিক্রি' : 'Sell via Cash' }}"
                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-emerald-400 hover:bg-emerald-500/20 active:bg-emerald-500/30 transition-all flex items-center gap-1 touch-press cursor-pointer">
                            <span>💵</span>
                            <span class="text-[11px]">{{ $locale === 'bn' ? 'ক্যাশ' : 'Cash' }}</span>
                        </button>

                        <button type="button" 
                                @click="vibrate(30)"
                                wire:click="recordSale({{ $product->id }}, 1, 'bkash')"
                                title="{{ $locale === 'bn' ? 'বিকাশ এ বিক্রি' : 'Sell via bKash' }}"
                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-pink-400 hover:bg-pink-500/20 active:bg-pink-500/30 transition-all flex items-center gap-1 touch-press cursor-pointer">
                            <span>📱</span>
                            <span class="text-[11px]">{{ $locale === 'bn' ? 'বিকাশ' : 'bKash' }}</span>
                        </button>

                        <button type="button" 
                                @click="vibrate(30)"
                                wire:click="recordSale({{ $product->id }}, 1, 'nagad')"
                                title="{{ $locale === 'bn' ? 'নগদ এ বিক্রি' : 'Sell via Nagad' }}"
                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-orange-400 hover:bg-orange-500/20 active:bg-orange-500/30 transition-all flex items-center gap-1 touch-press cursor-pointer">
                            <span>🔶</span>
                            <span class="text-[11px]">{{ $locale === 'bn' ? 'নগদ' : 'Nagad' }}</span>
                        </button>
                    </div>

                    <!-- Decrement / Correction Button -->
                    @if($soldCount > 0)
                        <button type="button" 
                                @click="vibrate(30)"
                                wire:click="recordCorrection({{ $product->id }})"
                                title="{{ seller_trans('correct') }}"
                                class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-zinc-950 hover:bg-rose-950/40 text-zinc-400 hover:text-rose-300 border border-zinc-800 hover:border-rose-800/40 font-black text-xl flex items-center justify-center touch-press cursor-pointer shrink-0 transition-colors">
                            −
                        </button>
                    @endif

                    <!-- + SELL Button (Classic Primary 1-Tap Trigger As Before) -->
                    <button type="button" 
                            @click="vibrate(50)"
                            wire:click="recordSale({{ $product->id }})"
                            class="flex-1 sm:flex-none sm:min-w-[130px] h-11 sm:h-12 px-5 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 active:from-amber-600 active:to-orange-600 text-zinc-950 font-black text-sm sm:text-base flex items-center justify-center gap-2 shadow-lg shadow-orange-500/20 touch-press cursor-pointer border border-amber-400/40">
                        <span class="text-xl leading-none">+</span>
                        <span>{{ seller_trans('sell') }}</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 text-center">
                <span class="text-3xl">🍔</span>
                <h4 class="font-bold text-sm text-zinc-300 mt-2">{{ seller_trans('no_food_items') }}</h4>
                <p class="text-xs text-zinc-500 mt-1">{{ seller_trans('no_food_items_hint') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Recent Sales Stream (Quick Undo) -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">⏱️</span>
                <h3 class="font-bold text-sm text-zinc-200">{{ seller_trans('today_transactions') }}</h3>
            </div>
            <a href="{{ route('seller.today-sales') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                {{ seller_trans('view_all') }}
            </a>
        </div>

        <div class="space-y-2">
            @forelse($recentSales as $sale)
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3 flex items-center justify-between gap-3 text-xs">
                    <div class="truncate">
                        <div class="font-bold text-zinc-100 truncate">
                            @foreach($sale->items as $item)
                                @php
                                    $itemDisplayName = $item->product ? $item->product->displayName($locale) : $item->product_name;
                                @endphp
                                {{ $itemDisplayName }} <span class="text-amber-400 font-bold">×{{ bn_num($item->quantity, $locale) }}</span>@if(!$loop->last), @endif
                            @endforeach
                        </div>
                        <p class="text-[10px] text-zinc-500 mt-0.5">
                            {{ bn_time($sale->created_at, $locale) }} • <span class="uppercase font-semibold text-zinc-400">{{ seller_trans($sale->payment_method) }}</span> • {{ $sale->invoice_no }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <span class="font-black text-emerald-400 text-sm">
                            {{ bn_curr($sale->total_amount, $locale, 0) }}
                        </span>

                        <button type="button" 
                                @click="vibrate(30)"
                                wire:click="undoLastSale({{ $sale->id }})"
                                wire:confirm="{{ seller_trans('void_confirm') }}"
                                class="px-2.5 py-1 bg-zinc-800 hover:bg-rose-950/40 text-zinc-400 hover:text-rose-300 border border-zinc-700/50 rounded-lg text-[10px] font-bold touch-press cursor-pointer">
                            {{ seller_trans('undo') }}
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-500 text-center py-4">{{ seller_trans('no_sales_yet') }}</p>
            @endforelse
        </div>
    </div>

    <!-- Seller Close Cart Modal -->
    @if($showCloseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                
                @if(!$isCartClosedSubmitted)
                    <!-- Initial Form State: Session Summary & Confirmation -->
                    <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🔴</span>
                            <h3 class="font-bold text-base sm:text-lg text-white">{{ seller_trans('close_cart') }}</h3>
                        </div>
                        <button type="button" wire:click="dismissCloseModal" class="text-zinc-400 hover:text-white font-black cursor-pointer text-base">✕</button>
                    </div>

                    <!-- Closing Summary: Total Sales & Total Items Sold in current session -->
                    <div class="grid grid-cols-2 gap-2.5 bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-3.5">
                        <div>
                            <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ seller_trans('total_sales') }}</span>
                            <span class="text-lg sm:text-xl font-black text-emerald-400 mt-0.5 block">{{ bn_curr($todaySalesTotal, $locale, 0) }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block">{{ seller_trans('total_items_sold') }}</span>
                            <span class="text-lg sm:text-xl font-black text-amber-400 mt-0.5 block">{{ bn_num($todayItemsTotal, $locale) }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-zinc-400 text-center py-1">
                        {{ $locale === 'bn' ? 'বর্তমান শিফট সমাপ্ত করতে কার্ট বন্ধ নিশ্চিত করুন।' : 'Confirm closing this cart session.' }}
                    </p>

                    <!-- Action Buttons: Cancel / Close Cart -->
                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-zinc-800">
                        <button type="button" wire:click="dismissCloseModal" class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 cursor-pointer transition-colors">
                            {{ seller_trans('cancel') }}
                        </button>
                        <button type="button" 
                                wire:click="closeCart"
                                class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 shadow-lg shadow-rose-600/30 touch-press cursor-pointer flex items-center justify-center gap-1.5">
                            <span>🔒</span>
                            <span>{{ seller_trans('close_cart') }}</span>
                        </button>
                    </div>

                @else
                    <!-- Success State: Cart Closed ✓ & Summary -->
                    <div class="text-center py-2 space-y-4">
                        <div class="w-14 h-14 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-3xl mx-auto shadow-lg shadow-emerald-500/10">
                            ✓
                        </div>

                        <div>
                            <h3 class="text-lg sm:text-xl font-black text-emerald-400">
                                {{ seller_trans('cart_closed_success') }}
                            </h3>
                        </div>

                        <!-- Session Final Summary Card -->
                        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 text-left space-y-2.5">
                            <div class="flex items-center justify-between text-xs sm:text-sm text-zinc-300">
                                <span>{{ seller_trans('total_sales') }}:</span>
                                <span class="font-bold text-white">{{ bn_curr($closedSalesTotal, $locale, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs sm:text-sm text-zinc-300">
                                <span>{{ seller_trans('total_items_sold') }}:</span>
                                <span class="font-bold text-amber-400">{{ bn_num($closedItemsTotal, $locale) }}</span>
                            </div>
                        </div>

                        <!-- Done Action Button -->
                        <button type="button" 
                                wire:click="dismissCloseModal"
                                class="w-full py-3 rounded-2xl text-xs sm:text-sm font-black text-zinc-950 bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-300 hover:to-teal-300 shadow-lg shadow-emerald-500/20 touch-press cursor-pointer">
                            {{ seller_trans('done') }}
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>
