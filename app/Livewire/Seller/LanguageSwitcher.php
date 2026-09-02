<?php

namespace App\Livewire\Seller;

use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $currentLocale = 'en';

    public function mount(): void
    {
        $this->currentLocale = app()->getLocale();
    }

    public function switchLanguage(string $locale): void
    {
        if (! in_array($locale, ['en', 'bn'], true)) {
            return;
        }

        $this->currentLocale = $locale;
        app()->setLocale($locale);
        session(['seller_locale' => $locale]);

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        $this->dispatch('seller-locale-changed', locale: $locale);
        $this->redirect(request()->header('Referer') ?: route('seller.quick-sell'), navigate: true);
    }

    public function render()
    {
        return view('livewire.seller.language-switcher');
    }
}
