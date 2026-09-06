<div class="space-y-4 max-w-4xl mx-auto">

    <!-- Page Header & Actions -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#2B1E16] tracking-tight flex items-center gap-2">
                    <span>⚖️</span> Assets & Liabilities
                </h1>
                <p class="text-xs text-[#8D7B70] font-medium">Simple business record book to track what you own and what you owe.</p>
            </div>

            <button type="button" wire:click="openAddModal('{{ $activeTab }}')"
                class="px-4 py-2.5 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-2xl text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-2xs touch-press cursor-pointer shrink-0">
                <span class="text-base leading-none font-bold">+</span>
                <span>Add Record</span>
            </button>
        </div>

        <!-- Section / Tab Switcher (🟢 Assets | 🔴 Liabilities) -->
        <div class="grid grid-cols-2 bg-white p-1.5 rounded-2xl border border-[#EFE7DE] gap-2 shadow-2xs">
            <button type="button" wire:click="switchTab('asset')"
                class="py-2.5 px-4 rounded-xl text-center text-xs sm:text-sm font-extrabold transition-all flex items-center justify-center gap-2 touch-press cursor-pointer {{ $activeTab === 'asset' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5] shadow-2xs' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                <span class="text-base">🟢</span>
                <span>Assets</span>
                <span class="text-[11px] px-2 py-0.5 rounded-full font-bold {{ $activeTab === 'asset' ? 'bg-[#1E8E3E] text-white' : 'bg-[#EFE7DE] text-[#554338]' }}">
                    {{ $assetsCount }}
                </span>
            </button>

            <button type="button" wire:click="switchTab('liability')"
                class="py-2.5 px-4 rounded-xl text-center text-xs sm:text-sm font-extrabold transition-all flex items-center justify-center gap-2 touch-press cursor-pointer {{ $activeTab === 'liability' ? 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] shadow-2xs' : 'text-[#554338] hover:bg-[#F8F3EA]' }}">
                <span class="text-base">🔴</span>
                <span>Liabilities</span>
                <span class="text-[11px] px-2 py-0.5 rounded-full font-bold {{ $activeTab === 'liability' ? 'bg-[#DC2626] text-white' : 'bg-[#EFE7DE] text-[#554338]' }}">
                    {{ $liabilitiesCount }}
                </span>
            </button>
        </div>
    </div>

    <!-- Active Section Summary Headline Card -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-[#EFE7DE]">
            <div class="flex items-center gap-2">
                <span class="text-lg">{{ $activeTab === 'asset' ? '🟢' : '🔴' }}</span>
                <h2 class="font-extrabold text-sm sm:text-base text-[#2B1E16]">
                    {{ $activeTab === 'asset' ? 'Total Business Assets' : 'Total Business Liabilities' }}
                </h2>
            </div>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $activeTab === 'asset' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5]' : 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]' }}">
                {{ $activeTab === 'asset' ? 'Asset Book' : 'Liability Book' }}
            </span>
        </div>

        <div class="pt-3 flex flex-col sm:flex-row sm:items-baseline justify-between gap-1">
            <div class="text-2xl sm:text-3xl font-black {{ $activeTab === 'asset' ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }} truncate">
                {{ $currency }}{{ number_format($activeTab === 'asset' ? $totalAssets : $totalLiabilities, 0) }}
            </div>
            <p class="text-xs text-[#8D7B70] font-medium">
                {{ $records->count() }} {{ $records->count() === 1 ? 'record' : 'records' }} saved
            </p>
        </div>
    </div>

    <!-- Records Table & List -->
    <div class="bg-white border border-[#EFE7DE] rounded-3xl p-4 sm:p-5 shadow-2xs space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                <span>{{ $activeTab === 'asset' ? '📦' : '💳' }}</span>
                <span>{{ $activeTab === 'asset' ? 'Asset Records' : 'Liability Records' }}</span>
            </h3>
            <button type="button" wire:click="openAddModal('{{ $activeTab }}')"
                class="text-xs font-bold text-[#F26522] hover:text-[#E05310] flex items-center gap-1 cursor-pointer">
                <span>+</span> Add {{ $activeTab === 'asset' ? 'Asset' : 'Liability' }}
            </button>
        </div>

        @if($records->isEmpty())
            <div class="py-10 text-center space-y-2 bg-[#F8F3EA]/60 rounded-2xl border border-dashed border-[#EFE7DE] p-6">
                <div class="text-3xl">{{ $activeTab === 'asset' ? '🟢' : '🔴' }}</div>
                <p class="text-xs sm:text-sm font-bold text-[#2B1E16]">
                    No {{ $activeTab === 'asset' ? 'asset' : 'liability' }} records saved yet.
                </p>
                <p class="text-xs text-[#8D7B70]">
                    {{ $activeTab === 'asset' ? 'Record items like Freezer, Refrigerator, Cash in Hand.' : 'Record items like Business Loan, Supplier Due, Shop Rent Due.' }}
                </p>
                <div class="pt-2">
                    <button type="button" wire:click="openAddModal('{{ $activeTab }}')"
                        class="px-4 py-2 bg-[#F26522] hover:bg-[#E05310] text-white font-extrabold rounded-xl text-xs touch-press cursor-pointer">
                        + Add First {{ $activeTab === 'asset' ? 'Asset' : 'Liability' }}
                    </button>
                </div>
            </div>
        @else
            <!-- Clean Records Data Table (Visible on all screens with horizontal scroll support) -->
            <div class="overflow-x-auto rounded-2xl border border-[#EFE7DE]">
                <table class="w-full text-left border-collapse min-w-[540px]">
                    <thead>
                        <tr class="bg-[#F8F3EA] border-b border-[#EFE7DE] text-[11px] sm:text-xs font-bold text-[#8D7B70] uppercase tracking-wider">
                            <th class="py-3 px-3 sm:px-4">{{ $activeTab === 'asset' ? 'Asset' : 'Liability' }}</th>
                            <th class="py-3 px-3 sm:px-4 text-right">Amount</th>
                            <th class="py-3 px-3 sm:px-4 text-center">Date</th>
                            <th class="py-3 px-3 sm:px-4 text-center">Time</th>
                            <th class="py-3 px-3 sm:px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFE7DE] text-xs sm:text-sm bg-white">
                        @foreach($records as $record)
                            <tr class="hover:bg-[#F8F3EA]/50 transition-colors">
                                <td class="py-3.5 px-3 sm:px-4 font-bold text-[#2B1E16]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $record->type === 'asset' ? 'bg-[#1E8E3E]' : 'bg-[#DC2626]' }} shrink-0"></span>
                                        <span>{{ $record->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 sm:px-4 text-right font-black {{ $record->type === 'asset' ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }} whitespace-nowrap">
                                    {{ $currency }}{{ number_format($record->amount, 0) }}
                                </td>
                                <td class="py-3.5 px-3 sm:px-4 text-center text-[#554338] font-semibold whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#F8F3EA] border border-[#EFE7DE] text-[11px] sm:text-xs font-bold">
                                        <span>📅</span>
                                        <span>{{ $record->formatted_date }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 sm:px-4 text-center text-[#8D7B70] font-semibold whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#F8F3EA] border border-[#EFE7DE] text-[11px] sm:text-xs font-bold">
                                        <span>⏰</span>
                                        <span>{{ $record->formatted_time }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" wire:click="editRecord({{ $record->id }})"
                                            class="px-2.5 py-1 bg-white hover:bg-[#F8F3EA] border border-[#EFE7DE] text-[#2B1E16] rounded-lg font-bold text-xs transition-colors cursor-pointer touch-press">
                                            Edit
                                        </button>
                                        <button type="button" wire:click="deleteRecord({{ $record->id }})"
                                            wire:confirm="Delete this {{ $record->type }} record ({{ $record->name }})?"
                                            class="px-2.5 py-1 bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA] rounded-lg font-bold text-xs transition-colors cursor-pointer touch-press">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bottom Total Calculation Bar -->
            <div class="mt-4 pt-4 border-t-2 border-[#EFE7DE] flex flex-col sm:flex-row items-center justify-between gap-2 bg-[#F8F3EA]/70 p-4 rounded-2xl">
                <div class="font-extrabold text-sm sm:text-base text-[#2B1E16] flex items-center gap-2">
                    <span>{{ $activeTab === 'asset' ? '🟢' : '🔴' }}</span>
                    <span>{{ $activeTab === 'asset' ? 'Total Assets:' : 'Total Liabilities:' }}</span>
                </div>
                <div class="text-lg sm:text-xl font-black {{ $activeTab === 'asset' ? 'text-[#1E8E3E]' : 'text-[#DC2626]' }}">
                    {{ $currency }}{{ number_format($activeTab === 'asset' ? $totalAssets : $totalLiabilities, 0) }}
                </div>
            </div>
        @endif
    </div>

    <!-- Simple Add / Edit Record Modal Form -->
    @if($showRecordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white border border-[#EFE7DE] rounded-3xl w-full max-w-md p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#EFE7DE] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $recordType === 'asset' ? '🟢' : '🔴' }}</span>
                        <h3 class="font-extrabold text-base text-[#2B1E16]">
                            {{ $editingRecordId ? 'Edit Record' : 'Add Record' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('showRecordModal', false)"
                        class="text-[#8D7B70] hover:text-[#2B1E16] font-bold cursor-pointer p-1">✕</button>
                </div>

                <form wire:submit="saveRecord" class="space-y-4">
                    <!-- Record Type Toggle -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">Record Type</label>
                        <div class="grid grid-cols-2 gap-2 bg-[#F8F3EA] p-1 rounded-2xl border border-[#EFE7DE]">
                            <button type="button" wire:click="$set('recordType', 'asset')"
                                class="py-2 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $recordType === 'asset' ? 'bg-[#EAF7EE] text-[#1E8E3E] border border-[#CDEED5] font-black shadow-2xs' : 'text-[#554338]' }}">
                                <span>🟢</span> Asset
                            </button>
                            <button type="button" wire:click="$set('recordType', 'liability')"
                                class="py-2 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $recordType === 'liability' ? 'bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] font-black shadow-2xs' : 'text-[#554338]' }}">
                                <span>🔴</span> Liability
                            </button>
                        </div>
                    </div>

                    <!-- Record Name -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">
                            Record Name
                        </label>
                        <input type="text" wire:model="name"
                            placeholder="{{ $recordType === 'asset' ? 'e.g. Freezer, Refrigerator, Cash' : 'e.g. Supplier Due, Business Loan' }}"
                            class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3.5 py-3 text-sm text-[#2B1E16] placeholder-[#8D7B70] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                            required>
                        @error('name') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Amount (৳) -->
                    <div>
                        <label class="block text-xs font-bold text-[#554338] mb-1.5">
                            Amount ({{ $currency }})
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-lg font-bold text-[#8D7B70]">{{ $currency }}</span>
                            <input type="number" step="0.01" wire:model="amount" placeholder="25000"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl pl-9 pr-4 py-3 text-lg text-[#2B1E16] font-black focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                        </div>
                        @error('amount') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date & Time Row -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Date</label>
                            <input type="date" wire:model="record_date"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none"
                                required>
                            @error('record_date') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#554338] mb-1.5">Time</label>
                            <input type="time" wire:model="record_time"
                                class="w-full bg-[#F8F3EA] border border-[#EFE7DE] rounded-2xl px-3 py-2.5 text-xs sm:text-sm text-[#2B1E16] font-semibold focus:ring-2 focus:ring-[#F26522] focus:outline-none">
                            @error('record_time') <span class="text-[#DC2626] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#EFE7DE]">
                        <button type="button" wire:click="$set('showRecordModal', false)"
                            class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#554338] hover:bg-[#F8F3EA] border border-[#EFE7DE] cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 rounded-2xl text-xs sm:text-sm font-black text-white bg-[#F26522] hover:bg-[#E05310] shadow-2xs touch-press cursor-pointer">
                            Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
