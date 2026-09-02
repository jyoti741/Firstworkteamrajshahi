<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <title>{{ $title ?? seller_trans('quick_sell') }} - {{ \App\Models\CartSetting::cartName() }}</title>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col font-sans antialiased pb-20 md:pb-6"
      x-data="{ 
          mobileMode: localStorage.getItem('cartflow_seller_mobile_mode') === 'true',
          toggleMobileMode() {
              this.mobileMode = !this.mobileMode;
              localStorage.setItem('cartflow_seller_mobile_mode', this.mobileMode);
          }
      }">

    <!-- Top Seller Header -->
    <header class="sticky top-0 z-40 bg-zinc-900/90 backdrop-blur-md border-b border-zinc-800 px-3 sm:px-4 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2">
            <!-- Left: Cart Brand & Live Status -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-lg sm:text-xl shadow-lg shadow-orange-500/20 shrink-0">
                    🍔
                </div>
                <div>
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <h1 class="font-bold text-sm sm:text-base md:text-lg tracking-tight text-white truncate max-w-[140px] sm:max-w-none">
                            {{ \App\Models\CartSetting::cartName() }}
                        </h1>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                            {{ seller_trans('live_pos') }}
                        </span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-zinc-400 flex items-center gap-1.5">
                        <span>{{ seller_trans('staff') }}: <strong class="text-zinc-200">{{ auth()->user()->name }}</strong></span>
                        <span class="text-zinc-600">•</span>
                        <span>{{ bn_date(now(), app()->getLocale(), 'D, d M Y') }}</span>
                    </p>
                </div>
            </div>

            <!-- Right: Language Switcher, Cart Shift Status, Navigation & Actions -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <!-- Shift Open/Closed Indicator -->
                @php
                    $headerDay = \App\Models\BusinessDay::with(['openedBy', 'closedBy'])->whereDate('date', \Carbon\Carbon::today())->latest('id')->first();
                @endphp
                @if($headerDay && $headerDay->isOpen())
                    <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-400 font-bold" title="{{ seller_trans('opened_at') }} {{ bn_time($headerDay->opened_at, app()->getLocale()) }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ seller_trans('cart_open') }}</span>
                        <span class="text-[11px] text-emerald-300 font-mono">{{ bn_time($headerDay->opened_at, app()->getLocale()) }}</span>
                    </div>
                @elseif($headerDay && $headerDay->isClosed())
                    <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-400 font-bold" title="{{ seller_trans('closed_at') }} {{ bn_time($headerDay->closed_at, app()->getLocale()) }}">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span>{{ seller_trans('cart_closed') }}</span>
                        <span class="text-[11px] text-rose-300 font-mono">{{ bn_time($headerDay->closed_at, app()->getLocale()) }}</span>
                    </div>
                @endif

                <!-- Language Switcher (বাংলা | English) Top-Right -->
                <livewire:seller.language-switcher />

                <!-- Desktop Mobile View Toggle Button -->
                <button type="button" 
                        @click="toggleMobileMode()" 
                        class="hidden md:flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer border"
                        :class="mobileMode ? 'bg-amber-500 text-zinc-950 border-amber-400 shadow-md font-black' : 'bg-zinc-800/80 text-zinc-300 border-zinc-700 hover:text-white'">
                    <span>📱</span>
                    <span x-text="mobileMode ? '{{ seller_trans('exit_mobile') }}' : '{{ seller_trans('mobile_view') }}'"></span>
                </button>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 bg-zinc-800/80 p-1 rounded-xl border border-zinc-700/50">
                    <a href="{{ route('seller.quick-sell') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('seller.quick-sell') ? 'bg-amber-500 text-zinc-950 shadow' : 'text-zinc-300 hover:text-white hover:bg-zinc-700/50' }}">
                        <span>🛒</span> {{ seller_trans('quick_sell') }}
                    </a>
                    <a href="{{ route('seller.today-sales') }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('seller.today-sales') ? 'bg-amber-500 text-zinc-950 shadow' : 'text-zinc-300 hover:text-white hover:bg-zinc-700/50' }}">
                        <span>📋</span> {{ seller_trans('today_sales') }}
                    </a>
                </nav>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            title="{{ seller_trans('logout') }}"
                            class="px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-bold text-rose-300 hover:text-white bg-rose-950/40 hover:bg-rose-900/60 border border-rose-800/50 transition-all flex items-center gap-1.5 cursor-pointer touch-press shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ seller_trans('logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Alert / Toast Notification Area -->
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-3 w-full">
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
        <div class="max-w-7xl mx-auto px-4 mt-3 w-full">
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

    <!-- SINGLE Main Content Area -->
    <main class="flex-1 w-full mx-auto p-3 sm:p-4 md:p-6" :class="mobileMode ? 'max-w-md border-x border-zinc-800/80 shadow-2xl bg-zinc-950 pb-20' : 'max-w-7xl'">
        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation Bar (Sticky for actual mobile & preview) -->
    <nav :class="mobileMode ? '!flex' : 'md:hidden flex'" 
         class="fixed bottom-0 left-0 right-0 z-50 bg-zinc-900/95 backdrop-blur-lg border-t border-zinc-800 px-3 py-2 items-center justify-around shadow-2xl safe-bottom">
        <a href="{{ route('seller.quick-sell') }}" 
           class="flex-1 flex flex-col items-center py-1.5 rounded-xl transition-all {{ request()->routeIs('seller.quick-sell') ? 'text-amber-400 bg-amber-500/10 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <span class="text-xl">🛒</span>
            <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('quick_sell') }}</span>
        </a>

        <a href="{{ route('seller.today-sales') }}" 
           class="flex-1 flex flex-col items-center py-1.5 rounded-xl transition-all {{ request()->routeIs('seller.today-sales') ? 'text-amber-400 bg-amber-500/10 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <span class="text-xl">📋</span>
            <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('today_sales') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="flex-1 flex flex-col items-center">
            @csrf
            <button type="submit" class="w-full flex flex-col items-center py-1.5 rounded-xl text-zinc-400 hover:text-rose-400 cursor-pointer">
                <span class="text-xl">🚪</span>
                <span class="text-[11px] mt-0.5 font-bold">{{ seller_trans('logout') }}</span>
            </button>
        </form>
    </nav>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
