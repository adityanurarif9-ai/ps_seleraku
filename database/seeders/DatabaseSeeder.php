<?php

namespace Database\Seeders;

use App\Models\CatalogItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@psseleraku.test'], [
            'name' => 'Admin Demo', 'password' => 'Seleraku!2026', 'is_admin' => true,
        ]);
        User::firstOrCreate(['email' => 'pemain@psseleraku.test'], [
            'name' => 'Pemain Demo', 'password' => 'Mabar!2026', 'is_admin' => false,
        ]);

        foreach ([
            ['PlayStation 5', 'Konsol', 15000, 2],
            ['PlayStation 4', 'Konsol', 10000, 2],
            ['PlayStation 3', 'Konsol', 6000, 1],
            ['Nintendo Switch', 'Konsol', 12000, 1],
            ['Mie Goreng', 'Makanan', 10000, 15],
            ['Nasi Goreng', 'Makanan', 15000, 12],
            ['Es Teh', 'Minuman', 4000, 25],
            ['Air Mineral', 'Minuman', 5000, 16],
            ['Kentang Goreng', 'Camilan', 8000, 8],
            ['Keripik', 'Camilan', 5000, 10],
        ] as [$name, $category, $price, $stock]) {
            CatalogItem::firstOrCreate(compact('name', 'category'), compact('price', 'stock'));
        }
    }
};
