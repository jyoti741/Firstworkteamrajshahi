<div class="space-y-4 max-w-5xl mx-auto">

    <!-- Top Bar -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs">
        <h1 class="text-lg sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2 truncate">
            <span>⚙️</span> Food Cart Configuration & Day Records
        </h1>
        <p class="text-xs text-[#8D7B70] mt-1 font-medium">Configure business profile, currency symbol, staff permission
            switches,
            and view daily close history.</p>
    </div>

    <!-- Form Section -->
    <form wire:submit="saveSettings" class="space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Brand & Cart Profile Card -->
            <div class="bg-white border border-[#EFE7DE] rounded-3xl p-5 shadow-2xs space-y-4">
                <h3
                    class="font-extrabold text-sm text-[#2B1E16] flex items-center gap-2 border-b border-[#EFE7DE] pb-2.5">
                    <span>🍔</span> Cart Brand & Location
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Food Cart Name</label>
                        <input type="text" wire:model="cart_name"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2.5 text-xs text-[#2B1E16] font-bold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Tagline / Slogan</label>
                        <input type="text" wire:model="cart_tagline"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2.5 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1">Currency Symbol</label>
                            <input type="text" wire:model="currency_symbol"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2.5 text-xs text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1">Contact Phone</label>
                            <input type="text" wire:model="phone"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2.5 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1">Pitch / Cart Location</label>
                        <input type="text" wire:model="address"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2.5 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Operational & Staff Policies -->
            <div
                class="bg-white border border-[#EFE7DE] rounded-3xl p-5 shadow-2xs space-y-4 flex flex-col justify-between">
                <div>
                    <h3
                        class="font-extrabold text-sm text-[#2B1E16] flex items-center gap-2 border-b border-[#EFE7DE] pb-2.5">
                        <span>🛡️</span> Staff Permissions & Receipt Footer
                    </h3>

                    <div class="space-y-4 mt-4">
                        <!-- Expense Permission Switch -->
                        <div class="p-3.5 bg-[#F8F3EA] rounded-2xl border border-[#EFE7DE] flex items-start gap-3">
                            <input type="checkbox" id="allow_seller_expense" wire:model="allow_seller_expense"
                                class="mt-0.5 rounded bg-white border-[#EFE7DE] text-[#F26522] focus:ring-[#F26522] h-4 w-4">
                            <label for="allow_seller_expense" class="text-xs cursor-pointer">
                                <span class="font-bold text-[#2B1E16] block">Allow Sellers to Log Quick Expenses</span>
                                <span class="text-[#8D7B70] text-[11px] block mt-0.5 font-medium">
                                    When enabled, staff can record urgent out-of-pocket costs (ice, emergency buns,
                                    cooking gas) from the quick sell POS.
                                </span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1">Receipt Footer Note</label>
                            <textarea wire:model="receipt_footer" rows="3"
                                placeholder="Thank you for visiting! Have a wonderful day!"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-xl px-3.5 py-2 text-xs text-[#2B1E16] focus:ring-2 focus:ring-[#F26522] focus:outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#EFE7DE] flex justify-end">
                    <button type="submit"
                        class="px-5 py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-xl text-xs shadow-2xs cursor-pointer touch-press">
                        Save Cart Configuration
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Business Days History Log -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl overflow-hidden shadow-2xs">
        <div class="p-4 sm:p-5 border-b border-[#EFE7DE] flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-sm text-[#2B1E16] flex items-center gap-2">
                    <span>📅</span> Business Days History & Reconciliation
                </h3>
                <p class="text-xs text-[#8D7B70] mt-0.5 font-medium">Audit shift opening floats and closing cash
                    records.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-xs text-[#554338]">
                <thead
                    class="text-[11px] uppercase tracking-wider text-[#8D7B70] font-bold bg-[#F8F3EA] border-b border-[#EFE7DE]">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Business Date</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Opening Float</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Total Day Sales</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Day Expenses</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Closing Cash Reported</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE7DE]">
                    @forelse($businessDays as $day)
                        <tr class="hover:bg-[#F8F3EA]/50 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-[#2B1E16]">
                                {{ $day->date->format('d M Y (D)') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $day->status === 'open' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#F8F3EA] text-[#8D7B70] border border-[#EFE7DE]' }}">
                                    {{ $day->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold text-[#554338]">
                                {{ $currency }}{{ number_format($day->opening_cash_float, 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-black text-[#1E8E3E]">
                                {{ $currency }}{{ number_format($day->sales_sum_total_amount ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold text-[#DC2626]">
                                {{ $currency }}{{ number_format($day->expenses_sum_amount ?? 0, 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-[#2B1E16]">
                                {{ $day->closing_cash_amount ? $currency . number_format($day->closing_cash_amount, 0) : '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-[#8D7B70] text-[11px] max-w-xs truncate">
                                {{ $day->notes ?? 'Normal shift close' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-[#8D7B70]">No business day logs recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>