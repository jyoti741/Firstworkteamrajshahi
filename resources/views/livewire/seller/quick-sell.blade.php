<div class="space-y-3.5 max-w-2xl mx-auto" x-data="{
    vibrate(ms = 40) {
        if (window.navigator && window.navigator.vibrate) {
            window.navigator.vibrate(ms);
        }
    }
}">

    <!-- 1. Top Cashier Greeting & Cart Live Status Card (Exact Mockup Match) -->
    <div
        class="bg-white rounded-2xl border border-[#EFE7DE] p-3 sm:p-4 flex items-center justify-between gap-2 sm:gap-2.5 shadow-2xs">
        <!-- Left: Cashier Avatar, Greeting & Seller Info -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div
                class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-[#FFF0E6] border border-[#FED7AA] flex items-center justify-center text-xl shrink-0 overflow-hidden shadow-2xs">
                👨‍🍳
            </div>
            <div class="truncate">
                <h1 class="text-xs sm:text-sm md:text-base font-extrabold text-[#2B1E16] leading-tight truncate">
                    {{ $greeting }}, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-[10px] sm:text-[11px] text-[#8D7B70] font-medium mt-0.5 truncate">
                    {{ seller_trans('staff') }}: <span
                        class="text-[#554338] font-bold">{{ auth()->user()->name }}</span>
                </p>
            </div>
        </div>

        <!-- Right: Status Badge & Close/Open Cart Button -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <!-- Live Status Capsule -->
            <div
                class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-xl border text-center transition-colors {{ $isCartOpen ? 'bg-[#EAF7EE] border-[#CDEED5]' : 'bg-[#FEF2F2] border-[#FECACA]' }}">
                <div
                    class="flex items-center justify-center gap-1 text-[9px] sm:text-xs font-bold {{ $isCartOpen ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full {{ $isCartOpen ? 'bg-[#1E8E3E] animate-pulse' : 'bg-[#DC2626]' }}"></span>
                    <span>{{ $isCartOpen ? seller_trans('cart_is_open') : seller_trans('cart_is_closed') }}</span>
                </div>
                <div
                    class="text-[8px] sm:text-[10px] font-semibold leading-none mt-0.5 {{ $isCartOpen ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }}">
                    @if($isCartOpen)
                        {{ seller_trans('opened_at') }}:
                        {{ $currentBusinessDay?->opened_at ? bn_time($currentBusinessDay->opened_at, $locale) : bn_time(now(), $locale) }}
                    @else
                        {{ seller_trans('closed_at') }}:
                        {{ $currentBusinessDay?->closed_at ? bn_time($currentBusinessDay->closed_at, $locale) : '—' }}
                    @endif
                </div>
            </div>

            <!-- Close Cart Outline Button -->
            @if($isCartOpen)
                <button type="button" @click="vibrate(40)" wire:click="openCloseModal"
                    class="px-2 py-1.5 sm:px-3 sm:py-2 rounded-xl border border-[#F2C49B] bg-[#FDF8F3] hover:bg-[#FCEFE3] text-[#D96B27] font-bold text-[11px] sm:text-xs flex items-center gap-1 transition-colors cursor-pointer touch-press shadow-2xs">
                    <span>🔒</span>
                    <span class="inline">{{ seller_trans('close_cart') }}</span>
                </button>
            @else
                <button type="button" @click="vibrate(40)" wire:click="openCart"
                    class="px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-[#1E8E3E] hover:bg-[#167030] text-white font-bold text-[11px] sm:text-xs flex items-center gap-1 transition-colors cursor-pointer touch-press shadow-2xs">
                    <span>🟢</span>
                    <span>{{ seller_trans('turn_on_cart') }}</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Live Toast Feedback Notification -->
    @if($feedbackMessage)
        <div
            class="py-2 px-3.5 bg-[#FFF7ED] border border-[#FED7AA] rounded-xl text-xs text-[#F26522] font-bold flex items-center justify-between shadow-2xs animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#F26522] animate-ping shrink-0"></span>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button type="button" wire:click="$set('feedbackMessage', null)"
                class="text-[#F26522] hover:text-[#C2410C] font-black cursor-pointer ml-2">✕</button>
        </div>
    @endif

<<<<<<< HEAD

=======
>>>>>>> jyoti2nd
    <!-- 4. Category Filter Horizontal Scroll Pills (Exact Mockup Match) -->
    @if($categories->count() > 1)
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 scrollbar-none">
            <!-- All Category Pill (Active Orange) -->
            <button type="button" @click="vibrate(15)" wire:click="selectCategory(null)"
                class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all touch-press cursor-pointer flex items-center gap-1.5 {{ is_null($selectedCategoryId) ? 'bg-[#F26522] text-white shadow-xs font-extrabold' : 'bg-white text-[#2B1E16] hover:bg-[#F8F3EA] border border-[#EFE7DE]' }}">
                <span>⊞</span>
                <span>{{ seller_trans('all_items') }}</span>
            </button>
            @foreach($categories as $category)
                <button type="button" @click="vibrate(15)" wire:click="selectCategory({{ $category->id }})"
                    class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all touch-press cursor-pointer flex items-center gap-1.5 {{ $selectedCategoryId === $category->id ? 'bg-[#F26522] text-white shadow-xs font-extrabold' : 'bg-white text-[#2B1E16] hover:bg-[#F8F3EA] border border-[#EFE7DE]' }}">
                    <span>{{ $category->icon }}</span>
                    <span>{{ $category->displayName($locale) }}</span>
                </button>
            @endforeach
        </div>
    @endif

    <!-- 5. FOOD ITEMS LIST (1 Full Row Per Item - Exact Mockup Match) -->
    <div class="space-y-3">
        @forelse($products as $product)
            @php
                $todayStats = $productTodaySales->get($product->id, ['count' => 0, 'revenue' => 0, 'cash_count' => 0, 'bkash_count' => 0, 'nagad_count' => 0]);
                $soldCount = $todayStats['count'];
                $isHighlighted = ($lastSoldProductId === $product->id);
                $foodDisplayName = $product->displayName($locale);
            @endphp
            <div x-data="{ qty: 0, method: 'cash' }"
                class="bg-white rounded-3xl border {{ $isHighlighted ? 'border-[#F26522] ring-2 ring-[#F26522]/30' : 'border-[#EFE7DE] hover:border-[#F26522]/40' }} p-3.5 sm:p-4 flex flex-col justify-between gap-2.5 shadow-2xs transition-all">

                <!-- Top Row: Food Image & Details on Left, SELL Button on Right -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                        <!-- Food Emoji/Image Box -->
                        <div
                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#F8F3EA] border border-[#EFE7DE] flex items-center justify-center text-2xl sm:text-3xl shrink-0 select-none shadow-2xs overflow-hidden">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $foodDisplayName }}" class="w-full h-full object-cover">
                            @else
                                {{ $product->image_emoji ?? '🍔' }}
                            @endif
                        </div>

                        <!-- Name, Price & Sold Badge -->
                        <div class="flex-1 min-w-0">
                            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] leading-tight truncate">
                                {{ $foodDisplayName }}
                            </h3>
                            <div class="text-base sm:text-xl font-black text-[#F26522] mt-0.5">
                                {{ bn_curr($product->price, $locale, 0) }}
                            </div>
                            <div
                                class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-[10px] sm:text-xs text-[#8D7B70] font-medium mt-0.5">
                                <span>{{ seller_trans('sold') }}: <strong
                                        class="text-[#2B1E16] font-extrabold">{{ bn_num($soldCount, $locale) }}</strong></span>
                                <span
                                    class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] bg-[#F8F3EA] px-1.5 sm:px-2 py-0.5 rounded-lg border border-[#EFE7DE]">
                                    <span class="inline-flex items-center gap-0.5 text-[#1E8E3E] font-bold"
                                        title="{{ seller_trans('cash') }}">
                                        <x-icon-cash
                                            class="w-2.5 h-1.5 sm:w-3 sm:h-2 shrink-0" /><span>{{ bn_num($todayStats['cash_count'], $locale) }}</span>
                                    </span>
                                    <span class="text-[#8D7B70]/40">•</span>
                                    <span class="inline-flex items-center gap-0.5 text-[#BE185D] font-bold"
                                        title="{{ seller_trans('bkash') }}">
                                        <x-icon-bkash
                                            class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 shrink-0" /><span>{{ bn_num($todayStats['bkash_count'], $locale) }}</span>
                                    </span>
                                    <span class="text-[#8D7B70]/40">•</span>
                                    <span class="inline-flex items-center gap-0.5 text-[#C2410C] font-bold"
                                        title="{{ seller_trans('nagad') }}">
                                        <x-icon-nagad
                                            class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 shrink-0" /><span>{{ bn_num($todayStats['nagad_count'], $locale) }}</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Instant SELL Button (Top Right) -->
                    <button type="button" @click="
                                vibrate(50);
                                let sellQty = qty > 0 ? qty : 1;
                                let sellMethod = method;
                                qty = 0;
                                method = 'cash';
                                $wire.recordSale({{ $product->id }}, sellQty, sellMethod);
                            "
                        class="py-2 px-5 sm:py-2.5 sm:px-6 rounded-xl bg-[#F26522] hover:bg-[#E05310] text-white font-black text-xs sm:text-sm flex items-center justify-center gap-1 shadow-2xs touch-press cursor-pointer shrink-0 transition-colors">
                        <span>SELL</span>
                        <span x-show="qty > 0" class="bg-black/20 px-1.5 py-0.5 rounded text-[10px] font-black"
                            x-text="'(' + qty + ')'"></span>
                    </button>
                </div>

                <!-- Bottom Row: [bKash] & [Nagad] and [- 0 +] Stepper (Aligned to Bottom Right) -->
                <div class="flex items-center justify-end gap-1.5 sm:gap-2 pt-1">
                    <!-- bKash Selector Button -->
                    <button type="button" @click="vibrate(25); method = (method === 'bkash' ? 'cash' : 'bkash')"
                        title="{{ seller_trans('bkash') }}" :class="method === 'bkash'
                                ? 'bg-[#E2136E] text-white border-[#BE185D] ring-2 ring-[#BE185D]/40 shadow-xs'
                                : 'bg-white hover:bg-[#FDF2F8] text-[#BE185D] border-[#FBCFE8]'"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl border font-bold text-[10px] sm:text-xs flex items-center gap-1 cursor-pointer touch-press transition-all select-none">
                        <x-icon-bkash class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0" />
                        <span>{{ seller_trans('bkash') }}</span>
                    </button>

                    <!-- Nagad Selector Button -->
                    <button type="button" @click="vibrate(25); method = (method === 'nagad' ? 'cash' : 'nagad')"
                        title="{{ seller_trans('nagad') }}" :class="method === 'nagad'
                                ? 'bg-[#EA580C] text-white border-[#C2410C] ring-2 ring-[#EA580C]/40 shadow-xs'
                                : 'bg-white hover:bg-[#FFF7ED] text-[#C2410C] border-[#FED7AA]'"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl border font-bold text-[10px] sm:text-xs flex items-center gap-1 cursor-pointer touch-press transition-all select-none">
                        <x-icon-nagad class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0" />
                        <span>{{ seller_trans('nagad') }}</span>
                    </button>

                    <!-- Stepper (- and + icons) -->
                    <div class="flex items-center gap-1 shrink-0">
                        <!-- Minus Button -->
                        <button type="button" @click="
                                    vibrate(30);
                                    if (qty > 0) {
                                        qty--;
                                    } else {
                                        $wire.recordCorrection({{ $product->id }});
                                    }
                                " title="−"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white hover:bg-[#FFF7ED] text-[#F26522] border border-[#FED7AA] font-black text-sm sm:text-base flex items-center justify-center touch-press cursor-pointer transition-colors shadow-2xs leading-none select-none">
                            −
                        </button>

                        <!-- Pending Quantity Display Counter -->
                        <span
                            class="min-w-[14px] sm:min-w-[18px] text-center font-black text-xs sm:text-sm text-[#2B1E16] select-none"
                            x-text="qty > 0 ? qty : 0">
                            0
                        </span>

                        <!-- Plus Button -->
                        <button type="button" @click="vibrate(20); qty++" title="+"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white hover:bg-[#FFF7ED] text-[#F26522] border border-[#FED7AA] font-black text-sm sm:text-base flex items-center justify-center touch-press cursor-pointer transition-colors shadow-2xs leading-none select-none">
                            +
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-[#EFE7DE] p-8 text-center shadow-2xs">
                <span class="text-3xl">🍔</span>
                <h4 class="font-bold text-sm text-[#2B1E16] mt-2">{{ seller_trans('no_food_items') }}</h4>
                <p class="text-xs text-[#8D7B70] mt-1">{{ seller_trans('no_food_items_hint') }}</p>
            </div>
        @endforelse
    </div>

    <!-- 6. Bottom Summary Bar (Exact Mockup Match) -->
    <div
        class="bg-white rounded-2xl border border-[#EFE7DE] p-2.5 sm:p-3.5 flex items-center justify-between gap-2 sm:gap-3 shadow-2xs">
        <div class="flex items-center gap-3 sm:gap-6">
            <div>
                <span
                    class="text-[9px] sm:text-xs font-semibold text-[#8D7B70] uppercase block">{{ seller_trans('total_sales') }}</span>
                <span
                    class="text-xs sm:text-base md:text-lg font-black text-[#2B1E16]">{{ bn_curr($todaySalesTotal, $locale, 0) }}</span>
            </div>
            <div class="w-px h-5 sm:h-6 bg-[#EFE7DE]"></div>
            <div>
                <span
                    class="text-[9px] sm:text-xs font-semibold text-[#8D7B70] uppercase block">{{ seller_trans('total_items_sold') }}</span>
                <span
                    class="text-xs sm:text-base md:text-lg font-black text-[#2B1E16]">{{ bn_num($todayItemsTotal, $locale) }}
                    {{ seller_trans('items_unit') }}</span>
            </div>
        </div>

        <a href="{{ route('seller.today-sales') }}"
            class="px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-[#F26522] hover:bg-[#E05310] text-white font-bold text-[11px] sm:text-sm flex items-center gap-1 sm:gap-1.5 shadow-xs touch-press cursor-pointer shrink-0">
            <span>📊</span>
            <span>{{ seller_trans('view_today_sales') }}</span>
        </a>
    </div>

    <!-- Seller Close Cart Modal -->
    @if($showCloseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">

                @if(!$isCartClosedSubmitted)
                    <!-- Initial Form State: Session Summary & Confirmation -->
                    <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🔴</span>
                            <h3 class="font-black text-base sm:text-lg text-[#2B1E16]">{{ seller_trans('close_cart') }}</h3>
                        </div>
                        <button type="button" wire:click="dismissCloseModal"
                            class="text-[#8D7B70] hover:text-[#2B1E16] font-black cursor-pointer text-base">✕</button>
                    </div>

                    <!-- Closing Summary: Total Sales & Total Items Sold in current session -->
                    <div class="grid grid-cols-2 gap-2.5 bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-3.5">
                        <div>
                            <span
                                class="text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">{{ seller_trans('total_sales') }}</span>
                            <span
                                class="text-lg sm:text-xl font-black text-[#1E8E3E] mt-0.5 block">{{ bn_curr($todaySalesTotal, $locale, 0) }}</span>
                        </div>
                        <div>
                            <span
                                class="text-[11px] font-bold text-[#8D7B70] uppercase tracking-wider block">{{ seller_trans('total_items_sold') }}</span>
                            <span
                                class="text-lg sm:text-xl font-black text-[#F26522] mt-0.5 block">{{ bn_num($todayItemsTotal, $locale) }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-[#8D7B70] text-center py-1 font-medium">
                        {{ $locale === 'bn' ? 'বর্তমান শিফট সমাপ্ত করতে কার্ট বন্ধ নিশ্চিত করুন।' : 'Confirm closing this cart session.' }}
                    </p>

                    <!-- Action Buttons: Cancel / Close Cart -->
                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="dismissCloseModal"
                            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold text-[#554338] hover:text-[#2B1E16] bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer transition-colors">
                            {{ seller_trans('cancel') }}
                        </button>
                        <button type="button" wire:click="closeCart" style="background-color: #DC2626; color: #ffffff;"
                            class="px-6 py-2.5 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#DC2626] bg-linear-to-r from-rose-600 to-red-600 hover:bg-[#B91C1C] hover:from-rose-500 hover:to-red-500 shadow-xs touch-press cursor-pointer flex items-center justify-center gap-2">
                            <span>🔒</span>
                            <span>{{ seller_trans('close') }}</span>
                        </button>
                    </div>

                @else
                    <!-- Success State: Cart Closed ✓ & Summary -->
                    <div class="text-center py-2 space-y-4">
                        <div
                            class="w-14 h-14 rounded-full bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5] flex items-center justify-center text-3xl mx-auto shadow-xs">
                            ✓
                        </div>

                        <div>
                            <h3 class="text-lg sm:text-xl font-black text-[#1E8E3E]">
                                {{ seller_trans('cart_closed_success') }}
                            </h3>
                        </div>

                        <!-- Session Final Summary Card -->
                        <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-4 text-left space-y-2.5">
                            <div class="flex items-center justify-between text-xs sm:text-sm text-[#554338] font-medium">
                                <span>{{ seller_trans('total_sales') }}:</span>
                                <span class="font-bold text-[#2B1E16]">{{ bn_curr($closedSalesTotal, $locale, 0) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs sm:text-sm text-[#554338] font-medium">
                                <span>{{ seller_trans('total_items_sold') }}:</span>
                                <span class="font-bold text-[#F26522]">{{ bn_num($closedItemsTotal, $locale) }}</span>
                            </div>
                        </div>

                        <!-- Done Action Button -->
                        <button type="button" wire:click="dismissCloseModal"
                            class="w-full py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#1E8E3E] bg-linear-to-r from-[#1E8E3E] to-teal-600 hover:from-emerald-600 hover:to-teal-700 shadow-xs touch-press cursor-pointer">
                            {{ seller_trans('done') }}
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>