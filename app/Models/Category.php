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
        $currentLocale = $locale ?? app()->getLocale();
        if ($currentLocale === 'bn' && ! empty($this->name_bn)) {
            return $this->name_bn;
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
