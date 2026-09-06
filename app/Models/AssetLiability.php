<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLiability extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'amount',
        'record_date',
        'record_time',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'record_date' => 'date',
        ];
    }

    public function scopeAssets(Builder $query): Builder
    {
        return $query->where('type', 'asset');
    }

    public function scopeLiabilities(Builder $query): Builder
    {
        return $query->where('type', 'liability');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->record_date ? $this->record_date->format('d M Y') : '';
    }

    public function getFormattedTimeAttribute(): string
    {
        if (! $this->record_time) {
            return '';
        }

        return Carbon::parse($this->record_time)->format('h:i A');
    }
}
