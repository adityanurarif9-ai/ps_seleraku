<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CatalogItemResource;
use App\Filament\Resources\PlaySessionResource;
use App\Filament\Resources\RentalReservationResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Ringkasan';

    public function getHeading(): string
    {
        return 'Dashboard PS Seleraku';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola katalog yang tampil di website, pantau reservasi, dan atur waktu main.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reservations')->label('Reservasi pengguna')->url(RentalReservationResource::getUrl()),
            Action::make('sessions')->label('Atur waktu main')->url(PlaySessionResource::getUrl()),
            Action::make('catalog')->label('Kelola katalog')->url(CatalogItemResource::getUrl()),
            Action::make('content')->label('Isi website')->url(WebsiteSettings::getUrl()),
            Action::make('website')->label('Lihat website')->color('gray')->url(route('home-page')),
        ];
    }
}
