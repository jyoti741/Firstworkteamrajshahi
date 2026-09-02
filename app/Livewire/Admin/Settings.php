<?php

namespace App\Livewire\Admin;

use App\Models\BusinessDay;
use App\Models\CartSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Food Cart Configuration & Business Days')]
class Settings extends Component
{
    public string $cart_name = '';
    public string $cart_tagline = '';
    public string $currency_symbol = '৳';
    public string $phone = '';
    public string $address = '';
    public bool $allow_seller_expense = true;
    public string $receipt_footer = '';

    public function mount(): void
    {
        $this->cart_name = CartSetting::get('cart_name', 'CartFlow Street Kitchen');
        $this->cart_tagline = CartSetting::get('cart_tagline', 'Fresh & Fast Street Food');
        $this->currency_symbol = CartSetting::get('currency_symbol', '৳');
        $this->phone = CartSetting::get('phone', '+880 1711-000000');
        $this->address = CartSetting::get('address', 'Dhanmondi, Dhaka');
        $this->allow_seller_expense = (bool) CartSetting::get('allow_seller_expense', '1');
        $this->receipt_footer = CartSetting::get('receipt_footer', 'Thank you for eating with us!');
    }

    public function saveSettings(): void
    {
        $this->validate([
            'cart_name' => 'required|string|max:150',
            'currency_symbol' => 'required|string|max:10',
            'phone' => 'nullable|string|max:40',
            'address' => 'nullable|string|max:200',
            'receipt_footer' => 'nullable|string|max:300',
        ]);

        CartSetting::set('cart_name', $this->cart_name);
        CartSetting::set('cart_tagline', $this->cart_tagline);
        CartSetting::set('currency_symbol', $this->currency_symbol);
        CartSetting::set('phone', $this->phone);
        CartSetting::set('address', $this->address);
        CartSetting::set('allow_seller_expense', $this->allow_seller_expense ? '1' : '0');
        CartSetting::set('receipt_footer', $this->receipt_footer);

        session()->flash('success', 'Food cart settings updated successfully.');
    }

    public function render()
    {
        $businessDays = BusinessDay::with(['openedBy', 'closedBy'])
            ->withSum(['sales' => fn ($q) => $q->where('status', 'completed')], 'total_amount')
            ->withSum('expenses', 'amount')
            ->latest('date')
            ->take(10)
            ->get();

        return view('livewire.admin.settings', [
            'businessDays' => $businessDays,
            'currency' => CartSetting::currency(),
        ]);
    }
}
