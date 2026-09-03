<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <title>{{ $title ?? seller_trans('quick_sell') }} - {{ \App\Models\CartSetting::cartName() }}</title>
</head>

<body class="min-h-screen bg-[#FBF7F0] text-[#2B1E16] flex flex-col font-sans antialiased pb-20 md:pb-6" x-data="{ 
          sellerMenuOpen: false,
          mobileMode: localStorage.getItem('cartflow_seller_mobile_mode') === 'true',
          theme: localStorage.getItem('cartflow_theme') || localStorage.getItem('cartflow_admin_theme') || 'bright',
          init() {
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('bright');
              } else {
                  document.documentElement.classList.add('bright');
                  document.documentElement.classList.remove('dark');
              }
          },
          toggleTheme(targetTheme = null) {
              if (targetTheme) {
                  this.theme = targetTheme;
              } else {
                  this.theme = this.theme === 'bright' ? 'dark' : 'bright';
              }
              localStorage.setItem('cartflow_theme', this.theme);
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
              localStorage.setItem('cartflow_seller_mobile_mode', this.mobileMode);
          }
      }">

    <!-- Top Seller Header -->
    <header
        class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#EFE7DE] px-3 sm:px-4 py-2.5 shadow-2xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2">
            <!-- Left: Hamburger Menu Button + Cart Brand & Live Status -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Hamburger Menu Button -->
                <button type="button" @click="sellerMenuOpen = true"
                    class="p-2 -ml-1 rounded-xl text-[#2B1E16] hover:bg-[#F8F3EA] cursor-pointer touch-press"
                    title="{{ seller_trans('menu') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-[#FFF0E6] border border-[#FED7AA] flex items-center justify-center text-lg sm:text-xl shadow-2xs shrink-0">
                    🍔
                </div>
                <div>
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <h1
                            class="font-extrabold text-sm sm:text-base md:text-lg tracking-tight text-[#2B1E16] truncate max-w-[140px] sm:max-w-none">
                            <span>Cart</span><span class="text-[#F26522]">Flow</span>
                        </h1>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1E8E3E] mr-1.5 animate-pulse"></span>
                            {{ seller_trans('live_pos') }}
                        </span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#8D7B70] flex items-center gap-1.5 font-medium">
                        <span>{{ seller_trans('staff') }}: <strong
                                class="text-[#554338]">{{ auth()->user()->name }}</strong></span>
                        <span>•</span>
                        <span>{{ bn_date(now(), app()->getLocale(), 'D, d M Y') }}</span>
                    </p>
                </div>
            </div>

            <!-- Right: Language Switcher, Cart Shift Status, Navigation & Actions -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <!-- Shift Open/Closed Indicator -->
                @php
                    /** @var \App\Models\BusinessDay|null $headerDay */
                    $headerDay = \App\Models\BusinessDay::with(['openedBy', 'closedBy'])->whereDate('date', \Carbon\Carbon::today())->latest('id')->first();
                @endphp
                @if($headerDay && $headerDay->status === 'open')
                    <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-[#EAF7EE] border border-[#CDEED5] text-xs text-[#1E8E3E] font-bold"
                        title="{{ seller_trans('opened_at') }} {{ bn_time($headerDay->opened_at, app()->getLocale()) }}">
                        <span class="w-2 h-2 rounded-full bg-[#1E8E3E] animate-pulse"></span>
                        <span>{{ seller_trans('cart_open') }}</span>
                        <span
                            class="text-[11px] text-[#1E8E3E] font-semibold">{{ bn_time($headerDay->opened_at, app()->getLocale()) }}</span>
                    </div>
                @elseif($headerDay && $headerDay->status === 'closed')
                    <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-xs text-[#DC2626] font-bold"
                        title="{{ seller_trans('closed_at') }} {{ bn_time($headerDay->closed_at, app()->getLocale()) }}">
                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                        <span>{{ seller_trans('cart_closed') }}</span>
                        <span
                            class="text-[11px] text-[#DC2626] font-semibold">{{ bn_time($headerDay->closed_at, app()->getLocale()) }}</span>
                    </div>
                @endif

                <!-- Admin Switch Button (if user is admin) -->
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                        class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#FFF0E6] text-[#F26522] border border-[#FED7AA] hover:bg-[#FFE6D5] transition-all touch-press shadow-2xs">
                        <span>👑</span>
                        <span>Admin</span>
                    </a>
                @endif

                <!-- Desktop Mobile / Desktop View Toggle Button -->
                <button type="button" @click="toggleMobileMode()"
                    class="hidden md:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer border"
                    :class="mobileMode ? 'bg-[#F26522] text-white border-[#F26522] shadow-2xs font-black' : 'bg-[#F8F3EA] text-[#554338] border-[#EFE7DE] hover:text-[#2B1E16]'">
                    <span>📱</span>
                    <span
                        x-text="mobileMode ? '{{ seller_trans('exit_mobile') }}' : '{{ seller_trans('mobile_view') }}'"></span>
                </button>


            </div>
        </div>
    </header>

    <!-- Seller Hamburger Slideover Menu Drawer -->
    <div x-show="sellerMenuOpen" x-cloak class="fixed inset-0 z-50 flex" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="sellerMenuOpen" x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sellerMenuOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs"></div>

        <!-- Panel -->
        <div x-show="sellerMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white pt-4 pb-4 border-r border-[#EFE7DE] shadow-2xl">

            <!-- Drawer Header -->
            <div class="flex items-center justify-between px-4 pb-3 border-b border-[#EFE7DE]">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl leading-none">🍔</span>
                    <div>
                        <div class="font-extrabold text-sm text-[#2B1E16]"><span>Cart</span><span
                                class="text-[#F26522]">Flow</span></div>
                        <div class="text-[10px] text-[#8D7B70] uppercase font-bold">{{ seller_trans('menu') }}</div>
                    </div>
                </div>
                <button type="button" @click="sellerMenuOpen = false"
                    class="p-2 rounded-xl text-[#8D7B70] hover:text-[#2B1E16] hover:bg-[#F8F3EA] cursor-pointer">✕</button>
            </div>

            <div class="flex-1 px-4 py-4 space-y-4 overflow-y-auto">
                <!-- Staff Profile Mini Card -->
                <div class="p-3 rounded-2xl bg-[#F8F3EA] border border-[#EFE7DE] flex items-center gap-3 shadow-2xs">
                    <div
                        class="w-10 h-10 rounded-xl bg-[#FFF0E6] border border-[#FED7AA] flex items-center justify-center text-xl shrink-0">
                        👨‍🍳
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-[#2B1E16] truncate">{{ auth()->user()->name }}</div>
                        <span
                            class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5] mt-0.5">
                            {{ auth()->user()->role === 'admin' ? 'Owner / Admin' : 'Staff / Cashier' }}
                        </span>
                    </div>
                </div>

                <!-- Language Selection Option (বাংলা | English) -->
                <div class="space-y-2">
                    <div class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                        {{ seller_trans('language') }}
                    </div>
                    <livewire:seller.language-switcher />
                </div>

                <!-- Bright / Dark Theme Feature (Default: Bright) -->
                <div class="space-y-2">
                    <div class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider">
                        {{ seller_trans('theme') }} / Appearance
                    </div>
                    <div class="grid grid-cols-2 gap-2 bg-[#F8F3EA] p-1 rounded-2xl border border-[#EFE7DE]">
                        <!-- Bright Theme Button (Default) -->
                        <button type="button" @click="toggleTheme('bright')"
                            class="py-2 px-2.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer flex items-center justify-center gap-1.5"
                            :class="theme === 'bright' ? 'bg-[#F26522] text-white font-black shadow-2xs' : 'text-[#554338] hover:text-[#2B1E16]'">
                            <span>☀️</span>
                            <span>Bright</span>
                        </button>

                        <!-- Dark Theme Button -->
                        <button type="button" @click="toggleTheme('dark')"
                            class="py-2 px-2.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer flex items-center justify-center gap-1.5"
                            :class="theme === 'dark' ? 'bg-[#27272a] text-white font-black shadow-2xs border border-[#3f3f46]' : 'text-[#554338] hover:text-[#2B1E16]'">
                            <span>🌙</span>
                            <span>Dark</span>
                        </button>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="space-y-1 pt-1">
                    <div class="text-[10px] font-bold text-[#8D7B70] uppercase tracking-wider pb-1">
                        Navigation
                    </div>
                    <a href="{{ route('seller.quick-sell') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('seller.quick-sell') ? 'bg-[#FFF0E6] text-[#F26522] font-black border border-[#FED7AA]' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                        <span class="text-base">🛒</span>
                        <span>{{ seller_trans('quick_sell') }}</span>
                    </a>

                    <a href="{{ route('seller.today-sales') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('seller.today-sales') ? 'bg-[#FFF0E6] text-[#F26522] font-black border border-[#FED7AA]' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                        <span class="text-base">📋</span>
                        <span>{{ seller_trans('today_sales') }}</span>
                    </a>

                    <a href="{{ route('seller.expenses') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('seller.expenses') ? 'bg-[#FFF0E6] text-[#F26522] font-black border border-[#FED7AA]' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                        <span class="text-base">💸</span>
                        <span>{{ seller_trans('expenses') }}</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-[#F26522] bg-[#FFF0E6] border border-[#FED7AA] hover:bg-[#FFE6D5] transition-all">
                            <span class="text-base">👑</span>
                            <span>Admin Dashboard</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Logout Button at bottom of Drawer -->
            <div class="p-3 border-t border-[#EFE7DE]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold text-[#DC2626] bg-[#FEF2F2] border border-[#FECACA] cursor-pointer touch-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ seller_trans('logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Alert / Toast Notification Area -->
    @if(session('error'))
        <div class="max-w-2xl mx-auto px-4 mt-3 w-full">
            <div
                class="bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] px-4 py-3 rounded-2xl flex items-center justify-between text-sm shadow-2xs font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="max-w-2xl mx-auto px-4 mt-3 w-full">
            <div
                class="bg-[#EAF7EE] border border-[#CDEED5] text-[#1E8E3E] px-4 py-3 rounded-2xl flex items-center justify-between text-sm shadow-2xs font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E8E3E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()"
                    class="text-[#1E8E3E] hover:text-[#15803D] font-bold">✕</button>
            </div>
        </div>
    @endif

    <!-- Main Content Area with Dynamic Mobile / Desktop Mode wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all"
        :class="mobileMode ? 'max-w-md mx-auto border-x border-[#EFE7DE] shadow-2xl min-h-screen bg-[#FBF7F0] pb-20' : 'w-full'">
        <main class="flex-1 w-full mx-auto p-2.5 sm:p-4 transition-all" :class="mobileMode ? 'max-w-md' : 'max-w-3xl'">
            {{ $slot }}
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-[#FBF7F0]/95 backdrop-blur-lg border-t border-[#EFE7DE] px-3 py-1.5 flex items-center justify-around shadow-lg safe-bottom"
        :class="mobileMode ? 'max-w-md mx-auto border-x border-[#EFE7DE]' : ''">
        <div class="max-w-2xl mx-auto w-full flex items-center justify-around">
            <!-- Quick Sell Tab -->
            <a href="{{ route('seller.quick-sell') }}"
                class="flex-1 flex flex-col items-center py-1.5 rounded-2xl transition-all touch-press {{ request()->routeIs('seller.quick-sell') ? 'text-[#F26522] bg-[#FFF0E6] font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                <span class="text-xl">🛒</span>
                <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('quick_sell') }}</span>
            </a>

            <!-- Today's Sales Tab -->
            <a href="{{ route('seller.today-sales') }}"
                class="flex-1 flex flex-col items-center py-1.5 rounded-2xl transition-all touch-press {{ request()->routeIs('seller.today-sales') ? 'text-[#F26522] bg-[#FFF0E6] font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                <span class="text-xl">📋</span>
                <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('today_sales') }}</span>
            </a>

            <!-- Expenses Tab -->
            <a href="{{ route('seller.expenses') }}"
                class="flex-1 flex flex-col items-center py-1.5 rounded-2xl transition-all touch-press {{ request()->routeIs('seller.expenses') ? 'text-[#F26522] bg-[#FFF0E6] font-black' : 'text-[#8D7B70] hover:text-[#2B1E16]' }}">
                <span class="text-xl">💸</span>
                <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('expenses') }}</span>
            </a>

            <!-- Logout Tab -->
            <form method="POST" action="{{ route('logout') }}" class="flex-1 flex flex-col items-center">
                @csrf
                <button type="submit"
                    class="w-full flex flex-col items-center py-1.5 rounded-2xl text-[#8D7B70] hover:text-[#DC2626] hover:bg-[#FEF2F2] transition-colors cursor-pointer touch-press">
                    <span class="text-xl">🚪</span>
                    <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('logout') }}</span>
                </button>
            </form>
        </div>
    </nav>

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>