<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\FoodOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FoodOrderManager
{
    public function create(array $data): FoodOrder
    {
        abort_unless(auth()->check(), 403);
        $data = validator($data, ['request_key' => 'required|uuid', 'notes' => 'nullable|string|max:500'])->validate();
        $cart = session('shopping_cart', []);

        return DB::transaction(function () use ($data, $cart) {
            User::whereKey(auth()->id())->lockForUpdate()->firstOrFail();
            if ($existing = FoodOrder::where('user_id', auth()->id())->where('request_key', $data['request_key'])->first()) {
                return $existing;
            }
            if (FoodOrder::where('user_id', auth()->id())->whereIn('status', ['pending', 'preparing', 'ready'])->count() >= 5) {
                throw ValidationException::withMessages(['order' => __('Maksimal 5 pesanan makanan aktif.')]);
            }
            $items = CatalogItem::whereIn('id', array_keys($cart))->orderBy('id')->lockForUpdate()->get();
            $lines = [];
            $total = 0;
            foreach ($items as $item) {
                if ($item->category === 'Konsol') {
                    continue;
                }
                $qty = filter_var($cart[$item->id]['quantity'] ?? 0, FILTER_VALIDATE_INT);
                if (! $item->is_visible || ! $qty || $qty < 1 || $qty > 99 || $qty > $item->stock) {
                    throw ValidationException::withMessages(['order' => __('Stok menu berubah. Periksa kembali keranjangmu.')]);
                }
                $lines[] = ['catalog_item_id' => $item->id, 'name' => $item->name, 'quantity' => $qty, 'unit_price' => $item->price];
                $total += $qty * $item->price;
            }
            if (! $lines) {
                throw ValidationException::withMessages(['order' => __('Tambahkan makanan atau minuman ke keranjang dahulu.')]);
            }
            // Missing/deleted menu entries must not be silently dropped from checkout.
            foreach (array_keys($cart) as $id) {
                if (! $items->contains('id', (int) $id)) {
                    throw ValidationException::withMessages(['order' => __('Stok menu berubah. Periksa kembali keranjangmu.')]);
                }
            }
            $order = FoodOrder::create(['user_id' => auth()->id(), 'request_key' => $data['request_key'], 'notes' => $data['notes'] ?? null, 'total_price' => $total, 'status' => 'pending']);
            $order->items()->createMany($lines);
            foreach ($lines as $line) {
                CatalogItem::whereKey($line['catalog_item_id'])->decrement('stock', $line['quantity']);
            }
            app(AdminAlerts::class)->send('Pesanan kedai baru', $order->reference().' • '.auth()->user()->name.' • Rp'.number_format($total, 0, ',', '.'), url('/admin/food-orders'));

            return $order;
        }, 3);
    }

    public function change(FoodOrder $order, string $status): void
    {
        abort_unless(auth()->user()?->is_admin || ($status === 'cancelled' && $order->user_id === auth()->id()), 403);
        DB::transaction(function () use ($order, $status) {
            $ids = $order->items()->pluck('catalog_item_id');
            CatalogItem::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            $o = FoodOrder::lockForUpdate()->findOrFail($order->id);
            if ($o->status === $status) {
                return;
            }
            $allowed = ['pending' => ['preparing', 'rejected', 'cancelled'], 'preparing' => ['ready', 'rejected'], 'ready' => ['completed']];
            if (! in_array($status, $allowed[$o->status] ?? [])) {
                throw ValidationException::withMessages(['order' => __('Status pesanan sudah berubah. Muat ulang halaman.')]);
            }
            if (in_array($status, ['rejected', 'cancelled'])) {
                foreach ($o->items as $line) {
                    CatalogItem::whereKey($line->catalog_item_id)->increment('stock', $line->quantity);
                }
            }
            $o->update(['status' => $status]);
        }, 3);
    }
}
