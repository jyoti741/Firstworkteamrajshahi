<x-layouts::auth :title="__('CartFlow Login')">
    <div class="flex flex-col gap-6">
        <!-- Brand Header -->
        <div class="flex flex-col items-center text-center -mt-3">
            <h1 class="text-lg font-black tracking-tight text-white">
                {{ \App\Models\CartSetting::cartName() }}
            </h1>
            <p class="text-xs text-zinc-400 max-w-xs mt-0.5">
                Food Cart Management &amp; Fast Sales Tracking System
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if(session('error'))
            <div class="p-3 text-xs bg-rose-950/50 border border-rose-800/60 text-rose-300 rounded-xl text-center">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1.5">Email / Username</label>
                <div class="relative">
                    <input id="email" name="email" type="email" value="{{ old('email', 'admin@cartflow.test') }}"
                        required autofocus autocomplete="email" placeholder="user@example.com"
                        class="w-full px-3.5 py-2.5 bg-zinc-900 border border-zinc-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" />
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-zinc-300">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-amber-400 hover:text-amber-300">Forgot
                            password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" type="password" value="password" required
                        autocomplete="current-password" placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 bg-zinc-900 border border-zinc-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" />
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember"
                    class="rounded bg-zinc-900 border-zinc-700 text-amber-500 focus:ring-amber-500 h-4 w-4" checked>
                <label for="remember" class="text-xs text-zinc-400">Remember on this device</label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-zinc-950 font-bold text-sm shadow-lg shadow-orange-500/25 transition-all transform active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                <span>🔐</span>
                <span>Sign In to CartFlow</span>
            </button>
        </form>

        <!-- Quick One-Click Demo Logins -->
        <div class="pt-3 border-t border-zinc-800/80">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500 text-center mb-2.5">
                Quick Demo Credentials
            </p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                    onclick="document.getElementById('email').value='admin@cartflow.test'; document.getElementById('password').value='password';"
                    class="p-2.5 bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800 hover:border-amber-500/40 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-amber-400">
                        <span>👑</span> Admin / Owner
                    </div>
                    <div class="text-[10px] text-zinc-400 truncate">admin@cartflow.test</div>
                </button>

                <button type="button"
                    onclick="document.getElementById('email').value='seller@cartflow.test'; document.getElementById('password').value='password';"
                    class="p-2.5 bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800 hover:border-amber-500/40 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                        <span>🛒</span> Seller / Staff
                    </div>
                    <div class="text-[10px] text-zinc-400 truncate">seller@cartflow.test</div>
                </button>
            </div>
            <p class="text-[10px] text-zinc-500 text-center mt-2">
                Default password: <code class="text-zinc-400 font-mono">password</code>
            </p>
        </div>
    </div>
</x-layouts::auth>