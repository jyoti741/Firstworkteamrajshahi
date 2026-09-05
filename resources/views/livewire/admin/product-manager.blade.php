<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between gap-2">
        <div class="min-w-0 flex-1">
            <h1
                class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2 truncate">
                <span>🍔</span> Food Items
            </h1>
            <p class="text-xs text-[#8D7B70] font-medium truncate">Manage cart menu items, pictures, selling prices, and bilingual names.</p>
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

        <div class="flex items-center gap-2">
            <select wire:model.live="categoryFilter"
                class="flex-1 sm:w-auto bg-white border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] focus:outline-none focus:ring-2 focus:ring-[#F26522] shadow-2xs font-semibold cursor-pointer">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn) ({{ $cat->name_bn }}) @endif</option>
                @endforeach
            </select>

            <button type="button" wire:click="openAddCategoryModal"
                class="px-3 py-2.5 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#F26522] rounded-2xl text-xs font-bold flex items-center gap-1 shadow-2xs transition-colors cursor-pointer"
                title="Add New Category">
                <span>+</span> Category
            </button>
        </div>
    </div>

    <!-- Food Items Cards Grid (Mobile-First) -->
    <div class="space-y-2.5">
        @forelse($products as $product)
            <div
                class="bg-white border border-[#EFE7DE] rounded-3xl p-3 sm:p-4 shadow-2xs flex items-center justify-between gap-2.5 sm:gap-3 hover:border-[#F26522]/30 transition-all">
                <!-- Left: Food Picture / Emoji, Name & Selling Price -->
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#F8F3EA] border border-[#EFE7DE] flex items-center justify-center text-xl sm:text-2xl shrink-0 select-none overflow-hidden">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $product->image_emoji ?? '🍔' }}
                        @endif
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
                            @if($product->category)
                                <span class="text-[#8D7B70] text-[10px] sm:text-xs font-semibold">
                                    • {{ $product->category->icon }} {{ $product->category->name }}
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

    <!-- Simple Add / Edit Food Item Modal (Mobile-Optimized) -->
    @if($showProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4 my-auto max-h-[92vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $image_emoji ?: '🍔' }}</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingProductId ? 'Edit Food Item' : 'Add Food Item' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showProductModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold text-lg p-1 cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveProduct" class="space-y-4">
                    <!-- 1. Item Picture Section -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#554338]">Item Picture</label>

                        <!-- Action Buttons Row: [ Upload Picture ] [ ✨ Suggest Picture ] -->
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="file" wire:model="photo" accept="image/*" capture="environment" class="hidden">
                                <span class="w-full py-2.5 px-3 rounded-2xl bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] text-[#2B1E16] text-xs font-bold flex items-center justify-center gap-1.5 transition-colors touch-press shadow-2xs text-center">
                                    <span>📷</span> Upload Picture
                                </span>
                            </label>

                            <button type="button" wire:click="suggestPicture"
                                class="w-full py-2.5 px-3 rounded-2xl bg-[#FFF8F0] hover:bg-[#FFEED9] border border-[#F26522]/30 text-[#F26522] text-xs font-bold flex items-center justify-center gap-1.5 transition-colors touch-press shadow-2xs text-center cursor-pointer">
                                <span>✨</span> Suggest Picture
                            </button>
                        </div>

                        <!-- Uploading Indicator -->
                        <div wire:loading wire:target="photo" class="text-xs text-[#F26522] font-semibold flex items-center gap-1.5 pt-1">
                            <span class="animate-spin text-sm">⏳</span> Uploading picture...
                        </div>
                        @error('photo') <span class="text-[#DC2626] text-xs font-bold block">{{ $message }}</span> @enderror

                        <!-- Picture Preview Container -->
                        @if($photo || $image_path)
                            <div class="bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl p-2.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-xl border border-[#EFE7DE] overflow-hidden bg-white shrink-0">
                                        @if($photo)
                                            <img src="{{ $photo->temporaryUrl() }}" alt="Uploaded preview" class="w-full h-full object-cover">
                                        @elseif(str_starts_with($image_path, 'images/'))
                                            <img src="{{ asset($image_path) }}" alt="Suggested preview" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('storage/' . $image_path) }}" alt="Saved preview" class="w-full h-full object-cover">
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($photo)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF5EE] text-[#1E8E3E] border border-[#CDE9D5]">
                                                    📷 Uploaded
                                                </span>
                                            @elseif($is_suggested)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FFF2EA] text-[#F26522] border border-[#FED7AA] truncate max-w-[170px]" title="{{ $suggested_image_title }}">
                                                    ✨ Suggested: {{ $suggested_image_title ?? 'Food Item' }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F0EBE5] text-[#554338] border border-[#E0D7CD]">
                                                    Saved Picture
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-[#8D7B70] mt-0.5 font-medium truncate">Used in Quick Sell & cards</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" wire:click="togglePresetGallery"
                                        class="px-2 py-1 rounded-xl text-[11px] font-bold text-[#F26522] hover:bg-white transition-colors cursor-pointer"
                                        title="Browse more food pictures">
                                        Change
                                    </button>
                                    <button type="button" wire:click="removePicture"
                                        class="p-1.5 rounded-xl text-xs text-[#DC2626] hover:bg-[#FEF2F2] transition-colors cursor-pointer"
                                        title="Remove picture">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Optional Expandable Food Gallery Picker -->
                        @if($showPresetGallery)
                            <div class="p-2.5 bg-white border border-[#EFE7DE] rounded-2xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-[#2B1E16]">Tap to pick a food photo:</span>
                                    <button type="button" wire:click="togglePresetGallery" class="text-xs text-[#8D7B70] hover:text-[#2B1E16] font-bold">✕ Close</button>
                                </div>
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5 max-h-40 overflow-y-auto p-0.5">
                                    @foreach($presetImages as $key => $img)
                                        <button type="button" wire:click="selectSuggestedPicture('{{ $img['path'] }}', '{{ $img['emoji'] }}', '{{ $img['name'] }}')"
                                            class="flex flex-col items-center p-1 rounded-xl border border-[#EFE7DE] hover:border-[#F26522] hover:bg-[#FFF8F0] transition-all cursor-pointer">
                                            <div class="w-10 h-10 rounded-lg overflow-hidden border border-[#EFE7DE] bg-[#F8F3EA]">
                                                <img src="{{ asset($img['path']) }}" alt="{{ $img['name'] }}" class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-[9px] font-bold text-[#554338] truncate w-full text-center mt-1">
                                                {{ explode(' / ', $img['name'])[0] }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Item Name (English) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Item Name (English)</label>
                        <input type="text" wire:model="name" placeholder="e.g. Classic Beef Burger"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('name') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Item Name (বাংলা) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Item Name (বাংলা)</label>
                        <input type="text" wire:model="name_bn" placeholder="যেমন: ক্লাসিক বিফ বার্গার"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        @error('name_bn') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- 4. Selling Price --}}
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Selling Price</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-base font-black text-[#8D7B70] select-none">{{ $currency }}</span>
                            <input type="number" step="0.01" wire:model="price" placeholder="150"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl pl-9 pr-3.5 py-3 text-base text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                        </div>
                        @error('price') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- 5. Category [ Select Category ▼ ] [+] OR Inline Write Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-[#554338]">Category</label>
                            @if($showNewCategoryInput)
                                <button type="button" wire:click="closeInlineCategoryInput"
                                    class="text-[11px] font-bold text-[#8D7B70] hover:text-[#2B1E16] cursor-pointer">
                                    ✕ Cancel
                                </button>
                            @endif
                        </div>

                        @if($showNewCategoryInput)
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <input type="text" wire:model="newCategoryName"
                                        placeholder="Write category name (e.g. স্ট্রিট ফুড / Fast Food)..."
                                        wire:keydown.enter.prevent="saveInlineCategory"
                                        class="flex-1 bg-[#F8F3EA] border-2 border-[#F26522] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-bold focus:outline-none shadow-2xs"
                                        autofocus>

                                    <button type="button" wire:click="saveInlineCategory"
                                        class="px-4 py-2.5 rounded-2xl bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold text-xs sm:text-sm shadow-2xs touch-press cursor-pointer shrink-0">
                                        Save
                                    </button>

                                    <button type="button" wire:click="closeInlineCategoryInput"
                                        class="w-10 h-10 rounded-2xl bg-[#F8F3EA] hover:bg-[#EFE7DE] text-[#8D7B70] hover:text-[#2B1E16] text-xs font-bold flex items-center justify-center cursor-pointer shrink-0"
                                        title="Cancel">
                                        ✕
                                    </button>
                                </div>
                                @error('newCategoryName') <span class="text-[#DC2626] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <select wire:model.live="category_id"
                                    class="flex-1 bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
                                    <option value="">Select a category...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn) ({{ $cat->name_bn }}) @endif</option>
                                    @endforeach
                                </select>

                                <button type="button" wire:click="openInlineCategoryInput"
                                    class="w-11 h-11 rounded-2xl bg-[#FFF8F0] hover:bg-[#FFEED9] border border-[#F26522]/40 text-[#F26522] font-black text-xl flex items-center justify-center transition-colors touch-press cursor-pointer shadow-2xs shrink-0"
                                    title="Add New Category">
                                    +
                                </button>
                            </div>
                        @endif
                        @error('category_id') <span class="text-[#DC2626] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Availability Toggle -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2 text-xs font-bold text-[#554338] cursor-pointer">
                            <input type="checkbox" wire:model="is_available"
                                class="rounded bg-[#F8F3EA] border-[#EFE7DE] text-[#F26522] focus:ring-[#F26522]">
                            Available for Sale in POS
                        </label>
                    </div>

                    <!-- Form Buttons: Cancel & Add Item -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showProductModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            {{ $editingProductId ? 'Save Changes' : 'Add Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Add Category Modal -->
    @if($showCategoryModal)
        <div class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-sm p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $categoryIcon ?: '🍲' }}</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showCategoryModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold text-lg p-1 cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveCategory" class="space-y-3.5">
                    <!-- Category Name -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Category Name</label>
                        <input type="text" wire:model="categoryName" placeholder="e.g. Street Food / স্ট্রিট ফুড"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required autofocus>
                        @error('categoryName') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showCategoryModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 1. Category Selection Action Popup Modal (Appears ONLY when category is selected) -->
    @if($showCategoryActionModal && $actionCategoryId)
        @php
            $actionCat = $categories->firstWhere('id', $actionCategoryId);
        @endphp
        @if($actionCat)
            <div class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-xs p-5 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-[#FFF8F0] border border-[#F26522]/30 text-3xl flex items-center justify-center mx-auto shadow-2xs">
                        {{ $actionCat->icon ?: '🍲' }}
                    </div>

                    <div>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $actionCat->name }}
                        </h3>
                        @if($actionCat->name_bn)
                            <p class="text-xs text-[#F26522] font-bold mt-0.5">{{ $actionCat->name_bn }}</p>
                        @endif
                        <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F8F3EA] text-[#8D7B70] border border-[#EFE7DE]">
                            Category Selected
                        </span>
                    </div>

                    <div class="space-y-2 pt-1 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="openEditCategoryModal({{ $actionCat->id }})"
                            class="w-full py-2.5 px-4 rounded-2xl bg-[#FFF8F0] hover:bg-[#FFEED9] border border-[#F26522]/40 text-[#F26522] font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-2xs">
                            <span>✏️</span> Edit Category Name
                        </button>

                        <button type="button" wire:click="openDeleteCategoryModal({{ $actionCat->id }})"
                            class="w-full py-2.5 px-4 rounded-2xl bg-white hover:bg-red-50 border border-[#DC2626]/30 text-[#DC2626] font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-2xs">
                            <span>🗑️</span> Delete Category
                        </button>

                        <button type="button" wire:click="closeCategoryActionModal"
                            class="w-full py-2.5 px-4 rounded-2xl bg-[#F26522] hover:bg-[#E05310] text-white font-black text-xs sm:text-sm flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-2xs">
                            <span>✓</span> Continue with this Category
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- 2. Edit Category Modal -->
    @if($showEditCategoryModal)
        <div class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-sm p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">✏️</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            Edit Category
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showEditCategoryModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold text-lg p-1 cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveEditedCategory" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Category Name (English)</label>
                        <input type="text" wire:model="editCategoryName" placeholder="e.g. Fuska, Burgers"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required autofocus>
                        @error('editCategoryName') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Category Name (বাংলা - Optional)</label>
                        <input type="text" wire:model="editCategoryNameBn" placeholder="e.g. ফুচকা, বার্গার"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        @error('editCategoryNameBn') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                        <p class="text-[10px] text-[#8D7B70] mt-1 font-medium">Leave empty to automatically translate or detect Bangla.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showEditCategoryModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 3. Delete Category Modal -->
    @if($showDeleteCategoryModal)
        <div class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-sm p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3 border-b border-[#EFE7DE] pb-3 text-[#DC2626]">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            Delete Category?
                        </h3>
                        <p class="text-xs text-[#8D7B70]">This action cannot be undone</p>
                    </div>
                </div>

                <div class="text-xs text-[#554338] leading-relaxed">
                    Are you sure you want to delete <span class="font-black text-[#2B1E16]">"{{ $deletingCategoryName }}"</span>?
                    <p class="mt-2 text-[#8D7B70] text-[11px]">
                        Items belonging to this category will not be deleted; their category will be set to unassigned.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                    <button type="button" wire:click="$set('showDeleteCategoryModal', false)"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="confirmDeleteCategory"
                        class="flex-1 py-2.5 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#DC2626] hover:bg-red-700 shadow-2xs touch-press cursor-pointer">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>