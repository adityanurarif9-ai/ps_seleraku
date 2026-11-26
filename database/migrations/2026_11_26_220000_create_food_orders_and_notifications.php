<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained();
            $t->uuid('request_key');
            $t->string('status', 20)->default('pending')->index();
            $t->unsignedBigInteger('total_price');
            $t->string('notes', 500)->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'request_key']);
        });
        Schema::create('food_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('food_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('catalog_item_id')->constrained();
            $t->string('name');
            $t->unsignedInteger('quantity');
            $t->unsignedBigInteger('unit_price');
            $t->timestamps();
        });
        Schema::create('notifications', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('type');
            $t->morphs('notifiable');
            $t->text('data');
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('food_order_items');
        Schema::dropIfExists('food_orders');
    }
};
