<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Services\FoodOrderManager;
use Illuminate\Http\Request;

class FoodOrderController extends Controller
{
    public function index()
    {
        $orders = FoodOrder::where('user_id', auth()->id())->with('items')->latest('id')->paginate(10);

        return response()->view('food-orders', compact('orders'))->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request, FoodOrderManager $manager)
    {
        $order = $manager->create($request->only('request_key', 'notes'));
        $cart = session('shopping_cart', []);
        foreach ($order->items as $line) {
            unset($cart[$line->catalog_item_id]);
        }
        session()->put('shopping_cart', $cart);

        return redirect()->route('food-orders.index', status: 303)->with('order_notice', __('Pesanan terkirim. Tunggu konfirmasi pengelola.'));
    }

    public function cancel(FoodOrder $order, FoodOrderManager $manager)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $manager->change($order, 'cancelled');

        return redirect()->route('food-orders.index', status: 303);
    }
}
