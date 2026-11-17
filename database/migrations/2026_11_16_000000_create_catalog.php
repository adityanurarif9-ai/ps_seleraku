<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });

        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 25);
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
            $table->unique(['name', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
