<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CatalogItemResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('PS Seleraku Admin')
            ->homeUrl(fn () => route('catalog'))
            ->colors(['primary' => Color::Orange])
            ->resources([CatalogItemResource::class])
            ->middleware(['web', DisableBladeIconComponents::class, DispatchServingFilamentEvent::class])
            ->authMiddleware([Authenticate::class], isPersistent: true);
    }
}