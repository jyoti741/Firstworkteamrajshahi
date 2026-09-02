<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );
    }

    public static function currency(): string
    {
        return self::get('currency_symbol', '৳');
    }

    public static function cartName(): string
    {
        return self::get('cart_name', 'CartFlow Food Cart');
    }

    public static function allowSellerExpense(): bool
    {
        return (bool) self::get('allow_seller_expense', '1');
    }
}
