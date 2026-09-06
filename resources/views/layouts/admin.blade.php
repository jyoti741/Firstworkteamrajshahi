<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <title>{{ $title ?? 'Admin Panel' }} - {{ \App\Models\CartSetting::cartName() }}</title>
</head>

<body class="min-h-screen bg-[#FBF7F0] text-[#2B1E16] font-sans antialiased pb-20 lg:pb-0" x-data="{ 
          mobileMenuOpen: false,
          mobileMode: localStorage.getItem('cartflow_mobile_mode') === 'true',
          theme: localStorage.getItem('cartflow_admin_theme') || 'bright',
          init() {
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('bright');
              } else {
                  document.documentElement.classList.add('bright');
                  document.documentElement.classList.remove('dark');
              }
          },
          toggleTheme() {
              this.theme = this.theme === 'bright' ? 'dark' : 'bright';
              localStorage.setItem('cartflow_admin_theme', this.theme);
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('bright');
              } else {
                  document.documentElement.classList.add('bright');
                  document.documentElement.classList.remove('dark');
              }
              window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: this.theme } }));
          },
          toggleMobileMode() {
              this.mobileMode = !this.mobileMode;
              localStorage.setItem('cartflow_mobile_mode', this.mobileMode);
          }
      }">

    <div class="flex min-h-screen">
        <!-- Desktop Sidebar (Hidden if on mobile OR if Desktop Mobile Mode is toggled ON) -->
        <aside :class="mobileMode ? '!hidden' : 'hidden lg:flex'"
            class="lg:flex-col w-64 bg-white border-r border-[#EFE7DE] shrink-0 sticky top-0 h-screen transition-all shadow-2xs">
            <!-- Brand Header -->
            <div class="h-16 px-5 flex items-center gap-3 border-b border-[#EFE7DE] shrink-0">
                <div class="text-2xl shrink-0 leading-none">
                    🍔
                </div>
                <div class="overflow-hidden">
                    <div class="flex items-center leading-none">
                        <span class="font-extrabold text-base text-[#2B1E16] tracking-tight">Cart</span>
                        <span class="font-extrabold text-base text-[#F26522] tracking-tight">Flow</span>
                    </div>
                    <span class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider mt-0.5 block">Admin
                        Control</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <div class="px-3 pb-2 text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                    Core Business
                </div>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">🏠</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.sales') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.sales') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">🛒</span>
                    <span>Sales Management</span>
                </a>

                <a href="{{ route('admin.expenses') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.expenses') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">💸</span>
                    <span>Expenses</span>
                </a>

                <a href="{{ route('admin.assets-liabilities') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.assets-liabilities') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">⚖️</span>
                    <span>Assets & Liabilities</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                    Catalog & Stock
                </div>

                <a href="{{ route('admin.products') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.products') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">🍔</span>
                    <span>Products & Prices</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                    Analysis & Staff
                </div>

                <a href="{{ route('admin.reports') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.reports') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">📊</span>
                    <span>Reports & P&L</span>
                </a>

                <!-- Dynamic Sellers ▾ Dropdown -->
                <div x-data="{ sellersOpen: {{ request()->routeIs('admin.sellers*') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button type="button" @click="sellersOpen = !sellersOpen"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer {{ request()->routeIs('admin.sellers*') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-base">👥</span>
                            <span>Sellers</span>
                        </div>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200"
                            :class="sellersOpen ? 'rotate-180 text-[#F26522]' : 'text-[#8D7B70]'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="sellersOpen" x-cloak class="pl-6 pr-1 space-y-1 pt-1">
                        <!-- All Sellers -->
                        <a href="{{ route('admin.sellers.overview') }}"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('admin.sellers.overview') && !request()->route('user') ? 'text-[#F26522] font-black bg-[#FFF0E6] border border-[#FED7AA]' : 'text-[#8D7B70] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                            <span>🌐</span> All Sellers
                        </a>

                        @php
                            $sidebarSellers = \App\Models\User::where('role', 'seller')->orWhere('role', 'admin')->orderBy('name')->get();
                            $currentRouteUser = request()->route('user');
                            $currentRouteUserId = is_object($currentRouteUser) ? $currentRouteUser->id : ($currentRouteUser ? (int) $currentRouteUser : null);
                        @endphp
                        @foreach($sidebarSellers as $navSeller)
                            <a href="{{ route('admin.sellers.overview', $navSeller->id) }}"
                                class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('admin.sellers.overview') && $currentRouteUserId === $navSeller->id ? 'text-[#F26522] font-black bg-[#FFF0E6] border border-[#FED7AA]' : 'text-[#8D7B70] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                                <span class="truncate">{{ $navSeller->name }}</span>
                                <span
                                    class="text-[10px] text-[#8D7B70]">{{ $navSeller->role === 'admin' ? '👑' : '👤' }}</span>
                            </a>
                        @endforeach

                        <!-- Manage Staff Accounts Link inside dropdown -->
                        <a href="{{ route('admin.sellers') }}"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-semibold text-[#8D7B70] hover:text-[#F26522] transition-all border-t border-[#EFE7DE] mt-1 pt-1.5">
                            <span>⚙️</span> Manage Accounts
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.sellers') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.sellers') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">👤</span>
                    <span>Staff Management</span>
                </a>

                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.settings') ? 'bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] font-black' : 'text-[#554338] hover:text-[#2B1E16] hover:bg-[#F8F3EA]' }}">
                    <span class="text-base">⚙️</span>
                    <span>Cart Settings</span>
                </a>
            </nav>

            <!-- Theme Switcher in Sidebar -->
            <div class="px-3 py-2 border-t border-[#EFE7DE] flex items-center justify-between shrink-0">
                <span class="text-[11px] font-bold text-[#8D7B70] flex items-center gap-1.5">
                    <span x-text="theme === 'dark' ? '🌙 Dark Mode' : '☀️ Bright Mode'"></span>
                </span>
                <button type="button" @click="toggleTheme()"
                    class="px-2.5 py-1 rounded-lg border text-xs font-bold transition-all cursor-pointer touch-press"
                    :class="theme === 'dark' ? 'bg-[#27272a] text-[#f4f4f5] border-[#3f3f46]' : 'bg-[#F8F3EA] text-[#2B1E16] border-[#EFE7DE]'">
                    <span x-text="theme === 'dark' ? '☀️ Bright' : '🌙 Dark'"></span>
                </button>
            </div>

            <!-- Bottom User Card -->
            <div class="p-3 border-t border-[#EFE7DE] bg-[#F8F3EA]/50 shrink-0">
                <div
                    class="flex items-center justify-between p-2 rounded-xl bg-white border border-[#EFE7DE] shadow-2xs">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div
                            class="w-8 h-8 rounded-lg bg-[#FFF0E6] text-[#F26522] font-black text-xs flex items-center justify-center border border-[#FED7AA] shrink-0">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-bold text-[#2B1E16] truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-[#F26522] font-semibold">Owner/Admin</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout"
                            class="p-1.5 rounded-lg text-[#8D7B70] hover:text-[#DC2626] hover:bg-[#FEF2F2] transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body Area -->
        <div class="flex-1 flex flex-col min-w-0"
            :class="mobileMode ? 'max-w-md mx-auto border-x border-[#EFE7DE] shadow-2xl min-h-screen bg-[#FBF7F0] pb-20' : ''">

            <!-- Top Header -->
            <header
                class="h-16 bg-white/95 backdrop-blur-md border-b border-[#EFE7DE] px-3 sm:px-4 md:px-6 flex items-center justify-between sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 mr-2">
                    <!-- Mobile Menu Toggle Button -->
                    <button type="button" @click="mobileMenuOpen = true" :class="mobileMode ? '!block' : 'lg:hidden'"
                        class="p-1.5 sm:p-2 rounded-xl text-[#2B1E16] hover:bg-[#F8F3EA] cursor-pointer shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                        <span class="text-base sm:text-lg shrink-0">👑</span>
                        <h2 class="font-extrabold text-xs sm:text-sm md:text-base text-[#2B1E16] tracking-tight truncate">
                            {{ $title ?? 'Owner Management Panel' }}
                        </h2>
                    </div>
                </div>

                <!-- Right Quick Links, Theme Switcher & Cart Status -->
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <!-- Cart Shift Status Pill -->
                    @php
                        /** @var \App\Models\BusinessDay|null $headerDay */
                        $headerDay = \App\Models\BusinessDay::with(['openedBy', 'closedBy'])->whereDate('date', \Carbon\Carbon::today())->latest('id')->first();
                    @endphp
                    @if($headerDay && $headerDay->status === 'open')
                        <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-[#EAF7EE] border border-[#CDEED5] text-xs text-[#1E8E3E] font-bold"
                            title="Opened at {{ $headerDay->opened_at?->format('h:i A') }}">
                            <span class="w-2 h-2 rounded-full bg-[#1E8E3E] animate-pulse"></span>
                            <span class="hidden sm:inline">Cart Open</span>
                            <span
                                class="text-[11px] text-[#1E8E3E] font-semibold">{{ $headerDay->opened_at?->format('h:i A') }}</span>
                        </div>
                    @elseif($headerDay && $headerDay->status === 'closed')
                        <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-xs text-[#DC2626] font-bold"
                            title="Closed at {{ $headerDay->closed_at?->format('h:i A') }}">
                            <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                            <span class="hidden sm:inline">Cart Closed</span>
                            <span
                                class="text-[11px] text-[#DC2626] font-semibold">{{ $headerDay->closed_at?->format('h:i A') }}</span>
                        </div>
                    @endif

                    <!-- Bright / Dark Theme Toggle Button -->
                    <button type="button" @click="toggleTheme()"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer border"
                        :class="theme === 'dark' ? 'bg-[#27272a] text-[#f4f4f5] border-[#3f3f46]' : 'bg-[#F8F3EA] text-[#554338] border-[#EFE7DE] hover:text-[#2B1E16]'"
                        :title="theme === 'dark' ? 'Switch to Bright Theme' : 'Switch to Dark Theme'">
                        <span x-text="theme === 'dark' ? '☀️' : '🌙'"></span>
                        <span class="hidden sm:inline" x-text="theme === 'dark' ? 'Bright' : 'Dark'"></span>
                    </button>

                    <!-- Desktop Mobile Mode Toggle Button -->
                    <button type="button" @click="toggleMobileMode()"
                        class="hidden lg:flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer border"
                        :class="mobileMode ? 'bg-[#F26522] text-white border-[#F26522] shadow-2xs font-black' : 'bg-[#F8F3EA] text-[#554338] border-[#EFE7DE] hover:text-[#2B1E16]'">
                        <span>📱</span>
                        <span x-text="mobileMode ? 'Exit Mobile' : 'Mobile View'"></span>
                    </button>

                    <a href="{{ route('seller.quick-sell') }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#F26522] hover:bg-[#E05310] text-white transition-all flex items-center gap-1.5 touch-press shadow-2xs">
                        <span>🛒</span>
                        <span class="hidden sm:inline">POS</span>
                    </a>
                </div>
            </header>

            <!-- Alerts -->
            @if(session('error'))
                <div class="px-4 md:px-6 mt-4 w-full">
                    <div
                        class="bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] px-4 py-3 rounded-2xl flex items-center justify-between text-sm shadow-2xs font-semibold">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#DC2626] shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-[#DC2626] hover:text-[#991B1B] font-bold">✕</button>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="px-4 md:px-6 mt-4 w-full">
                    <div
                        class="bg-[#EAF7EE] border border-[#CDEED5] text-[#1E8E3E] px-4 py-3 rounded-2xl flex items-center justify-between text-sm shadow-2xs font-semibold">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#1E8E3E] shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-[#1E8E3E] hover:text-[#15803D] font-bold">✕</button>
                    </div>
                </div>
            @endif

            <!-- Main Page Content Slot -->
            <main class="flex-1 p-3 sm:p-4 md:p-6 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Admin Mobile Bottom Navigation Dock -->
    <nav :class="mobileMode ? '!flex' : 'lg:hidden flex'"
        class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-[#EFE7DE] px-2 py-1.5 items-center justify-around shadow-lg safe-bottom">
        <a href="{{ route('admin.dashboard') }}"
            class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.dashboard') ? 'text-[#F26522] font-bold' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
            <span class="text-lg">🏠</span>
            <span class="text-[10px] mt-0.5">Home</span>
        </a>

        <a href="{{ route('admin.sales') }}"
            class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.sales') ? 'text-[#F26522] font-bold' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
            <span class="text-lg">🛒</span>
            <span class="text-[10px] mt-0.5">Sales</span>
        </a>

        <a href="{{ route('admin.expenses') }}"
            class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.expenses') ? 'text-[#F26522] font-bold' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
            <span class="text-lg">💸</span>
            <span class="text-[10px] mt-0.5">Expense</span>
        </a>

        <a href="{{ route('admin.reports') }}"
            class="flex-1 flex flex-col items-center py-1 rounded-xl transition-all touch-press {{ request()->routeIs('admin.reports') ? 'text-[#F26522] font-bold' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
            <span class="text-lg">📊</span>
            <span class="text-[10px] mt-0.5">Reports</span>
        </a>

        <button type="button" @click="mobileMenuOpen = true"
            class="flex-1 flex flex-col items-center py-1 rounded-xl text-[#8D7B70] hover:text-[#2B1E16] transition-all touch-press cursor-pointer">
            <span class="text-lg">⋯</span>
            <span class="text-[10px] mt-0.5">More</span>
        </button>
    </nav>

    <!-- Mobile Slideover Navigation Menu -->
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-50 flex" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs"></div>

        <!-- Panel -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4 border-r border-[#EFE7DE] shadow-2xl">

            <div class="flex items-center justify-between px-4 pb-4 border-b border-[#EFE7DE]">
                <div class="flex items-center gap-3">
                    <span class="text-2xl leading-none">🍔</span>
                    <div>
                        <div class="font-extrabold text-sm text-[#2B1E16]">{{ \App\Models\CartSetting::cartName() }}
                        </div>
                        <div class="text-[10px] text-[#F26522] uppercase font-bold">Admin Menu</div>
                    </div>
                </div>
                <button type="button" @click="mobileMenuOpen = false"
                    class="p-2 rounded-xl text-[#8D7B70] hover:text-[#2B1E16] hover:bg-[#F8F3EA] cursor-pointer">✕</button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                <div class="px-3 pb-1 text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                    Catalog & Operations
                </div>
                <a href="{{ route('admin.products') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('admin.products') ? 'bg-[#FFF0E6] text-[#F26522] font-black' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                    <span>🍔</span> Food Items
                </a>

                <a href="{{ route('admin.assets-liabilities') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('admin.assets-liabilities') ? 'bg-[#FFF0E6] text-[#F26522] font-black' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                    <span>⚖️</span> Assets & Liabilities
                </a>

                <div class="pt-3 px-3 pb-1 text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                    Staff & System
                </div>
                <!-- Mobile Drawer Dynamic Sellers ▾ Dropdown -->
                <div x-data="{ mobileSellersOpen: {{ request()->routeIs('admin.sellers*') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button type="button" @click="mobileSellersOpen = !mobileSellersOpen"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all cursor-pointer {{ request()->routeIs('admin.sellers*') ? 'bg-[#FFF0E6] text-[#F26522] font-black' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                        <div class="flex items-center gap-3">
                            <span>👥</span>
                            <span>Sellers</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200"
                            :class="mobileSellersOpen ? 'rotate-180 text-[#F26522]' : 'text-[#8D7B70]'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="mobileSellersOpen" x-cloak class="pl-6 pr-1 space-y-1 pt-1">
                        <a href="{{ route('admin.sellers.overview') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('admin.sellers.overview') && !request()->route('user') ? 'text-[#F26522] font-black bg-[#FFF0E6]' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                            <span>🌐</span> All Sellers
                        </a>

                        @foreach($sidebarSellers as $navSeller)
                            <a href="{{ route('admin.sellers.overview', $navSeller->id) }}"
                                class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('admin.sellers.overview') && $currentRouteUserId === $navSeller->id ? 'text-[#F26522] font-black bg-[#FFF0E6]' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                                <span class="truncate">{{ $navSeller->name }}</span>
                                <span
                                    class="text-[10px] text-[#8D7B70]">{{ $navSeller->role === 'admin' ? '👑' : '👤' }}</span>
                            </a>
                        @endforeach

                        <a href="{{ route('admin.sellers') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-[#8D7B70] hover:text-[#F26522] transition-all border-t border-[#EFE7DE] mt-1 pt-1.5">
                            <span>⚙️</span> Manage Accounts
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.sellers') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('admin.sellers') ? 'bg-[#FFF0E6] text-[#F26522] font-black' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                    <span>👤</span> Staff Management
                </a>

                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('admin.settings') ? 'bg-[#FFF0E6] text-[#F26522] font-black' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                    <span>⚙️</span> Settings
                </a>

                <div class="pt-3 border-t border-[#EFE7DE]">
                    <a href="{{ route('seller.quick-sell') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-[#1E8E3E] bg-[#EAF7EE] border border-[#CDEED5]">
                        <span>🛒</span> Switch to Seller POS
                    </a>
                </div>

                <div class="pt-3 px-1 flex items-center justify-between">
                    <span class="text-xs font-bold text-[#8D7B70]">Appearance Theme</span>
                    <button type="button" @click="toggleTheme()"
                        class="px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 cursor-pointer touch-press"
                        :class="theme === 'dark' ? 'bg-[#27272a] text-[#f4f4f5] border-[#3f3f46]' : 'bg-[#F8F3EA] text-[#2B1E16] border-[#EFE7DE]'">
                        <span x-text="theme === 'dark' ? '☀️ Bright Theme' : '🌙 Dark Theme'"></span>
                    </button>
                </div>
            </nav>

            <div class="p-3 border-t border-[#EFE7DE]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold text-[#DC2626] bg-[#FEF2F2] border border-[#FECACA] cursor-pointer">
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