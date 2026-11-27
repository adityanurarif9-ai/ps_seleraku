<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_reservations', function (Blueprint $t) {
            $t->string('contact_name', 100)->nullable();
            $t->string('contact_phone', 30)->nullable();
        });
        Schema::create('test_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained();
            $t->foreignId('rental_reservation_id')->nullable()->unique()->constrained();
            $t->foreignId('food_order_id')->nullable()->unique()->constrained();
            $t->unsignedBigInteger('amount');
            $t->string('method', 30)->nullable();
            $t->string('status', 25)->default('pending');
            $t->timestamp('simulated_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_payments');
        Schema::table('rental_reservations', fn (Blueprint $t) => $t->dropColumn(['contact_name', 'contact_phone']));
    }
};
