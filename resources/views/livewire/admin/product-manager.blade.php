<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between gap-2">
        <div class="min-w-0 flex-1">
            <h1
                class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2 truncate">
                <span>🍔</span> Food Items
            </h1>
            <p class="text-xs text-[#8D7B70] font-medium truncate">Manage cart menu items, categories, selling prices,
                and bilingual names.</p>
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

        <div class="flex items-center gap-1.5 w-full sm:w-auto">
            <select wire:model.live="categoryFilter"
                class="flex-1 sm:flex-none bg-white border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] focus:outline-none focus:ring-2 focus:ring-[#F26522] shadow-2xs font-semibold cursor-pointer">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn)
                        ({{ $cat->name_bn }})
                    @endif</option>
                @endforeach
            </select>

            <!-- + button beside category filter -->
            <button type="button" wire:click="openAddCategoryModal" title="Add New Category"
                class="w-10 h-10 rounded-2xl bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#F26522] font-black text-lg flex items-center justify-center cursor-pointer touch-press shrink-0 shadow-2xs transition-colors">
                +
            </button>

            <!-- Edit and Delete when a category is selected -->
            @if($categoryFilter !== 'all')
                <button type="button" wire:click="editCategory({{ $categoryFilter }})" title="Edit Selected Category"
                    class="w-10 h-10 rounded-2xl bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] text-sm flex items-center justify-center cursor-pointer touch-press shrink-0 shadow-2xs transition-colors">
                    ✏️
                </button>
                <button type="button" wire:click="deleteCategory({{ $categoryFilter }})"
                    wire:confirm="Are you sure you want to delete this category?" title="Delete Selected Category"
                    class="w-10 h-10 rounded-2xl bg-white hover:bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] text-sm flex items-center justify-center cursor-pointer touch-press shrink-0 shadow-2xs transition-colors">
                    🗑️
                </button>
            @endif
        </div>
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
                            @if($product->category)
                                <span class="text-[#8D7B70] text-[11px] font-medium whitespace-nowrap">
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
                    <!-- Item Name (English) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Item Name (English) / নাম (ইংরেজি)</label>
                        <input type="text" wire:model.live.debounce.250ms="name" placeholder="e.g. Fuchka, Classic Beef Burger"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('name') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Item Name (বাংলা) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Item Name (বাংলা) / নাম (বাংলা)</label>
                        <input type="text" wire:model.live.debounce.250ms="name_bn" placeholder="যেমন: ফুচকা, বার্গার"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        @error('name_bn') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Suggested Emoji & Custom Selection -->
                    <div class="bg-[#F8F3EA]/80 border border-[#EFE7DE] rounded-2xl p-3 space-y-2.5 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#554338]">
                                {{ $manualEmojiSet ? 'Selected Emoji' : 'Suggested Emoji' }}
                                @if($manualEmojiSet)
                                    <span class="text-[10px] text-[#8D7B70] font-normal ml-1">(Manual selection preserved)</span>
                                @else
                                    <span class="text-[10px] text-[#1E8E3E] font-medium ml-1">● Auto-detected</span>
                                @endif
                            </label>
                            @if($manualEmojiSet)
                                <button type="button" wire:click="resetToAutoEmoji"
                                    class="text-[11px] font-bold text-[#F26522] hover:text-[#E05310] cursor-pointer">
                                    ↺ Reset to Auto
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Emoji Preview Badge -->
                            <div class="w-12 h-12 rounded-2xl bg-white border border-[#EFE7DE] flex items-center justify-center text-2xl shadow-2xs shrink-0 select-none">
                                {{ $image_emoji ?: '🍽️' }}
                            </div>

                            <!-- Small Change Button Beside Emoji -->
                            <button type="button" wire:click="toggleEmojiPicker"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold {{ $showEmojiPicker ? 'bg-[#2B1E16] text-white' : 'bg-white hover:bg-[#F8F3EA] text-[#2B1E16] border border-[#EFE7DE]' }} shadow-2xs transition-colors cursor-pointer touch-press shrink-0">
                                {{ $showEmojiPicker ? 'Done' : 'Change' }}
                            </button>

                            <span class="text-[11px] text-[#8D7B70] font-medium truncate">
                                @if($manualEmojiSet)
                                    Preserved your custom choice
                                @elseif($name || $name_bn)
                                    Auto-suggested from item name
                                @else
                                    Type item name to auto-detect emoji
                                @endif
                            </span>
                        </div>

                        <!-- Manual Emoji Selection Drawer (Opened when Change is clicked) -->
                        @if($showEmojiPicker)
                            <div class="pt-2.5 border-t border-[#EFE7DE] space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-[#554338]">Custom emoji:</span>
                                    <input type="text" wire:model.live="image_emoji"
                                        class="w-14 bg-white border border-[#EFE7DE] rounded-xl py-1 text-center text-xl focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                                    <span class="text-[10px] text-[#8D7B70]">(Type or paste any emoji)</span>
                                </div>

                                <div>
                                    <span class="text-[11px] font-bold text-[#8D7B70] block mb-1">Or pick a food emoji:</span>
                                    <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto p-1.5 bg-white border border-[#EFE7DE] rounded-xl">
                                        @foreach($this->popularFoodEmojis as $em)
                                            <button type="button" wire:click="selectEmoji('{{ $em }}')"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-lg hover:bg-[#F8F3EA] {{ $image_emoji === $em ? 'bg-[#F26522]/20 ring-2 ring-[#F26522]' : '' }} transition-colors cursor-pointer touch-press"
                                                title="Select {{ $em }}">
                                                {{ $em }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Selling Price ({{ $currency }})</label>
                        <input type="number" step="0.01" wire:model="price" placeholder="150"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-base text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('price') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#554338]">Category</label>
                        <div class="flex items-center gap-1.5">
                            <select wire:model.live="category_id"
                                class="flex-1 bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
                                @if($categories->isEmpty())
                                    <option value="">No categories yet (click + to add)</option>
                                @endif
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }} @if($cat->name_bn)
                                    ({{ $cat->name_bn }}) @endif</option>
                                @endforeach
                            </select>

                            <!-- + button beside category to open field -->
                            <button type="button" wire:click="toggleInlineCategoryAdd" title="Add New Category"
                                class="h-[42px] w-[42px] rounded-2xl {{ $showInlineCategoryInput && !$inlineCategoryEditingId ? 'bg-[#F26522] text-white' : 'bg-[#F8F3EA] hover:bg-[#EFE7DE] text-[#F26522]' }} border border-[#EFE7DE] font-black text-xl flex items-center justify-center cursor-pointer transition-colors shrink-0 shadow-2xs touch-press">
                                +
                            </button>

                            @if($category_id)
                                <!-- Edit option while category is selected -->
                                <button type="button" wire:click="startInlineCategoryEdit" title="Edit Selected Category"
                                    class="h-[42px] w-[42px] rounded-2xl {{ $inlineCategoryEditingId ? 'bg-[#2B1E16] text-white' : 'bg-[#F8F3EA] hover:bg-[#EFE7DE] text-[#2B1E16]' }} border border-[#EFE7DE] text-xs flex items-center justify-center cursor-pointer transition-colors shrink-0 shadow-2xs touch-press">
                                    ✏️
                                </button>

                                <!-- Delete option while category is selected -->
                                <button type="button" wire:click="deleteSelectedCategory"
                                    wire:confirm="Are you sure you want to delete this category?"
                                    title="Delete Selected Category"
                                    class="h-[42px] w-[42px] rounded-2xl bg-[#F8F3EA] hover:bg-[#FEF2F2] border border-[#EFE7DE] hover:border-[#FECACA] text-[#DC2626] text-xs flex items-center justify-center cursor-pointer transition-colors shrink-0 shadow-2xs touch-press">
                                    🗑️
                                </button>
                            @endif
                        </div>

                        <!-- Inline field to write category name -->
                        @if($showInlineCategoryInput)
                            <div class="p-3 bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl space-y-2 mt-2 shadow-inner">
                                <div class="flex items-center justify-between text-xs font-bold text-[#554338]">
                                    <span>{{ $inlineCategoryEditingId ? '✏️ Edit Category Name' : '+ New Category' }}</span>
                                    <button type="button" wire:click="cancelInlineCategory"
                                        class="text-[#8D7B70] hover:text-[#2B1E16] text-xs font-semibold cursor-pointer">✕
                                        Cancel</button>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" wire:model="inlineCategoryIcon" placeholder="🍔"
                                        class="w-12 bg-white border border-[#EFE7DE] rounded-xl py-2 text-center text-base focus:ring-2 focus:ring-[#F26522] focus:outline-none shrink-0"
                                        title="Category Icon / Emoji">
                                    <input type="text" wire:model="inlineCategoryName"
                                        placeholder="Category Name in English (e.g. Noodles)..."
                                        class="flex-1 min-w-0 bg-white border border-[#EFE7DE] rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" wire:model="inlineCategoryNameBn"
                                        placeholder="Bangla Name / বাংলা নাম (যেমন: নুডলস)..."
                                        class="flex-1 min-w-0 bg-white border border-[#EFE7DE] rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                        wire:keydown.enter.prevent="saveInlineCategory">
                                    <button type="button" wire:click="saveInlineCategory"
                                        class="px-3.5 py-2 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold text-xs rounded-xl cursor-pointer transition-colors shadow-2xs shrink-0">
                                        {{ $inlineCategoryEditingId ? 'Update' : 'Save' }}
                                    </button>
                                </div>
                                @error('inlineCategoryName')
                                    <span class="text-[#DC2626] text-[11px] font-bold block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
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

    <!-- Category Modal (from main filter) -->
    @if($showCategoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-sm p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📁</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showCategoryModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveCategory" class="space-y-3.5">
                    <div class="flex gap-2">
                        <div class="w-16">
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Icon</label>
                            <input type="text" wire:model="categoryIcon"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-2 py-2.5 text-center text-xl text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Category Name</label>
                            <input type="text" wire:model="categoryName" placeholder="e.g. Burgers, Drinks"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                            @error('categoryName') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Bangla Name (Optional)</label>
                        <input type="text" wire:model="categoryNameBn" placeholder="যেমন: বার্গার, পানীয়"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-2.5 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showCategoryModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            {{ $editingCategoryId ? 'Update Category' : 'Save Category' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>