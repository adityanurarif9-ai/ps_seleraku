<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_reservations', function (Blueprint $table) {
            $table->boolean('requires_identity_check')->default(false);
            $table->timestamp('pickup_verified_at')->nullable();
        });
        Schema::create('rental_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_reservation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('document_type', 30);
            $table->longText('encrypted_photo');
            $table->timestamp('expires_at')->index();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_identities');
        Schema::table('rental_reservations', fn (Blueprint $table) => $table->dropColumn(['requires_identity_check', 'pickup_verified_at']));
    }
};
