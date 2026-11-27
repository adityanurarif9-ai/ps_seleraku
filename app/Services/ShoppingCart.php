<?php

namespace App\Services;

use App\Models\CatalogItem;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ShoppingCart
{
    public function lines(): Collection
    {
        $cart = session('shopping_cart', []);
        $items = CatalogItem::whereIn('id', array_map(fn ($key) => (int) $key, array_keys($cart)))->where('is_visible', true)->get()->keyBy('id');
        $valid = [];
        $lines = collect();
        foreach ($cart as $id => $entry) {
            $item = $items->get((int) $id);
            if (! $item) {
                continue;
            }
            $quantity = $item->category === 'Konsol' ? 1 : min(99, max(1, (int) ($entry['quantity'] ?? 1)));
            $mode = ($entry['mode'] ?? 'lounge') === 'daily' ? 'daily' : 'lounge';
            $price = $item->category === 'Konsol' && $mode === 'daily' ? $item->daily_price : $item->price;
            $unit_number = $item->category === 'Konsol' ? ($entry['unit_number'] ?? null) : null;
            $valid[$id] = compact('quantity', 'mode', 'unit_number');
            $lines->push(compact('item', 'quantity', 'mode', 'price', 'unit_number') + ['available' => $item->stock >= $quantity && $price !== null && (! $unit_number || $unit_number <= $item->stock)]);
        }
        session()->put('shopping_cart', $valid);

        return $lines;
    }

    public function add(CatalogItem $item, string $mode, ?int $unitNumber = null): void
    {
        $this->check($item, 1, $mode);
        $cart = session('shopping_cart', []);
        if ($unitNumber !== null && ($item->category !== 'Konsol' || $unitNumber < 1 || $unitNumber > $item->stock)) {
            throw ValidationException::withMessages(['cart' => 'Nomor unit tidak tersedia.']);
        }
        $key = $unitNumber !== null ? $item->id.':'.$unitNumber : $item->id;
        if (! isset($cart[$key]) && count($cart) >= 40) {
            throw ValidationException::withMessages(['cart' => __('Keranjang maksimal 40 pilihan.')]);
        }
        $quantity = $item->category === 'Konsol' ? 1 : (($cart[$item->id]['quantity'] ?? 0) + 1);
        $this->check($item, $quantity, $mode);
        $cart[$key] = compact('quantity', 'mode') + ['unit_number' => $unitNumber];
        session()->put('shopping_cart', $cart);
    }

    public function update(CatalogItem $item, int $quantity): void
    {
        $cart = session('shopping_cart', []);
        abort_unless(isset($cart[$item->id]), 404);
        $this->check($item, $quantity, $cart[$item->id]['mode']);
        $cart[$item->id]['quantity'] = $quantity;
        session()->put('shopping_cart', $cart);
    }

    private function check(CatalogItem $item, int $quantity, string $mode): void
    {
        if (! $item->is_visible || $quantity < 1 || $quantity > min(99, $item->stock)
            || ($item->category === 'Konsol' && ($quantity !== 1 || ($mode === 'daily' && $item->daily_price === null)))) {
            throw ValidationException::withMessages(['cart' => __('Pilihan atau jumlah ini belum tersedia. Periksa kembali stok di katalog.')]);
        }
    }
}
