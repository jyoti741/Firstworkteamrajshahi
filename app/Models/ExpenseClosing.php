<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'closed_at',
        'total_sales',
        'total_expenses',
        'net_profit',
        'sales_count',
        'expenses_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_sales' => 'decimal:2',
            'total_expenses' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'period_start' => 'datetime',
            'closed_at' => 'datetime',
            'sales_count' => 'integer',
            'expenses_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsProfitAttribute(): bool
    {
        return (float) $this->net_profit >= 0;
    }

    public function getFormattedClosedAtAttribute(): string
    {
        return $this->closed_at ? $this->closed_at->format('d M · h:i A') : '';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->closed_at ? $this->closed_at->format('d M Y') : '';
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->closed_at ? $this->closed_at->format('h:i A') : '';
    }

    public function getFormattedPeriodAttribute(): string
    {
        if ($this->period_start) {
            return $this->period_start->format('d M, h:i A') . ' → ' . $this->closed_at->format('d M, h:i A');
        }

        return 'Initial period → ' . $this->closed_at->format('d M, h:i A');
    }
}
