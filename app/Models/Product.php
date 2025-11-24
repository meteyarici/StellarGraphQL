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


    public function searchableAs(): string
    {
        return 'products';
    }
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,   // <-- string’e çevirdik
            'title' => (string) $this->title,
            'description' => (string) $this->description,
            'price' => (float) $this->price,
            'brand' => $this->brand,
            'stock' => $this->stock,
        ];
    }

    public function typesenseSearchableFields(): array
    {
        return ['title', 'description']; // text search alanları
    }

    public function getSearchSettings(): array
    {
        // Önemli: TypeSense'in tam metin araması yapacağı alanlar.
        $fields = [
            'title',          // Ürün adı
            'description',   // Ürün açıklaması
            'sku',           // Stok Kodu
        ];

        return [
            // Arama motorunun tam metin arama yapacağı alanlar (Hata çözümü için zorunlu)
            'fields' => $fields,

            // Filtreleme için kullanılacak alanlar (Daha önceki sorunlardan çözülenler)
            'filterable_fields' => [
                'title',
                'brand',
                'stock',
                'price',
            ],

            // Sıralama için kullanılacak alanlar (Opsiyonel)
            // 'sortable_fields' => ['price', 'created_at'],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getMeiliSearchSettings(): array
    {
        return [
            // Filtreleme için kullanılacak alanlar: 'stock' ve 'brand'
            'filterableAttributes' => [
                'title',
                'brand',
                'stock',
                'price',
                'in_stock',
            ],
             'sortableAttributes' => [
                 'price',
             ],
        ];
    }
}
