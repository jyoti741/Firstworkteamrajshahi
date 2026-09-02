<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <title>{{ $title ?? 'Admin Panel' }} - {{ \App\Models\CartSetting::cartName() }}</title>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 font-sans antialiased pb-20 lg:pb-0" 
      x-data="{ 
          mobileMenuOpen: false,
          mobileMode: localStorage.getItem('cartflow_mobile_mode') === 'true',
          toggleMobileMode() {
              this.mobileMode = !this.mobileMode;
              localStorage.setItem('cartflow_mobile_mode', this.mobileMode);
          }
      }">

    <div class="flex min-h-screen">
        <!-- Desktop Sidebar (Hidden if on mobile OR if Desktop Mobile Mode is toggled ON) -->
        <aside :class="mobileMode ? '!hidden' : 'hidden lg:flex'" 
               class="lg:flex-col w-64 bg-zinc-900 border-r border-zinc-800 shrink-0 sticky top-0 h-screen transition-all">
            <!-- Brand Header -->
            <div class="h-16 px-5 flex items-center gap-3 border-b border-zinc-800 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-lg shadow-md shadow-orange-500/20">
                    🍔
                </div>
                <div class="overflow-hidden">
                    <h1 class="font-bold text-sm text-white truncate">{{ \App\Models\CartSetting::cartName() }}</h1>
                    <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider">Admin Control</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <div class="px-3 pb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                    Core Business
                </div>

                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">🏠</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.sales') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.sales') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">🛒</span>
                    <span>Sales Management</span>
                </a>

                <a href="{{ route('admin.expenses') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.expenses') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">💸</span>
                    <span>Expenses</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                    Catalog & Stock
                </div>

                <a href="{{ route('admin.products') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.products') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">🍔</span>
                    <span>Products & Prices</span>
                </a>

                <a href="{{ route('admin.inventory') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.inventory') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">📦</span>
                    <span>Inventory Logs</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                    Analysis & Staff
                </div>

                <a href="{{ route('admin.reports') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.reports') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">📊</span>
                    <span>Reports & P&L</span>
                </a>

                <!-- Dynamic Sellers ▾ Dropdown -->
                <div x-data="{ sellersOpen: {{ request()->routeIs('admin.sellers*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" 
                            @click="sellersOpen = !sellersOpen" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ request()->routeIs('admin.sellers*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-base">👥</span>
                            <span>Sellers</span>
                        </div>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="sellersOpen ? 'rotate-180 text-amber-400' : 'text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="sellersOpen" x-cloak class="pl-6 pr-1 space-y-1 pt-1">
                        <!-- All Sellers -->
                        <a href="{{ route('admin.sellers.overview') }}" 
                           class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.sellers.overview') && !request()->route('user') ? 'text-amber-400 font-bold bg-amber-500/10 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/40' }}">
                            <span>🌐</span> All Sellers
                        </a>

                        @php
                            $sidebarSellers = \App\Models\User::where('role', 'seller')->orWhere('role', 'admin')->orderBy('name')->get();
                            $currentRouteUser = request()->route('user');
                            $currentRouteUserId = is_object($currentRouteUser) ? $currentRouteUser->id : ($currentRouteUser ? (int) $currentRouteUser : null);
                        @endphp
                        @foreach($sidebarSellers as $navSeller)
                            <a href="{{ route('admin.sellers.overview', $navSeller->id) }}" 
                               class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.sellers.overview') && $currentRouteUserId === $navSeller->id ? 'text-amber-400 font-bold bg-amber-500/10 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/40' }}">
                                <span class="truncate">{{ $navSeller->name }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $navSeller->role === 'admin' ? '👑' : '👤' }}</span>
                            </a>
                        @endforeach

                        <!-- Manage Staff Accounts Link inside dropdown -->
                        <a href="{{ route('admin.sellers') }}" 
                           class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-500 hover:text-amber-300 transition-all border-t border-zinc-800/60 mt-1 pt-1.5">
                            <span>⚙️</span> Manage Accounts
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.sellers') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.sellers') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">👤</span>
                    <span>Staff Management</span>
                </a>

                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.settings') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/60' }}">
                    <span class="text-base">⚙️</span>
                    <span>Cart Settings</span>
                </a>
            </nav>

            <!-- Bottom User Card -->
            <div class="p-3 border-t border-zinc-800 bg-zinc-900/50 shrink-0">
                <div class="flex items-center justify-between p-2 rounded-xl bg-zinc-800/40">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 font-bold text-xs flex items-center justify-center border border-amber-500/30 shrink-0">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-semibold text-zinc-200 truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-amber-400 font-medium">Owner/Admin</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-400 hover:bg-rose-950/30 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body Area -->
        <div class="flex-1 flex flex-col min-w-0" :class="mobileMode ? 'max-w-md mx-auto border-x border-zinc-800/80 shadow-2xl min-h-screen bg-zinc-950 pb-20' : ''">
            
            <!-- Top Header -->
            <header class="h-16 bg-zinc-900/80 backdrop-blur-md border-b border-zinc-800 px-4 md:px-6 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Mobile Menu Toggle Button (Visible on mobile screens or if mobileMode active) -->
                    <button type="button" 
                            @click="mobileMenuOpen = true" 
                            :class="mobileMode ? '!block' : 'lg:hidden'"
                            class="p-2 rounded-xl text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="text-lg">👑</span>
                        <h2 class="font-bold text-sm md:text-base text-white truncate">
                            {{ $title ?? 'Owner Management Panel' }}
                        </h2>
                    </div>
                </div>

                <!-- Right Quick Links & Cart Status -->
                <div class="flex items-center gap-2">
                    <!-- Cart Shift Status Pill -->
                    @php
                        $headerDay = \App\Models\BusinessDay::with(['openedBy', 'closedBy'])->whereDate('date', \Carbon\Carbon::today())->latest('id')->first();
                    @endphp
                    @if($headerDay && $headerDay->isOpen())
                        <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-400 font-bold" title="Opened at {{ $headerDay->opened_at?->format('h:i A') }}">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="hidden sm:inline">Cart Open</span>
                            <span class="text-[11px] text-emerald-300 font-mono">{{ $headerDay->opened_at?->format('h:i A') }}</span>
                        </div>
                    @elseif($headerDay && $headerDay->isClosed())
                        <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-400 font-bold" title="Closed at {{ $headerDay->closed_at?->format('h:i A') }}">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            <span class="hidden sm:inline">Cart Closed</span>
                            <span class="text-[11px] text-rose-300 font-mono">{{ $headerDay->closed_at?->format('h:i A') }}</span>
                        </div>
                    @endif

                    <!-- Desktop Mobile Mode Toggle Button -->
                    <button type="button" 
                            @click="toggleMobileMode()" 
                            class="hidden lg:flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer border"
                            :class="mobileMode ? 'bg-amber-500 text-zinc-950 border-amber-400 shadow-md font-black' : 'bg-zinc-800/80 text-zinc-300 border-zinc-700 hover:text-white'">
                        <span>📱</span>
                        <span x-text="mobileMode ? 'Exit Mobile' : 'Mobile View'"></span>
                    </button>

                    <a href="{{ route('seller.quick-sell') }}" 
                       class="px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 transition-all flex items-center gap-1.5 touch-press">
                        <span>🛒</span>
                        <span class="hidden sm:inline">POS</span>
                    </a>
                </div>
            </header>

            <!-- Alerts -->
            @if(session('error'))
                <div class="px-4 md:px-6 mt-4 w-full">
                    <div class="bg-rose-900/30 border border-rose-500/40 text-rose-300 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200">✕</button>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="px-4 md:px-6 mt-4 w-full">
                    <div class="bg-emerald-900/30 border border-emerald-500/40 text-emerald-300 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">✕</button>
                    </div>
                </div>
            @endif

            <!-- SINGLE Main Page Content Slot (100% Reliable Livewire DOM) -->
            <main class="flex-1 p-3 sm:p-4 md:p-6 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Admin Mobile Bottom Navigation Dock (Sticky for fast 1-thumb touch navigation) -->
    <nav :class="mobileMode ? '!flex' : 'lg:hidden flex'" 
         class="fixed bottom-0 left-0 right-0 z-40 bg-zinc-900/95 backdrop-blur-xl border-t border-zinc-800 px-2 py-1.5 items-center justify-around shadow-2xl safe-bottom">
        <a href="{{ route('admin.dashboard') }}" 
           class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.dashboard') ? 'text-amber-400 bg-amber-500/10 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <span class="text-lg">🏠</span>
            <span class="text-[10px] mt-0.5">Home</span>
        </a>

        <a href="{{ route('admin.sales') }}" 
           class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.sales') ? 'text-amber-400 bg-amber-500/10 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <span class="text-lg">🛒</span>
            <span class="text-[10px] mt-0.5">Sales</span>
        </a>

        <a href="{{ route('admin.expenses') }}" 
           class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.expenses') ? 'text-amber-400 bg-amber-500/10 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <span class="text-lg">💸</span>
            <span class="text-[10px] mt-0.5">Expense</span>
        </a>

        <a href="{{ route('admin.reports') }}" 
           class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.reports') ? 'text-amber-400 bg-amber-500/10 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <span class="text-lg">📊</span>
            <span class="text-[10px] mt-0.5">Reports</span>
        </a>

        <button type="button" 
                @click="mobileMenuOpen = true" 
                class="flex-1 flex flex-col items-center py-1 rounded-xl text-zinc-400 hover:text-white transition-all touch-press cursor-pointer">
            <span class="text-lg">⋯</span>
            <span class="text-[10px] mt-0.5">More</span>
        </button>
    </nav>

    <!-- Mobile Slideover Navigation Menu -->
    <div x-show="mobileMenuOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex" 
         role="dialog" 
         aria-modal="true">
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false" 
             class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>

        <!-- Panel -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-zinc-900 pt-5 pb-4 border-r border-zinc-800">
            
            <div class="flex items-center justify-between px-4 pb-4 border-b border-zinc-800">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🍔</span>
                    <div>
                        <div class="font-bold text-sm text-white">{{ \App\Models\CartSetting::cartName() }}</div>
                        <div class="text-[10px] text-amber-400 uppercase font-bold">More Features</div>
                    </div>
                </div>
                <button type="button" @click="mobileMenuOpen = false" class="p-2 rounded-lg text-zinc-400 hover:text-white">✕</button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                <div class="px-3 pb-1 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                    Catalog & Operations
                </div>
                <a href="{{ route('admin.products') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.products') ? 'bg-amber-500/15 text-amber-400' : 'text-zinc-300 hover:bg-zinc-800' }}">
                    <span>🍔</span> Food Items
                </a>
                <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.inventory') ? 'bg-amber-500/15 text-amber-400' : 'text-zinc-300 hover:bg-zinc-800' }}">
                    <span>📦</span> Inventory
                </a>

                <div class="pt-3 px-3 pb-1 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                    Staff & System
                </div>
                <!-- Mobile Drawer Dynamic Sellers ▾ Dropdown -->
                <div x-data="{ mobileSellersOpen: {{ request()->routeIs('admin.sellers*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" 
                            @click="mobileSellersOpen = !mobileSellersOpen" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer {{ request()->routeIs('admin.sellers*') ? 'bg-amber-500/15 text-amber-400' : 'text-zinc-300 hover:bg-zinc-800' }}">
                        <div class="flex items-center gap-3">
                            <span>👥</span>
                            <span>Sellers</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="mobileSellersOpen ? 'rotate-180 text-amber-400' : 'text-zinc-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="mobileSellersOpen" x-cloak class="pl-6 pr-1 space-y-1 pt-1">
                        <a href="{{ route('admin.sellers.overview') }}" 
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.sellers.overview') && !request()->route('user') ? 'text-amber-400 font-bold bg-amber-500/10' : 'text-zinc-400 hover:text-zinc-200' }}">
                            <span>🌐</span> All Sellers
                        </a>

                        @foreach($sidebarSellers as $navSeller)
                            <a href="{{ route('admin.sellers.overview', $navSeller->id) }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.sellers.overview') && $currentRouteUserId === $navSeller->id ? 'text-amber-400 font-bold bg-amber-500/10' : 'text-zinc-400 hover:text-zinc-200' }}">
                                <span class="truncate">{{ $navSeller->name }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $navSeller->role === 'admin' ? '👑' : '👤' }}</span>
                            </a>
                        @endforeach

                        <a href="{{ route('admin.sellers') }}" 
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-500 hover:text-amber-300 transition-all border-t border-zinc-800/60 mt-1 pt-1.5">
                            <span>⚙️</span> Manage Accounts
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.sellers') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.sellers') ? 'bg-amber-500/15 text-amber-400' : 'text-zinc-300 hover:bg-zinc-800' }}">
                    <span>👤</span> Staff Management
                </a>

                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.settings') ? 'bg-amber-500/15 text-amber-400' : 'text-zinc-300 hover:bg-zinc-800' }}">
                    <span>⚙️</span> Settings
                </a>

                <div class="pt-3 border-t border-zinc-800">
                    <a href="{{ route('seller.quick-sell') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-emerald-300 bg-emerald-950/40 border border-emerald-800/40">
                        <span>🛒</span> Switch to Seller POS
                    </a>
                </div>
            </nav>

            <div class="p-3 border-t border-zinc-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-semibold text-rose-300 bg-rose-950/40 border border-rose-800/40">
                        <span>🚪</span> Log out
                    </button>
                </form>
            </div>
        </div>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
