<div class="space-y-6">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-zinc-900 border border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-lg">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>📦</span> Food Inventory & Stock Level
            </h1>
            <p class="text-xs text-zinc-400 mt-1">Track prepared patties, buns, cans, and stock adjustments in real time.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tabs -->
            <div class="flex items-center bg-zinc-950 p-1 rounded-xl border border-zinc-800 text-xs font-semibold">
                <button type="button" wire:click="$set('activeTab', 'stock')" class="px-3 py-1.5 rounded-lg transition-all {{ $activeTab === 'stock' ? 'bg-amber-500 text-zinc-950 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Current Stock
                </button>
                <button type="button" wire:click="$set('activeTab', 'logs')" class="px-3 py-1.5 rounded-lg transition-all {{ $activeTab === 'logs' ? 'bg-amber-500 text-zinc-950 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Audit Logs
                </button>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert Banner -->
    @if($lowStockCount > 0)
        <div class="bg-rose-950/40 border border-rose-800/60 rounded-2xl p-4 flex items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <h4 class="font-bold text-rose-300">Low Stock Warning ({{ $lowStockCount }} items below 10 units)</h4>
                    <p class="text-rose-400/80 mt-0.5">Please restock these items soon to avoid running out during service hours.</p>
                </div>
            </div>

            <button type="button" 
                    wire:click="$toggle('lowStockOnly')"
                    class="px-3 py-1.5 rounded-xl bg-rose-900/60 hover:bg-rose-800 text-rose-200 font-bold text-xs border border-rose-700/50 cursor-pointer shrink-0">
                {{ $lowStockOnly ? 'Show All Stock' : 'Filter Low Stock' }}
            </button>
        </div>
    @endif

    @if($activeTab === 'stock')
        <!-- Stock Table -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="p-4 border-b border-zinc-800 flex items-center justify-between gap-4">
                <input type="text" 
                       wire:model.live.debounce.200ms="search" 
                       placeholder="🔍 Search food inventory..." 
                       class="bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-zinc-500 focus:ring-2 focus:ring-amber-500 focus:outline-none w-72">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="text-[11px] uppercase tracking-wider text-zinc-500 bg-zinc-950/80 border-b border-zinc-800">
                        <tr>
                            <th class="px-4 py-3.5">Food Item</th>
                            <th class="px-4 py-3.5">Category</th>
                            <th class="px-4 py-3.5 text-center">Current Stock</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-center">Quick Restock</th>
                            <th class="px-4 py-3.5 text-center">Adjust</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @forelse($products as $product)
                            <tr class="hover:bg-zinc-850/40 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center text-base">
                                            {{ $product->image_emoji ?? '🍔' }}
                                        </span>
                                        <span class="font-bold text-zinc-100 text-sm">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-zinc-400">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-base font-black {{ $product->current_stock <= 10 ? 'text-rose-400' : 'text-emerald-400' }}">
                                        {{ $product->current_stock }}
                                    </span>
                                    <span class="text-[10px] text-zinc-500 block">units</span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($product->current_stock <= 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">OUT OF STOCK</span>
                                    @elseif($product->current_stock <= 10)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">LOW STOCK</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">IN STOCK</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                wire:click="quickRestock({{ $product->id }}, 10)"
                                                class="px-2.5 py-1 rounded-lg text-emerald-300 hover:text-white bg-emerald-950/40 hover:bg-emerald-900/60 border border-emerald-800/40 font-bold text-xs cursor-pointer">
                                            +10
                                        </button>
                                        <button type="button" 
                                                wire:click="quickRestock({{ $product->id }}, 50)"
                                                class="px-2.5 py-1 rounded-lg text-emerald-300 hover:text-white bg-emerald-950/40 hover:bg-emerald-900/60 border border-emerald-800/40 font-bold text-xs cursor-pointer">
                                            +50
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                wire:click="openAdjustModal({{ $product->id }}, 'restock')"
                                                class="px-2.5 py-1 rounded-lg text-zinc-300 hover:text-white bg-zinc-800 hover:bg-zinc-700 font-semibold cursor-pointer">
                                            Custom...
                                        </button>
                                        <button type="button" 
                                                wire:click="openAdjustModal({{ $product->id }}, 'waste')"
                                                title="Record spoiled/wasted items"
                                                class="px-2 py-1 rounded-lg text-rose-400 hover:text-white bg-rose-950/30 hover:bg-rose-900/50 font-semibold cursor-pointer">
                                            Waste
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                                    No tracked inventory items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Inventory Audit Logs Tab -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="text-[11px] uppercase tracking-wider text-zinc-500 bg-zinc-950/80 border-b border-zinc-800">
                        <tr>
                            <th class="px-4 py-3.5">Timestamp</th>
                            <th class="px-4 py-3.5">Food Item</th>
                            <th class="px-4 py-3.5">Action Type</th>
                            <th class="px-4 py-3.5 text-center">Qty Change</th>
                            <th class="px-4 py-3.5 text-center">Remaining Stock</th>
                            <th class="px-4 py-3.5">Staff / Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @forelse($logs as $log)
                            <tr class="hover:bg-zinc-850/40 transition-colors">
                                <td class="px-4 py-3.5 text-zinc-400 font-medium">
                                    {{ $log->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-white">
                                    {{ $log->product->name ?? 'Deleted Item' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $log->type === 'restock' ? 'bg-emerald-500/20 text-emerald-300' : ($log->type === 'sale' ? 'bg-blue-500/20 text-blue-300' : 'bg-rose-500/20 text-rose-300') }}">
                                        {{ $log->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-black {{ $log->quantity_change > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $log->quantity_change > 0 ? '+'.$log->quantity_change : $log->quantity_change }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-zinc-200">
                                    {{ $log->remaining_stock }}
                                </td>
                                <td class="px-4 py-3.5 text-zinc-400">
                                    <span class="font-medium text-zinc-300">{{ $log->user->name ?? 'System' }}</span>
                                    <span class="text-zinc-500 block text-[11px]">{{ $log->notes }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500">No inventory logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-800">
                {{ $logs->links() }}
            </div>
        </div>
    @endif

    <!-- Stock Adjustment Modal -->
    @if($showAdjustModal && $selectedProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <div>
                        <h3 class="font-bold text-base text-white">Adjust Stock: {{ $selectedProduct->name }}</h3>
                        <p class="text-xs text-zinc-400">Current Stock: <strong class="text-amber-400">{{ $selectedProduct->current_stock }} units</strong></p>
                    </div>
                    <button type="button" wire:click="$set('showAdjustModal', false)" class="text-zinc-400 hover:text-white">✕</button>
                </div>

                <form wire:submit="applyAdjustment" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Adjustment Type</label>
                        <select wire:model="adjustType" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="restock">➕ Restock (Add units to inventory)</option>
                            <option value="waste">🗑️ Food Waste / Spoilage (Deduct units)</option>
                            <option value="adjustment">⚙️ Audit Discrepancy / Correction</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Quantity (Units)</label>
                        <input type="number" wire:model="adjustQuantity" min="1" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3.5 py-2 text-sm text-white font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Notes / Supplier info</label>
                        <textarea wire:model="adjustNotes" rows="2" placeholder="e.g. Received shipment from bakery / expired bun package" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-800">
                        <button type="button" wire:click="$set('showAdjustModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white bg-zinc-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-950 bg-amber-500 hover:bg-amber-400">
                            Confirm Adjustment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
