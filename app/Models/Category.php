<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function displayName(?string $locale = null): string
    {
        $currentLocale = $locale ?? session('seller_locale', auth()->user()?->locale ?? app()->getLocale());
        if ($currentLocale === 'bn') {
            if (! empty($this->name_bn)) {
                return $this->name_bn;
            }

            $dictionary = [
                'burgers' => 'বার্গার',
                'burger' => 'বার্গার',
                'crispy chicken' => 'ক্রিস্পি চিকেন',
                'chicken' => 'চিকেন',
                'fries & sides' => 'ফ্রেঞ্চ ফ্রাইস ও সাইডস',
                'fries' => 'ফ্রেঞ্চ ফ্রাই',
                'french fries' => 'ফ্রেঞ্চ ফ্রাই',
                'drinks & shakes' => 'ড্রিংকস ও শেকস',
                'drinks' => 'ড্রিংকস',
                'beverages' => 'পানীয়',
                'value combos' => 'কম্বো অফার',
                'combos' => 'কম্বো',
                'fast food' => 'ফাস্ট ফুড',
                'fuska' => 'ফুচকা',
                'fuchka' => 'ফুচকা',
                'chotpoti' => 'চটপটি',
                'noodles' => 'নুডলস',
                'noodle' => 'নুডলস',
                'chowmein' => 'চাওমিন',
                'pizza' => 'পিৎজা',
                'rice' => 'ভাত',
                'biryani' => 'বিরিয়ানি',
                'roll' => 'রোল',
                'rolls' => 'রোল',
                'shawarma' => 'শর্মা',
                'sandwich' => 'স্যান্ডউইচ',
                'sandwiches' => 'স্যান্ডউইচ',
                'coffee' => 'কফি',
                'tea' => 'চা',
                'ice cream' => 'আইসক্রিম',
                'cake' => 'কেক',
                'desserts' => 'মিষ্টি ও ডেজার্ট',
                'dessert' => 'ডেজার্ট',
                'snacks' => 'স্ন্যাকস',
                'snack' => 'স্ন্যাকস',
                'general' => 'সাধারণ',
            ];

            $lower = mb_strtolower(trim($this->name));
            if (isset($dictionary[$lower])) {
                return $dictionary[$lower];
            }
        }

        return $this->name;
    }

    public function getLocalizedNameAttribute(): string
    {
        return $this->displayName();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('sort_order');
    }

    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_available', true)->orderBy('sort_order');
    }
}
