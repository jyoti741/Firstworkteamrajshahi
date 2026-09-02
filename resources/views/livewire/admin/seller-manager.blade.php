<div class="space-y-5 max-w-4xl mx-auto">

    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>👥</span> Sellers
            </h1>
            <p class="text-xs text-zinc-400">Manage seller accounts, status, and login PIN/passwords.</p>
        </div>

        <button type="button" wire:click="openAddModal"
            class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-zinc-950 font-black rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-lg shadow-orange-500/20 touch-press cursor-pointer">
            <span>+</span> Add Seller
        </button>
    </div>

    <!-- Simple Seller Cards List -->
    <div class="space-y-3">
        @forelse($users as $user)
            <div
                class="bg-zinc-900 border border-zinc-800 rounded-3xl p-4 sm:p-5 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 hover:border-zinc-700 transition-all">
                <!-- Left: Avatar, Name & Status -->
                <div class="flex items-center gap-3.5">
                    <div
                        class="w-12 h-12 rounded-2xl {{ $user->is_active ? ($user->role === 'admin' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30') : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }} flex items-center justify-center text-xl shrink-0">
                        {{ $user->role === 'admin' ? '👑' : '👤' }}
                    </div>

                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-base text-zinc-100">{{ $user->name }}</h3>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->is_active ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                {{ $user->is_active ? 'Active' : 'Disabled' }}
                            </span>
                            @if($user->role === 'admin')
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Owner
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 text-xs text-zinc-400 mt-1">
                            <span>{{ $user->email }}</span>
                            <span>•</span>
                            <span>Today: <strong
                                    class="text-emerald-400 font-bold">{{ $currency }}{{ number_format($user->sales_sum_total_amount_where_date_created_at_today ?? 0, 0) }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons (Overview, Disable/Enable & Edit/Reset) -->
                <div
                    class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-zinc-800/80 justify-end shrink-0 flex-wrap">
                    <a href="{{ route('admin.sellers.overview', $user->id) }}"
                       class="px-3 py-2 rounded-xl text-xs font-bold text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 transition-colors touch-press flex items-center gap-1.5">
                        <span>📊</span> Overview
                    </a>

                    @if($user->id !== auth()->id())
                        <button type="button" wire:click="toggleActive({{ $user->id }})"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition-colors touch-press cursor-pointer {{ $user->is_active ? 'bg-rose-950/30 hover:bg-rose-900/60 text-rose-400 border border-rose-800/40' : 'bg-emerald-950/30 hover:bg-emerald-900/60 text-emerald-400 border border-emerald-800/40' }}">
                            {{ $user->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    @endif

                    <button type="button" wire:click="editSeller({{ $user->id }})"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold text-zinc-200 bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 transition-colors touch-press cursor-pointer">
                        Edit / Reset Password
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 text-center">
                <span class="text-3xl">👥</span>
                <h4 class="font-bold text-sm text-zinc-300 mt-2">No seller accounts found</h4>
                <p class="text-xs text-zinc-500 mt-1">Tap "+ Add Seller" to create staff credentials.</p>
            </div>
        @endforelse
    </div>

    <!-- Prominent + Add Seller button at bottom of list -->
    <div class="pt-2">
        <button type="button" wire:click="openAddModal"
            class="w-full py-3.5 bg-zinc-900 hover:bg-zinc-850 active:bg-zinc-800 border border-zinc-800 hover:border-amber-500/40 text-amber-400 font-bold rounded-2xl text-sm flex items-center justify-center gap-2 transition-all touch-press cursor-pointer shadow-lg">
            <span>+</span> Add Seller
        </button>
    </div>

    <!-- Add / Edit Seller Modal Form -->
    @if($showSellerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">👤</span>
                        <h3 class="font-bold text-base text-white">
                            {{ $editingUserId ? 'Edit Seller Account' : 'Add New Seller' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showSellerModal', false)"
                        class="text-zinc-400 hover:text-white">✕</button>
                </div>

                <form wire:submit="saveSeller" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Seller Name</label>
                        <input type="text" wire:model="name" placeholder="e.g. Rahim, Karim"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            required>
                        @error('name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">Login Email</label>
                        <input type="email" wire:model="email" placeholder="e.g. rahim@cartflow.test"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            required>
                        @error('email') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Role</label>
                            <select wire:model="role"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                <option value="seller">👤 Seller</option>
                                <option value="admin">👑 Admin / Owner</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Phone (Optional)</label>
                            <input type="text" wire:model="phone" placeholder="+880 1711..."
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-300 mb-1.5">
                            Password / PIN {{ $editingUserId ? '(Leave empty to keep existing)' : '' }}
                        </label>
                        <input type="password" wire:model="password" placeholder="••••••••"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-3.5 py-3 text-sm text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            @if(!$editingUserId) required @endif>
                        @error('password') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2 text-xs font-semibold text-zinc-300 cursor-pointer">
                            <input type="checkbox" wire:model="is_active"
                                class="rounded bg-zinc-950 border-zinc-700 text-amber-500 focus:ring-amber-500">
                            Seller is Active & Allowed to Sign In
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-800">
                        <button type="button" wire:click="$set('showSellerModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-zinc-400 hover:text-white bg-zinc-800 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-zinc-950 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 shadow-lg shadow-amber-500/20 touch-press cursor-pointer">
                            {{ $editingUserId ? 'Update Seller' : 'Save Seller' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>