<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Services\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(ShoppingCart $cart)
    {
        $lines = $cart->lines();
        $foodTotal = $lines->filter(fn ($line) => $line['item']->category !== 'Konsol')->sum(fn ($line) => $line['price'] * $line['quantity']);

        $menuItems = CatalogItem::whereIn('category', ['Makanan', 'Minuman', 'Camilan'])->where('is_visible', true)->where('stock', '>', 0)->orderBy('category')->orderBy('name')->get();

        return response()->view('cart', compact('lines', 'foodTotal', 'menuItems'))->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request, ShoppingCart $cart)
    {
        $data = $request->validate(['catalog_item_id' => 'required|integer|exists:catalog_items,id', 'unit_number' => 'nullable|integer|min:1', 'mode' => ['nullable', Rule::in(['lounge', 'daily'])]]);
        try {
            $cart->add(CatalogItem::findOrFail($data['catalog_item_id']), $data['mode'] ?? 'lounge', isset($data['unit_number']) ? (int) $data['unit_number'] : null);
        } catch (ValidationException $exception) {
            $exception->redirectTo(route('cart.index'));
            throw $exception;
        }

        return redirect()->route('cart.index', status: 303)->with('cart_notice', __('Pilihan ditambahkan ke keranjang.'));
    }

    public function update(Request $request, CatalogItem $item, ShoppingCart $cart)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cart->update($item, (int) $data['quantity']);

        return redirect()->route('cart.index', status: 303)->with('cart_notice', __('Jumlah diperbarui.'));
    }

    public function destroy(Request $request, CatalogItem $item)
    {
        $cart = session('shopping_cart', []);
        $data = $request->validate(['unit_number' => 'nullable|integer|min:1']);
        $key = isset($data['unit_number']) ? $item->id.':'.$data['unit_number'] : $item->id;
        unset($cart[$key]);
        session()->put('shopping_cart', $cart);

        return redirect()->route('cart.index', status: 303);
    }
}
