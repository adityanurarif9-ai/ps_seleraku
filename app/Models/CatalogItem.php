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

    protected $attributes = ['is_visible' => true];

    protected $fillable = ['name', 'category', 'price', 'stock', 'daily_price', 'platform', 'description', 'image_path', 'games', 'is_visible'];

    public function platformKey(): string
    {
        if ($this->platform) {
            return $this->platform;
        }
        $name = strtolower($this->name);

        return str_contains($name, 'nintendo') ? 'nintendo' : (str_contains($name, '5') ? 'ps5' : (str_contains($name, '4') ? 'ps4' : (str_contains($name, '3') ? 'ps3' : 'other')));
    }

    public function image(): string
    {
        return $this->image_path ?: match ($this->platformKey()) {
            'ps5' => 'images/ps5-ultra.png','ps4' => 'images/ps4-pro.png','ps3' => 'images/ps3-unit.png','nintendo' => 'images/nintendo-unit.png',default => $this->category === 'Minuman' ? 'images/fnb-esteh.jpg' : 'images/fnb-kentang.jpg'
        };
    }

    protected function casts(): array
    {
        return ['price' => 'integer', 'stock' => 'integer', 'daily_price' => 'integer', 'games' => 'array', 'is_visible' => 'boolean'];
    }
}
