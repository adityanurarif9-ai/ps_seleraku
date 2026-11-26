<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Services\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function index(ShoppingCart $cart)
    {
        $lines = $cart->lines();
        $foodTotal = $lines->filter(fn ($line) => $line['item']->category !== 'Konsol')->sum(fn ($line) => $line['price'] * $line['quantity']);
        return response()->view('cart', compact('lines', 'foodTotal'))->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request, ShoppingCart $cart)
    {
        $data = $request->validate(['catalog_item_id'=>'required|integer|exists:catalog_items,id', 'mode'=>['nullable', Rule::in(['lounge','daily'])]]);
        try {
            $cart->add(CatalogItem::findOrFail($data['catalog_item_id']), $data['mode'] ?? 'lounge');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $exception->redirectTo(route('cart.index'));
            throw $exception;
        }
        return redirect()->route('cart.index', status:303)->with('cart_notice', __('Pilihan ditambahkan ke keranjang.'));
    }

    public function update(Request $request, CatalogItem $item, ShoppingCart $cart)
    {
        $data = $request->validate(['quantity'=>'required|integer|min:1|max:99']);
        $cart->update($item, (int) $data['quantity']);
        return redirect()->route('cart.index', status:303)->with('cart_notice', __('Jumlah diperbarui.'));
    }

    public function destroy(CatalogItem $item)
    {
        $cart = session('shopping_cart', []);
        unset($cart[$item->id]);
        session()->put('shopping_cart', $cart);
        return redirect()->route('cart.index', status:303);
    }
}
