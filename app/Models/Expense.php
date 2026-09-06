<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_day_id',
        'title',
        'description',
        'category',
        'amount',
        'expense_date',
        'expense_time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->expense_date ? $this->expense_date->format('d M Y') : '';
    }

    public function getFormattedTimeAttribute(): string
    {
        if ($this->expense_time) {
            return \Carbon\Carbon::parse($this->expense_time)->format('h:i A');
        }

        return $this->created_at ? $this->created_at->format('h:i A') : '';
    }

    public function getRecordedAtAttribute(): \Carbon\Carbon
    {
        if ($this->expense_date && $this->expense_time) {
            return \Carbon\Carbon::parse($this->expense_date->format('Y-m-d') . ' ' . $this->expense_time);
        }

        return $this->created_at ?? now();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessDay(): BelongsTo
    {
        return $this->belongsTo(BusinessDay::class);
    }

    public static function categoryLabels(): array
    {
        return [
            'ingredients' => 'Ingredients',
            'raw_materials' => 'Ingredients & Raw Materials',
            'transportation' => 'Transportation',
            'transport' => 'Transportation & Logistics',
            'packaging' => 'Packaging',
            'gas' => 'Gas',
            'utilities' => 'Utilities & Power',
            'salaries' => 'Staff Salaries',
            'rent' => 'Cart Space & Rent',
            'maintenance' => 'Repairs & Maintenance',
            'other' => 'Other / Miscellaneous',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? ucfirst($this->category);
    }

    public function getDescriptionAttribute(?string $value): string
    {
        return $value ?? $this->title ?? '';
    }
}
