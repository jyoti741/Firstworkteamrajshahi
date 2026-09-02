<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Top Bar -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-lg">
        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <span>⚙️</span> Food Cart Configuration & Day Records
        </h1>
        <p class="text-xs text-zinc-400 mt-1">Configure business profile, currency symbol, staff permission switches, and view daily close history.</p>
    </div>

    <!-- Form Section -->
    <form wire:submit="saveSettings" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Brand & Cart Profile Card -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-lg space-y-4">
                <h3 class="font-bold text-sm text-white flex items-center gap-2 border-b border-zinc-800 pb-2.5">
                    <span>🍔</span> Cart Brand & Location
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Food Cart Name</label>
                        <input type="text" wire:model="cart_name" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Tagline / Slogan</label>
                        <input type="text" wire:model="cart_tagline" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Currency Symbol</label>
                            <input type="text" wire:model="currency_symbol" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Contact Phone</label>
                            <input type="text" wire:model="phone" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Pitch / Cart Location</label>
                        <input type="text" wire:model="address" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Operational & Staff Policies -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-lg space-y-4 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-white flex items-center gap-2 border-b border-zinc-800 pb-2.5">
                        <span>🛡️</span> Staff Permissions & Receipt Footer
                    </h3>

                    <div class="space-y-4 mt-4">
                        <!-- Expense Permission Switch -->
                        <div class="p-3.5 bg-zinc-950/80 rounded-xl border border-zinc-800 flex items-start gap-3">
                            <input type="checkbox" id="allow_seller_expense" wire:model="allow_seller_expense" class="mt-0.5 rounded bg-zinc-900 border-zinc-700 text-amber-500 focus:ring-amber-500 h-4 w-4">
                            <label for="allow_seller_expense" class="text-xs cursor-pointer">
                                <span class="font-bold text-zinc-100 block">Allow Sellers to Log Quick Expenses</span>
                                <span class="text-zinc-400 text-[11px] block mt-0.5">
                                    When enabled, staff can record urgent out-of-pocket costs (ice, emergency buns, cooking gas) from the quick sell POS.
                                </span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Receipt Footer Note</label>
                            <textarea wire:model="receipt_footer" rows="3" placeholder="Thank you for visiting! Have a wonderful day!" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-800 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold rounded-xl text-xs shadow-lg shadow-amber-500/20 cursor-pointer">
                        Save Cart Configuration
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Business Days History Log -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <span>📅</span> Business Days History & Reconciliation
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Audit shift opening floats and closing cash records.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-300">
                <thead class="text-[11px] uppercase tracking-wider text-zinc-500 bg-zinc-950/80 border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3.5">Business Date</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Opening Float</th>
                        <th class="px-4 py-3.5 text-right">Total Day Sales</th>
                        <th class="px-4 py-3.5 text-right">Day Expenses</th>
                        <th class="px-4 py-3.5 text-right">Closing Cash Reported</th>
                        <th class="px-4 py-3.5">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($businessDays as $day)
                        <tr class="hover:bg-zinc-850/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-white">
                                {{ $day->date->format('d M Y (D)') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $day->status === 'open' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-zinc-800 text-zinc-400' }}">
                                    {{ $day->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold text-zinc-300">
                                {{ $currency }}{{ number_format($day->opening_cash_float, 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-black text-emerald-400">
                                {{ $currency }}{{ number_format($day->sales_sum_total_amount ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold text-rose-400">
                                {{ $currency }}{{ number_format($day->expenses_sum_amount ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-zinc-100">
                                {{ $day->closing_cash_amount ? $currency.number_format($day->closing_cash_amount, 0) : '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-zinc-500 text-[11px] max-w-xs truncate">
                                {{ $day->notes ?? 'Normal shift close' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-zinc-500">No business day logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
