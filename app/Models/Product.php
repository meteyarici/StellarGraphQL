<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    protected $fillable = [
        'uuid', 'title', 'slug', 'sku', 'brand', 'price', 'stock', 'is_active'
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
