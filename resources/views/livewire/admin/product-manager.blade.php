<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between gap-2">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2 truncate">
                <span>🍔</span> Food Items
            </h1>
            <p class="text-xs text-[#8D7B70] font-medium truncate">Manage cart menu items, prices, profit margins, and bilingual names.</p>
        </div>

        <button type="button" wire:click="openAddProductModal"
            class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-2xs touch-press cursor-pointer shrink-0">
            <span>+</span> Add Item
        </button>
    </div>

    <!-- Search & Quick Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <div class="relative flex-1">
            <input type="text" wire:model.live.debounce.200ms="search"
                placeholder="🔍 Search food items (English or বাংলা)..."
                class="w-full bg-white border border-[#EFE7DE] rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-[#2B1E16] placeholder-[#8D7B70] focus:outline-none focus:ring-2 focus:ring-[#F26522] shadow-2xs font-medium">
            @if($search)
                <button type="button" wire:click="$set('search', '')"
                    class="absolute right-3.5 top-2.5 text-[#8D7B70] text-xs cursor-pointer">✕</button>
            @endif
        </div>

        <select wire:model.live="categoryFilter"
            class="w-full sm:w-auto bg-white border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] focus:outline-none focus:ring-2 focus:ring-[#F26522] shadow-2xs font-semibold cursor-pointer">
            <option value="all">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn) ({{ $cat->name_bn }})
                @endif</option>
            @endforeach
        </select>
    </div>

    <!-- Food Items Cards Grid (Mobile-First) -->
    <div class="space-y-2.5">
        @forelse($products as $product)
            <div
                class="bg-white border border-[#EFE7DE] rounded-3xl p-3 sm:p-4 shadow-2xs flex items-center justify-between gap-2.5 sm:gap-3 hover:border-[#F26522]/30 transition-all">
                <!-- Left: Emoji, Name & Price -->
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                    <div
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-[#F8F3EA] border border-[#EFE7DE] flex items-center justify-center text-xl sm:text-2xl shrink-0 select-none">
                        {{ $product->image_emoji ?? '🍔' }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                            <h3 class="font-extrabold text-xs sm:text-sm text-[#2B1E16] truncate">
                                {{ $product->name }}
                                @if($product->name_bn)
                                    <span class="text-[#F26522] font-bold text-xs ml-1">({{ $product->name_bn }})</span>
                                @endif
                            </h3>
                            @if(!$product->is_available)
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] shrink-0">
                                    Unavailable
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs sm:text-sm mt-0.5">
                            <span class="font-black text-[#F26522] text-sm sm:text-base whitespace-nowrap">
                                {{ $currency }}{{ number_format($product->price, 0) }}
                            </span>
                            @if($product->cost_price > 0)
                                <span class="text-[#8D7B70] text-[10px] sm:text-[11px] font-medium whitespace-nowrap">
                                    (Cost: {{ $currency }}{{ number_format($product->cost_price, 0) }} • Profit: <strong
                                        class="text-[#1E8E3E] font-bold">+{{ $currency }}{{ number_format($product->price - $product->cost_price, 0) }}</strong>)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <button type="button" wire:click="editProduct({{ $product->id }})"
                        class="px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#2B1E16] bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] transition-colors touch-press cursor-pointer">
                        Edit
                    </button>

                    <button type="button" wire:click="deleteProduct({{ $product->id }})"
                        wire:confirm="Delete '{{ $product->name }}' from menu?"
                        class="p-1.5 sm:p-2 rounded-xl text-xs text-[#DC2626] hover:bg-[#FEF2F2] border border-transparent hover:border-[#FECACA] transition-colors cursor-pointer"
                        title="Delete Item">
                        🗑️
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white border border-[#EFE7DE] rounded-3xl p-8 text-center shadow-2xs">
                <span class="text-3xl">🍔</span>
                <h4 class="font-extrabold text-sm text-[#2B1E16] mt-2">No food items found</h4>
                <p class="text-xs text-[#8D7B70] mt-1 font-medium">Tap "+ Add Item" above to add your first menu item.</p>
            </div>
        @endforelse
    </div>

    <!-- Prominent + Add Item Button at bottom of list -->
    <div class="pt-2">
        <button type="button" wire:click="openAddProductModal"
            class="w-full py-3.5 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] hover:border-[#F26522]/40 text-[#F26522] font-extrabold rounded-2xl text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer shadow-2xs">
            <span>+</span> Add Food Item
        </button>
    </div>

    <!-- Simple Add / Edit Food Item Modal -->
    @if($showProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🍔</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingProductId ? 'Edit Food Item' : 'Add Food Item' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showProductModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveProduct" class="space-y-4">
                    <!-- Item Names: English & Bangla + Emoji -->
                    <div class="flex gap-2">
                        <div class="w-16">
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Emoji</label>
                            <input type="text" wire:model="image_emoji"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-2 py-3 text-center text-xl text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        </div>

                        <div class="flex-1">
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">English Name (e.g. Burger)</label>
                            <input type="text" wire:model="name" placeholder="e.g. Classic Beef Burger"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                            @error('name') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Bangla Name -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Bangla Name / বাংলা নাম (যেমন:
                            বার্গার)</label>
                        <input type="text" wire:model="name_bn" placeholder="যেমন: ক্লাসিক বিফ বার্গার"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        @error('name_bn') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Prices: Selling & Cost -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Selling Price
                                ({{ $currency }})</label>
                            <input type="number" step="0.01" wire:model="price" placeholder="150"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-base text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                            @error('price') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Cost Price
                                ({{ $currency }})</label>
                            <input type="number" step="0.01" wire:model="cost_price" placeholder="90"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-base text-[#554338] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                            @error('cost_price') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Category</label>
                        <select wire:model="category_id"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn)
                                ({{ $cat->name_bn }}) @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Availability Toggle -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2 text-xs font-bold text-[#554338] cursor-pointer">
                            <input type="checkbox" wire:model="is_available"
                                class="rounded bg-[#F8F3EA] border-[#EFE7DE] text-[#F26522] focus:ring-[#F26522]">
                            Available for Sale in POS
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showProductModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            {{ $editingProductId ? 'Save Changes' : 'Save Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>