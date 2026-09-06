<?php

namespace App\Livewire\Admin;

use App\Models\AssetLiability;
use App\Models\CartSetting;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Assets & Liabilities')]
class AssetLiabilityManager extends Component
{
    public string $activeTab = 'asset'; // 'asset' or 'liability'

    // Modal state for Add/Edit
    public bool $showRecordModal = false;
    public ?int $editingRecordId = null;

    // Form fields
    public string $recordType = 'asset';
    public string $name = '';
    public ?float $amount = null;
    public string $record_date = '';
    public string $record_time = '';

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['asset', 'liability'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function openAddModal(?string $type = null): void
    {
        $this->reset(['editingRecordId', 'name', 'amount']);
        $this->recordType = $type ?? $this->activeTab;
        $this->record_date = Carbon::today()->toDateString();
        $this->record_time = Carbon::now()->format('H:i');
        $this->showRecordModal = true;
    }

    public function editRecord(int $id): void
    {
        $record = AssetLiability::findOrFail($id);
        $this->editingRecordId = $record->id;
        $this->recordType = $record->type;
        $this->name = $record->name;
        $this->amount = (float) $record->amount;
        $this->record_date = $record->record_date->toDateString();
        $this->record_time = Carbon::parse($record->record_time)->format('H:i');
        $this->showRecordModal = true;
    }

    public function saveRecord(): void
    {
        $this->validate([
            'recordType' => 'required|in:asset,liability',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'record_date' => 'required|date',
            'record_time' => 'nullable|string',
        ]);

        $time = trim($this->record_time) !== ''
            ? Carbon::parse($this->record_time)->format('H:i:s')
            : Carbon::now()->format('H:i:s');

        if ($this->editingRecordId) {
            $record = AssetLiability::findOrFail($this->editingRecordId);
            $record->update([
                'type' => $this->recordType,
                'name' => trim($this->name),
                'amount' => $this->amount,
                'record_date' => $this->record_date,
                'record_time' => $time,
            ]);

            $typeLabel = $this->recordType === 'asset' ? 'Asset' : 'Liability';
            session()->flash('success', "{$typeLabel} updated successfully.");
        } else {
            AssetLiability::create([
                'type' => $this->recordType,
                'name' => trim($this->name),
                'amount' => $this->amount,
                'record_date' => $this->record_date,
                'record_time' => $time,
            ]);

            $typeLabel = $this->recordType === 'asset' ? 'Asset' : 'Liability';
            session()->flash('success', "{$typeLabel} recorded successfully.");
        }

        $this->showRecordModal = false;
        $this->reset(['editingRecordId', 'name', 'amount']);
    }

    public function deleteRecord(int $id): void
    {
        $record = AssetLiability::findOrFail($id);
        $typeLabel = $record->type === 'asset' ? 'Asset' : 'Liability';
        $record->delete();

        session()->flash('success', "{$typeLabel} deleted successfully.");
    }

    public function render()
    {
        $records = AssetLiability::where('type', $this->activeTab)
            ->orderByDesc('record_date')
            ->orderByDesc('record_time')
            ->orderByDesc('id')
            ->get();

        $totalAssets = (float) AssetLiability::assets()->sum('amount');
        $totalLiabilities = (float) AssetLiability::liabilities()->sum('amount');
        $assetsCount = AssetLiability::assets()->count();
        $liabilitiesCount = AssetLiability::liabilities()->count();

        return view('livewire.admin.asset-liability-manager', [
            'records' => $records,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'assetsCount' => $assetsCount,
            'liabilitiesCount' => $liabilitiesCount,
            'currency' => CartSetting::currency(),
        ]);
    }
}
