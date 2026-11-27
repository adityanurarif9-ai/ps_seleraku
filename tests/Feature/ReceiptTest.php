<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\FoodOrder;
use App\Models\User;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_rental_pdf_download_is_private_and_uses_real_order(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u);
        $item = CatalogItem::create(['name' => 'PlayStation 4', 'category' => 'Konsol', 'stock' => 2, 'price' => 10000, 'daily_price' => 90000]);
        $r = app(RentalReservationManager::class)->create(['catalog_item_id' => $item->id, 'unit_number' => 2, 'duration' => 90, 'mode' => 'lounge', 'starts_at' => now()->addHour()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')]);
        $this->get('/reservasi/'.$r->id)->assertOk()->assertSee('Unduh struk PDF');
        $response = $this->get(route('receipts.rental', $r).'?total=1');
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString($r->reference(), $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->actingAs(User::factory()->create());
        $this->get(route('receipts.rental', $r))->assertForbidden();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get(route('receipts.rental', $r))->assertOk();
        auth()->logout();
        $this->get(route('receipts.rental', $r))->assertRedirect(route('login'));
    }

    public function test_food_receipt_works_for_owner_and_admin_but_not_other_players(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u);
        $o = FoodOrder::create(['user_id' => $u->id, 'request_key' => fake()->uuid(), 'status' => 'pending', 'total_price' => 24000]);
        $food = CatalogItem::create(['name' => 'Es Teh', 'category' => 'Minuman', 'price' => 12000, 'stock' => 5]);
        $o->items()->create(['catalog_item_id' => $food->id, 'name' => 'Es Teh', 'quantity' => 2, 'unit_price' => 12000]);
        $this->get('/pesanan-makanan')->assertOk()->assertSee('Unduh struk PDF');
        $pdf = $this->get(route('receipts.food', $o));
        $pdf->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertGreaterThan(3000, strlen($pdf->getContent()));
        $this->actingAs(User::factory()->create());
        $this->get(route('receipts.food', $o))->assertForbidden();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get(route('receipts.food', $o))->assertOk();
    }

    public function test_receipt_html_escapes_notes_and_clearly_marks_demo_and_footer_links_to_maps(): void
    {
        app()->setLocale('id');
        $html = view('receipts.order', ['reference' => 'PS-TEST', 'kind' => 'Rental', 'customer' => '<img src="https://bad.example/a">', 'orderedAt' => now(), 'status' => 'Menunggu', 'payment' => null, 'total' => 15000, 'lines' => [['name' => 'PS5 / Unit 02', 'detail' => '90 menit', 'quantity' => 1, 'unit_price' => 15000]], 'schedule' => null, 'notes' => '<script>alert(1)</script>'])->render();
        $this->assertStringContainsString('DEMO - BUKAN BUKTI LUNAS', $html);
        $this->assertStringContainsString('Rp15.000', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=', $html);
        $this->get('/kontak')->assertOk()->assertSee('loading="lazy"', false)->assertSee('SMK%20Telkom%20Purwokerto', false)->assertSee('Bukan alamat toko.');
    }
}
