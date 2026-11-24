<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_items', fn (Blueprint $t) => $t->unsignedInteger('daily_price')->nullable());
        foreach (['PlayStation 5' => 150000, 'PlayStation 4' => 90000, 'PlayStation 3' => 50000, 'Nintendo Switch' => 120000] as $name => $price) {
            DB::table('catalog_items')->where('name', $name)->where('category', 'Konsol')->update(['daily_price' => $price]);
        }
        Schema::create('rental_reservations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('catalog_item_id')->constrained()->restrictOnDelete();
            $t->foreignId('play_session_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $t->string('mode', 10);
            $t->string('status', 20)->default('pending')->index();
            $t->dateTime('starts_at')->index();
            $t->dateTime('ends_at')->index();
            $t->unsignedInteger('duration_minutes');
            $t->unsignedInteger('unit_rate');
            $t->unsignedBigInteger('total_price');
            $t->string('notes', 500)->nullable();
            $t->string('admin_note', 500)->nullable();
            $t->timestamps();
            $t->index(['catalog_item_id', 'status', 'starts_at']);
        });
        Schema::create('rental_extension_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('rental_reservation_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('minutes');
            $t->string('status', 20)->default('pending');
            $t->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $t->timestamp('decided_at')->nullable();
            $t->timestamps();
            $t->index(['rental_reservation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_extension_requests');
        Schema::dropIfExists('rental_reservations');
        Schema::table('catalog_items', fn (Blueprint $t) => $t->dropColumn('daily_price'));
    }
};
