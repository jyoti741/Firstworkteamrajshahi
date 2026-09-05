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

    public static array $banglaTranslations = [
        'fuska' => 'ফুচকা',
        'fuchka' => 'ফুচকা',
        'phuchka' => 'ফুচকা',
        'chotpoti' => 'চটপটি',
        'biryani' => 'বিরিয়ানি',
        'burger' => 'বার্গার',
        'burgers' => 'বার্গার',
        'chicken' => 'চিকেন',
        'crispy chicken' => 'ক্রিস্পি চিকেন',
        'fries' => 'ফ্রাইজ',
        'fries & sides' => 'ফ্রেঞ্চ ফ্রাইস ও সাইডস',
        'sides' => 'সাইডস',
        'drinks' => 'ড্রিংকস ও জুস',
        'drinks & shakes' => 'ড্রিংকস ও শেকস',
        'juice' => 'জুস',
        'beverage' => 'পানীয়',
        'beverages' => 'পানীয়',
        'tea' => 'চা',
        'coffee' => 'কফি',
        'pizza' => 'পিজ্জা',
        'shawarma' => 'শর্মা',
        'roll' => 'রোল',
        'snacks' => 'নাস্তা',
        'snack' => 'নাস্তা',
        'street food' => 'স্ট্রিট ফুড',
        'fast food' => 'ফাস্ট ফুড',
        'main meals' => 'প্রধান খাবার',
        'main food' => 'প্রধান খাবার',
        'value combos' => 'কম্বো অফার',
        'combos' => 'কম্বো',
        'combo' => 'কম্বো',
        'sweets' => 'মিষ্টান্ন',
        'sweet' => 'মিষ্টান্ন',
        'desserts' => 'মিষ্টান্ন',
        'dessert' => 'মিষ্টান্ন',
        'general' => 'সাধারণ',
    ];

    public static function translateToBangla(string $name): ?string
    {
        $clean = mb_strtolower(trim($name), 'UTF-8');
        if (isset(self::$banglaTranslations[$clean])) {
            return self::$banglaTranslations[$clean];
        }

        foreach (self::$banglaTranslations as $key => $bn) {
            if (str_contains($clean, $key)) {
                return $bn;
            }
        }

        return null;
    }

    public function displayName(?string $locale = null): string
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($currentLocale === 'bn') {
            if (! empty($this->name_bn)) {
                return $this->name_bn;
            }

            // Check if name contains Bengali characters e.g. "Fuska(ফুচকা)"
            if (preg_match('/[\x{0980}-\x{09FF}]+/u', $this->name, $matches)) {
                return $matches[0];
            }

            // Common category translations fallback
            $translated = self::translateToBangla($this->name);
            if ($translated) {
                return $translated;
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
