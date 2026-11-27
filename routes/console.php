<?php

use App\Models\RentalIdentity;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rentals:purge-identities', function () {
    $this->info(RentalIdentity::purgeExpired().' expired identity photos removed.');
})->purpose('Remove expired encrypted rental identity photos');
Schedule::command('rentals:purge-identities')->daily();
