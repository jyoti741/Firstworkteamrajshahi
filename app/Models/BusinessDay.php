<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'status',
        'opening_cash_float',
        'closing_cash_amount',
        'closing_cost',
        'opened_at',
        'closed_at',
        'opened_by_id',
        'closed_by_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'opening_cash_float' => 'decimal:2',
            'closing_cash_amount' => 'decimal:2',
            'closing_cost' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    protected static function booted(): void
    {
        static::created(function (BusinessDay $day) {
            if ($day->opened_at) {
                CartStatusLog::create([
                    'business_day_id' => $day->id,
                    'user_id' => $day->opened_by_id,
                    'event' => 'opened',
                    'occurred_at' => $day->opened_at,
                    'opening_cash_float' => $day->opening_cash_float ?? 0,
                    'notes' => $day->notes ?: 'Cart opened',
                ]);
            }

            if ($day->closed_at) {
                CartStatusLog::create([
                    'business_day_id' => $day->id,
                    'user_id' => $day->closed_by_id,
                    'event' => 'closed',
                    'occurred_at' => $day->closed_at,
                    'closing_cash_amount' => $day->closing_cash_amount,
                    'closing_cost' => $day->closing_cost ?? 0,
                    'notes' => $day->notes ?: 'Cart closed',
                ]);
            }
        });

        static::updated(function (BusinessDay $day) {
            if ($day->wasChanged('closed_at') && $day->closed_at) {
                CartStatusLog::create([
                    'business_day_id' => $day->id,
                    'user_id' => $day->closed_by_id,
                    'event' => 'closed',
                    'occurred_at' => $day->closed_at,
                    'closing_cash_amount' => $day->closing_cash_amount,
                    'closing_cost' => $day->closing_cost ?? 0,
                    'sales_total' => (float) $day->sales()->where('status', 'completed')->sum('total_amount'),
                    'notes' => $day->notes ?: 'Cart closed',
                ]);
            }

            if ($day->wasChanged('status') && $day->status === 'open') {
                CartStatusLog::create([
                    'business_day_id' => $day->id,
                    'user_id' => $day->opened_by_id,
                    'event' => 'opened',
                    'occurred_at' => now(),
                    'opening_cash_float' => $day->opening_cash_float ?? 0,
                    'notes' => 'Cart reopened/started',
                ]);
            }
        });
    }

    /**
     * Start a brand-new open cart session/shift
     */
    public static function startNewSession(?int $userId = null, float $float = 0): self
    {
        return self::create([
            'date' => Carbon::today()->toDateString(),
            'status' => 'open',
            'opened_at' => now(),
            'opened_by_id' => $userId ?? auth()->id(),
            'closed_at' => null,
            'closed_by_id' => null,
            'opening_cash_float' => $float,
            'closing_cost' => 0,
            'closing_cash_amount' => null,
        ]);
    }

    /**
     * Get the currently active open cart session (or null if closed)
     */
    public static function activeSession(): ?self
    {
        return self::where('status', 'open')->latest('id')->first();
    }

    /**
     * Open active session or reopen today's session or start a new day session
     */
    public static function openActiveOrNew(?int $userId = null, float $float = 0): self
    {
        $active = self::activeSession();
        if ($active) {
            return $active;
        }

        $todayRecord = self::whereDate('date', Carbon::today()->toDateString())->latest('id')->first();
        if ($todayRecord) {
            $todayRecord->update([
                'status' => 'open',
                'closed_at' => null,
                'closed_by_id' => null,
                'opened_at' => $todayRecord->opened_at ?? now(),
                'opened_by_id' => $userId ?? auth()->id() ?? $todayRecord->opened_by_id,
            ]);

            return $todayRecord;
        }

        return self::startNewSession($userId, $float);
    }

    /**
     * Open cart: if currently closed, start a new session; otherwise return active session
     */
    public function openCart(?int $userId = null, float $float = 0): self
    {
        if ($this->isClosed()) {
            return self::startNewSession($userId, $float);
        }

        $this->update([
            'status' => 'open',
            'opened_at' => $this->opened_at ?? now(),
            'opened_by_id' => $userId ?? auth()->id() ?? $this->opened_by_id,
            'opening_cash_float' => $float > 0 ? $float : $this->opening_cash_float,
        ]);

        return $this;
    }

    public function closeCart(?int $userId = null, ?float $closingCost = null, ?float $closingCash = null, ?string $notes = null): self
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by_id' => $userId ?? auth()->id(),
            'closing_cost' => $closingCost ?? $this->closing_cost,
            'closing_cash_amount' => $closingCash ?? $this->closing_cash_amount,
            'notes' => $notes ?? $this->notes,
        ]);

        return $this;
    }

    public static function current(): ?self
    {
        $active = self::activeSession();
        if ($active) {
            return $active;
        }

        $latest = self::whereDate('date', Carbon::today()->toDateString())->latest('id')->first();
        if ($latest) {
            return $latest;
        }

        return self::create([
            'date' => Carbon::today()->toDateString(),
            'status' => 'closed',
            'opened_at' => null,
            'opened_by_id' => null,
            'opening_cash_float' => 0,
            'closing_cost' => 0,
            'closing_cash_amount' => null,
        ]);
    }
}
