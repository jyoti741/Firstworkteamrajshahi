<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>🍔</span> Food Items
            </h1>
            <p class="text-xs text-zinc-400">Manage cart menu items, prices, profit margins, and bilingual names.</p>
        </div>

        <button type="button" 
                wire:click="openAddProductModal"
                class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-zinc-950 font-black rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-lg shadow-orange-500/20 touch-press cursor-pointer">
            <span>+</span> Add Item
        </button>
    </div>

    <!-- Search & Quick Filters -->
    <div class="flex items-center gap-2">
        <div class="relative flex-1">
            <input type="text" 
                   wire:model.live.debounce.200ms="search" 
                   placeholder="🔍 Search food items (English or বাংলা)..." 
                   class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
            @if($search)
                <button type="button" wire:click="$set('search', '')" class="absolute right-3.5 top-2.5 text-zinc-400 text-xs cursor-pointer">✕</button>
            @endif
        </div>

        <select wire:model.live="categoryFilter" class="bg-zinc-900 border border-zinc-800 rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="all">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn) ({{ $cat->name_bn }}) @endif</option>
            @endforeach
        </select>
    </div>

    <!-- Food Items Cards Grid (Mobile-First) -->
    <div class="space-y-3">
        @forelse($products as $product)
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl flex items-center justify-between gap-3 hover:border-zinc-700 transition-all">
                <!-- Left: Emoji, Name & Price -->
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-2xl shrink-0">
                        {{ $product->image_emoji ?? '🍔' }}
                    </div>

                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-sm sm:text-base text-zinc-100">
                                {{ $product->name }}
                                @if($product->name_bn)
                                    <span class="text-amber-400 font-semibold text-xs ml-1 font-sans">({{ $product->name_bn }})</span>
                                @endif
                            </h3>
                            @if(!$product->is_available)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                    Unavailable
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-xs sm:text-sm mt-0.5">
                            <span class="font-black text-amber-400 text-sm sm:text-base">
                                {{ $currency }}{{ number_format($product->price, 0) }}
                            </span>
                            @if($product->cost_price > 0)
                                <span class="text-zinc-500 text-[11px]">
                                    (Cost: {{ $currency }}{{ number_format($product->cost_price, 0) }} • Profit: <strong class="text-emerald-400">+{{ $currency }}{{ number_format($product->price - $product->cost_price, 0) }}</strong>)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" 
                            wire:click="editProduct({{ $product->id }})"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-zinc-200 bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 transition-colors touch-press cursor-pointer">
                        Edit
                    </button>

                    <button type="button" 
                            wire:click="deleteProduct({{ $product->id }})"
                            wire:confirm="Delete '{{ $product->name }}' from menu?"
                            class="p-2 rounded-xl text-xs text-rose-400 hover:text-white bg-rose-950/30 hover:bg-rose-900/60 transition-colors cursor-pointer"
                            title="Delete Item">
                        🗑️
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 text-center">
                <span class="text-3xl">🍔</span>
                <h4 class="font-bold text-sm text-zinc-300 mt-2">No food items found</h4>
                <p class="text-xs text-zinc-500 mt-1">Tap "+ Add Item" above to add your first menu item.</p>
            </div>
        @endforelse
    </div>

    <!-- Prominent + Add Item Button at bottom of list -->
    <div class="pt-2">
        <button type="button" 
                wire:click="openAddProductModal"
                class="w-full py-3.5 bg-zinc-900 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-amber-500/40 text-amber-400 font-bold rounded-2xl text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer shadow-lg">
            <span>+</span> Add Food Item
        </button>
    </div>

    <!-- Simple Add / Edit Food Item Modal -->
    @if($showProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🍔</span>
                        <h3 class="font-bold text-base text-white">{{ $editingProductId ? 'Edit Food Item' : 'Add Food Item' }}</h3>
                    </div>
                    <button type="button" wire:click="$set('showProductModal', false)" class="text-zinc-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveProduct" class="space-y-4">
                    <!-- Item Names: English & Bangla + Emoji -->
                    <div class="flex gap-2">
                        <div class="w-16">
                            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Emoji</label>
                            <input type="text" 
                                   wire:model="image_emoji" 
                                   class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-2 py-3 text-center text-xl text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>

                        <div class="flex-1">
                            <label class="block text-xs font-bold text-zinc-300 mb-1.5">English Name (e.g. Burger)</label>
                            <input type="text" 
                                   wire:model="name" 
                                   placeholder="e.g. Classic Beef Burger" 
                                   class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none" 
                                   required>
                            @error('name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Bangla Name -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Bangla Name / বাংলা নাম (যেমন: বার্গার)</label>
                        <input type="text" 
                               wire:model="name_bn" 
                               placeholder="যেমন: ক্লাসিক বিফ বার্গার" 
                               class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none font-sans">
                        @error('name_bn') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Prices: Selling & Cost -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Selling Price ({{ $currency }})</label>
                            <input type="number" 
                                   step="0.01" 
                                   wire:model="price" 
                                   placeholder="150" 
                                   class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-base text-white font-black focus:ring-2 focus:ring-amber-500 focus:outline-none" 
                                   required>
                            @error('price') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Cost Price ({{ $currency }})</label>
                            <input type="number" 
                                   step="0.01" 
                                   wire:model="cost_price" 
                                   placeholder="90" 
                                   class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-base text-zinc-300 font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @error('cost_price') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Category (Optional / Defaulted) -->
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Category</label>
                        <select wire:model="category_id" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn) ({{ $cat->name_bn }}) @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Availability Toggle -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2 text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" wire:model="is_available" class="rounded bg-zinc-950 border-zinc-700 text-amber-500 focus:ring-amber-500">
                            Available for Sale in POS
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-800">
                        <button type="button" 
                                wire:click="$set('showProductModal', false)" 
                                class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-zinc-400 hover:text-white bg-zinc-800 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-zinc-950 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 shadow-lg shadow-amber-500/20 touch-press cursor-pointer">
                            {{ $editingProductId ? 'Save Changes' : 'Save Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
