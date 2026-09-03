<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_day_id',
        'user_id',
        'event',
        'occurred_at',
        'opening_cash_float',
        'closing_cash_amount',
        'closing_cost',
        'sales_total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'opening_cash_float' => 'decimal:2',
            'closing_cash_amount' => 'decimal:2',
            'closing_cost' => 'decimal:2',
            'sales_total' => 'decimal:2',
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

    public function isOpened(): bool
    {
        return $this->event === 'opened';
    }

    public function isClosed(): bool
    {
        return $this->event === 'closed';
    }

    public static function logOpen(?int $userId = null, float $float = 0, ?string $notes = null, ?int $businessDayId = null): self
    {
        return self::create([
            'business_day_id' => $businessDayId,
            'user_id' => $userId ?? auth()->id(),
            'event' => 'opened',
            'occurred_at' => now(),
            'opening_cash_float' => $float,
            'notes' => $notes ?? 'Cart opened',
        ]);
    }

    public static function logClose(?int $userId = null, ?float $cash = null, ?float $cost = null, ?float $sales = null, ?string $notes = null, ?int $businessDayId = null): self
    {
        return self::create([
            'business_day_id' => $businessDayId,
            'user_id' => $userId ?? auth()->id(),
            'event' => 'closed',
            'occurred_at' => now(),
            'closing_cash_amount' => $cash,
            'closing_cost' => $cost ?? 0,
            'sales_total' => $sales,
            'notes' => $notes ?? 'Cart closed',
        ]);
    }
}
