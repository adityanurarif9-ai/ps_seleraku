<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_requires_login_and_reloads_current_server_prices(): void
    {
        $this->get('/keranjang')->assertRedirect(route('login'));
        $item=CatalogItem::create(['name'=>'Minuman uji', 'category'=>'Minuman', 'price'=>12000, 'stock'=>5]);
        $this->actingAs(User::factory()->create());
        $this->post('/keranjang',['catalog_item_id'=>$item->id,'price'=>1,'quantity'=>99])->assertRedirect(route('cart.index'));
        $this->assertSame(1,session('shopping_cart.'.$item->id.'.quantity'));
        $this->patch('/keranjang/'.$item->id,['quantity'=>3])->assertSessionHasNoErrors()->assertRedirect(route('cart.index'));
        $this->get('/keranjang')->assertOk()->assertSee('Minuman uji')->assertSee('Rp36.000')->assertSee('value="3"',false);
        $item->update(['price'=>15000]);
        $this->get('/keranjang')->assertSee('Rp45.000');
        $this->delete('/keranjang/'.$item->id)->assertRedirect(route('cart.index'));
        $this->get('/keranjang')->assertSee('Keranjangmu masih kosong');
    }

    public function test_cart_validates_stock_and_preserves_console_mode_for_booking(): void
    {
        $this->actingAs(User::factory()->create())->withSession(['locale'=>'en']);
        $item=CatalogItem::create(['name'=>'PS5 Rental', 'category'=>'Konsol', 'price'=>15000, 'daily_price'=>150000, 'stock'=>1]);
        $this->post('/keranjang',['catalog_item_id'=>$item->id,'mode'=>'daily'])->assertSessionHasNoErrors();
        $this->post('/keranjang',['catalog_item_id'=>$item->id,'mode'=>'daily'])->assertSessionHasNoErrors();
        $this->assertSame(1,session('shopping_cart.'.$item->id.'.quantity'));
        $this->get('/keranjang')->assertOk()->assertSee('Cart')->assertSee('Rp150.000')->assertSee('mode=daily')->assertSee('catalog_item_id='.$item->id);
        $this->patch('/keranjang/'.$item->id,['quantity'=>2])->assertSessionHasErrors('cart');
        $item->update(['is_visible'=>false]);
        $this->post('/keranjang',['catalog_item_id'=>$item->id])->assertSessionHasErrors('cart');
        $this->get('/keranjang')->assertDontSee('PS5 Rental')->assertSee('Your cart is empty');
    }

    public function test_menu_stock_limits_and_cross_page_header_count(): void
    {
        $this->actingAs(User::factory()->create());
        $item=CatalogItem::create(['name'=>'Camilan uji', 'category'=>'Camilan', 'price'=>5000, 'stock'=>2]);
        $this->post('/keranjang',['catalog_item_id'=>$item->id])->assertSessionHasNoErrors();
        $this->patch('/keranjang/'.$item->id,['quantity'=>3])->assertSessionHasErrors('cart');
        $this->patch('/keranjang/'.$item->id,['quantity'=>0])->assertSessionHasErrors('quantity');
        $this->get('/tentang')->assertOk()->assertSee('ps-cart-count')->assertSee(route('cart.index'));
        $this->get('/menu-fnb')->assertOk()->assertSee('Tambah ke keranjang')->assertDontSee('id="fnb-order-submit"',false);
        $this->post('/logout');
        $this->actingAs(User::factory()->create())->get('/keranjang')->assertSee('Keranjangmu masih kosong');
    }
}
