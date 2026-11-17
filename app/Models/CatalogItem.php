<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    public const CATEGORIES = [
        'Konsol' => 'Konsol',
        'Makanan' => 'Makanan',
        'Minuman' => 'Minuman',
        'Camilan' => 'Camilan',
    ];

    protected $fillable = ['name', 'category', 'price', 'stock'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'stock' => 'integer'];
    }
}
