<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'user_id',
        'business_day_id',
        'total_amount',
        'total_cost',
        'total_profit',
        'total_items_count',
        'payment_method',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'total_profit' => 'decimal:2',
            'total_items_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessDay(): BelongsTo
    {
        return $this->belongsTo(BusinessDay::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('ymd') . '-';
        $lastSale = self::where('invoice_no', 'like', $prefix . '%')->latest('id')->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->invoice_no, strlen($prefix));
            $nextNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
