<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'name_bn',
        'description',
        'price',
        'cost_price',
        'image_emoji',
        'image_path',
        'current_stock',
        'track_inventory',
        'is_available',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'current_stock' => 'integer',
            'track_inventory' => 'boolean',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function displayName(?string $locale = null): string
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($currentLocale === 'bn' && !empty($this->name_bn)) {
            return $this->name_bn;
        }

        return $this->name;
    }

    public function getLocalizedNameAttribute(): string
    {
        return $this->displayName();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return asset('storage/' . $this->image_path);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class)->latest();
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->price <= 0) {
            return 0.0;
        }

        return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
    }

    public function getUnitProfitAttribute(): float
    {
        return max(0, $this->price - $this->cost_price);
    }

    public function adjustStock(int $qtyChange, string $type, ?int $userId = null, ?string $notes = null): void
    {
        $this->current_stock += $qtyChange;
        $this->save();

        $this->inventoryLogs()->create([
            'user_id' => $userId,
            'type' => $type,
            'quantity_change' => $qtyChange,
            'remaining_stock' => $this->current_stock,
            'notes' => $notes,
        ]);
    }
}
