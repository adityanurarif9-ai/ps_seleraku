<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ManagePlaySessions;
use App\Models\CatalogItem;
use App\Models\PlaySession;
use App\Models\User;
use App\Services\PlaySessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PlaySessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->freezeTime();
    }

    private function item(int $stock = 1): CatalogItem
    {
        return CatalogItem::create(['name' => 'PlayStation 5', 'category' => 'Konsol', 'price' => 15000, 'stock' => $stock]);
    }

    private function start(CatalogItem $item): PlaySession
    {
        return app(PlaySessionManager::class)->start(['catalog_item_id' => $item->id, 'customer_name' => 'Adit', 'minutes' => 60]);
    }

    public function test_pause_resume_extend_and_finish_preserve_server_time(): void
    {
        $manager = app(PlaySessionManager::class);
        $s = $this->start($this->item());
        $this->travel(10)->minutes();
        $s = $manager->change($s, 'pause');
        $this->assertSame(3000, $s->remaining());
        $this->travel(20)->minutes();
        $this->assertSame(3000, $s->fresh()->remaining());
        $s = $manager->change($s, 'resume');
        $this->travel(5)->minutes();
        $this->assertSame(2700, $s->fresh()->remaining());
        $s = $manager->change($s, 'extend', 30);
        $this->assertSame(4500, $s->remaining());
        $this->assertSame(5400, $s->total_seconds);
        $this->assertSame(22500, $s->estimatedPrice());
        $s = $manager->change($s, 'finish');
        $this->assertSame('finished', $s->status);
        $this->assertSame(0, $s->remaining());
        $this->assertNotNull($s->finished_at);
        $this->assertSame('finished', $manager->change($s, 'resume')->status);
    }

    public function test_unit_capacity_is_reserved_while_paused_and_released_after_finish(): void
    {
        $item = $this->item();
        $s = $this->start($item);
        $manager = app(PlaySessionManager::class);
        $manager->change($s, 'pause');
        try {
            $this->start($item);
            $this->fail('Occupied unit should not start again.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('catalog_item_id', $e->errors());
        }
        $manager->change($s, 'finish');
        $this->assertSame('running', $this->start($item)->status);
    }

    public function test_expired_timer_stays_zero_and_releases_capacity_without_browser(): void
    {
        $item = $this->item();
        $s = $this->start($item);
        $this->travel(61)->minutes();
        $this->assertSame('expired', $s->fresh()->displayStatus());
        $this->assertSame(0, $s->fresh()->remaining());
        $this->assertSame('running', $this->start($item)->status);
        $this->expectException(ValidationException::class);
        app(PlaySessionManager::class)->change($s, 'extend', 30);
    }

    public function test_player_cannot_create_or_change_sessions(): void
    {
        $s = $this->start($this->item());
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->get('/admin/play-sessions')->assertRedirect(url('/admin/login'));
        try {
            app(PlaySessionManager::class)->change($s, 'finish');
            $this->fail('Must reject player.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame('running', $s->fresh()->status);
    }

    public function test_admin_page_renders_and_actions_update_real_session(): void
    {
        $s = $this->start($this->item());
        $this->get('/admin/play-sessions')->assertOk()->assertSee('Adit');
        Livewire::test(ManagePlaySessions::class)->callTableAction('pause', $s)->assertHasNoTableActionErrors();
        $this->assertSame('paused', $s->fresh()->status);
        Livewire::test(ManagePlaySessions::class)->callTableAction('extend', $s, ['minutes' => 15])->assertHasNoTableActionErrors();
        $this->assertSame(4500, $s->fresh()->remaining());
        Livewire::test(ManagePlaySessions::class)->callTableAction('resume',$s)->assertHasNoTableActionErrors();
        $this->assertSame('running',$s->fresh()->status);
    }
}
