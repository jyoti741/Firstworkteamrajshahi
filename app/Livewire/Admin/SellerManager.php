<?php

namespace App\Livewire\Admin;

use App\Models\CartSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Staff & Seller Account Management')]
class SellerManager extends Component
{
    // Modal state for Add/Edit Seller
    public bool $showSellerModal = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'seller'; // 'seller', 'admin'
    public string $phone = '';
    public string $password = '';
    public bool $is_active = true;

    public function openAddModal(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password']);
        $this->role = 'seller';
        $this->is_active = true;
        $this->showSellerModal = true;
    }

    public function editSeller(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone = $user->phone ?? '';
        $this->password = ''; // leave blank if keeping same
        $this->is_active = $user->is_active;
        $this->showSellerModal = true;
    }

    public function saveSeller(): void
    {
        $rules = [
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,'.($this->editingUserId ?? 'NULL'),
            'role' => 'required|in:admin,seller',
            'phone' => 'nullable|string|max:30',
        ];

        if (! $this->editingUserId || ! empty($this->password)) {
            $rules['password'] = 'required|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            User::findOrFail($this->editingUserId)->update($data);
            session()->flash('success', "Staff account '{$this->name}' updated.");
        } else {
            $data['email_verified_at'] = now();
            User::create($data);
            session()->flash('success', "New staff account '{$this->name}' created.");
        }

        $this->showSellerModal = false;
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        $user->is_active = ! $user->is_active;
        $user->save();
        session()->flash('success', "Account status updated for {$user->name}.");
    }

    public function render()
    {
        $today = Carbon::today();

        $users = User::withCount(['sales' => fn ($q) => $q->where('status', 'completed')])
            ->withSum(['sales' => fn ($q) => $q->where('status', 'completed')], 'total_amount')
            ->withSum(['sales' => fn ($q) => $q->where('status', 'completed')->whereDate('created_at', $today)], 'total_amount')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.seller-manager', [
            'users' => $users,
            'currency' => CartSetting::currency(),
        ]);
    }
}
