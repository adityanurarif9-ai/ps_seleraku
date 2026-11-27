<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['rental_reservations', 'play_sessions'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedInteger('unit_number')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['rental_reservations', 'play_sessions'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('unit_number'));
        }
    }
};
