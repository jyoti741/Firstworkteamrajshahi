<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2">
                <span>👥</span> Sellers
            </h1>
            <p class="text-xs text-[#8D7B70] font-medium">Manage seller accounts, status, and login PIN/passwords.</p>
        </div>

        <button type="button" wire:click="openAddModal"
            class="px-4 py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-2xs touch-press cursor-pointer">
            <span>+</span> Add Seller
        </button>
    </div>

    <!-- Simple Seller Cards List -->
    <div class="space-y-3">
        @forelse($users as $user)
            <div
                class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 hover:border-[#F26522]/30 transition-all">
                <!-- Left: Avatar, Name & Status -->
                <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                    <div
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ $user->is_active ? ($user->role === 'admin' ? 'bg-[#FDF4EB] text-[#F26522] border border-[#FAD7C0]' : 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]') : 'bg-[#F8F3EA] text-[#8D7B70] border border-[#EFE7DE]' }} flex items-center justify-center text-lg sm:text-xl shrink-0">
                        {{ $user->role === 'admin' ? '👑' : '👤' }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] truncate">{{ $user->name }}</h3>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $user->is_active ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                                {{ $user->is_active ? 'Active' : 'Disabled' }}
                            </span>
                            @if($user->role === 'admin')
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-[#FDF4EB] text-[#F26522] border border-[#FAD7C0] shrink-0">
                                    Owner
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-[#8D7B70] mt-0.5 font-medium">
                            <span class="truncate max-w-[180px] sm:max-w-none">{{ $user->email }}</span>
                            <span>•</span>
                            <span class="whitespace-nowrap">Today: <strong
                                    class="text-[#1E8E3E] font-bold">{{ $currency }}{{ number_format($user->sales_sum_total_amount_where_date_created_at_today ?? 0, 0) }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons (Overview, Disable/Enable & Edit/Reset) -->
                <div
                    class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#EFE7DE] justify-start sm:justify-end shrink-0 flex-wrap">
                    <a href="{{ route('admin.sellers.overview', $user->id) }}"
                        class="px-3 py-1.5 sm:py-2 rounded-xl text-xs font-bold text-[#F26522] bg-[#FDF4EB] hover:bg-[#FCE6D5] border border-[#FAD7C0] transition-colors touch-press flex items-center gap-1.5">
                        <span>📊</span> Overview
                    </a>

                    @if($user->id !== auth()->id())
                        <button type="button" wire:click="toggleActive({{ $user->id }})"
                            class="px-3 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition-colors touch-press cursor-pointer {{ $user->is_active ? 'bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA]' : 'bg-[#EAF7EE] hover:bg-[#D7EFE0] text-[#1E8E3E] border border-[#CDEED5]' }}">
                            {{ $user->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    @endif

                    <button type="button" wire:click="editSeller({{ $user->id }})"
                        class="px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl text-xs font-bold text-[#2B1E16] bg-[#F8F3EA] hover:bg-[#EFE7DE] border border-[#EFE7DE] transition-colors touch-press cursor-pointer">
                        Edit / Reset Password
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white border border-[#EFE7DE] rounded-3xl p-8 text-center shadow-2xs">
                <span class="text-3xl">👥</span>
                <h4 class="font-extrabold text-sm text-[#2B1E16] mt-2">No seller accounts found</h4>
                <p class="text-xs text-[#8D7B70] mt-1 font-medium">Tap "+ Add Seller" to create staff credentials.</p>
            </div>
        @endforelse
    </div>

    <!-- Prominent + Add Seller button at bottom of list -->
    <div class="pt-2">
        <button type="button" wire:click="openAddModal"
            class="w-full py-3.5 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] hover:border-[#F26522]/40 text-[#F26522] font-extrabold rounded-2xl text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer shadow-2xs">
            <span>+</span> Add Seller
        </button>
    </div>

    <!-- Add / Edit Seller Modal Form -->
    @if($showSellerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">👤</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingUserId ? 'Edit Seller Account' : 'Add New Seller' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showSellerModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveSeller" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Seller Name</label>
                        <input type="text" wire:model="name" placeholder="e.g. Rahim, Karim"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('name') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Login Email</label>
                        <input type="email" wire:model="email" placeholder="e.g. rahim@cartflow.test"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('email') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Role</label>
                            <select wire:model="role"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none cursor-pointer">
                                <option value="seller">👤 Seller</option>
                                <option value="admin">👑 Admin / Owner</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Phone (Optional)</label>
                            <input type="text" wire:model="phone" placeholder="+880 1711..."
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">
                            Password / PIN {{ $editingUserId ? '(Leave empty to keep existing)' : '' }}
                        </label>
                        <input type="password" wire:model="password" placeholder="••••••••"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            @if(!$editingUserId) required @endif>
                        @error('password') <span class="text-[#DC2626] text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2 text-xs font-bold text-[#554338] cursor-pointer">
                            <input type="checkbox" wire:model="is_active"
                                class="rounded bg-[#F8F3EA] border-[#EFE7DE] text-[#F26522] focus:ring-[#F26522]">
                            Seller is Active & Allowed to Sign In
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showSellerModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            {{ $editingUserId ? 'Update Seller' : 'Save Seller' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>