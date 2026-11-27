<?php

namespace App\Providers\Filament;

use App\Filament\Auth\AdminLogin;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\WebsiteSettings;
use App\Filament\Resources\CatalogItemResource;
use App\Filament\Resources\FoodOrderResource;
use App\Filament\Resources\PlaySessionResource;
use App\Filament\Resources\RentalExtensionRequestResource;
use App\Filament\Resources\RentalReservationResource;
use App\Filament\Widgets\CatalogOverview;
use App\Filament\Widgets\LowStockItems;
use App\Http\Middleware\AuthenticateAdmin;
use App\Support\SiteAsset;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            ->darkMode(false)
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->brandName('PS Seleraku Admin')
            ->brandLogo(fn () => new HtmlString(view('filament.brand-logo')->render()))
            ->brandLogoHeight('3rem')
            ->font('Poppins')
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => new HtmlString('<link rel="stylesheet" href="'.asset('admin.css').'?v='.filemtime(public_path('admin.css')).'">'))
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => new HtmlString('<link rel="stylesheet" href="'.SiteAsset::url('console-pattern.css').'">'))
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn () => view('filament.admin-login-note'), scopes: AdminLogin::class)
            ->homeUrl(fn () => Dashboard::getUrl())
            ->colors(['primary' => Color::Blue])
            ->pages([Dashboard::class, WebsiteSettings::class])
            ->widgets([CatalogOverview::class, LowStockItems::class])
            ->resources([FoodOrderResource::class, CatalogItemResource::class, PlaySessionResource::class, RentalReservationResource::class, RentalExtensionRequestResource::class])
            ->middleware(['web', DisableBladeIconComponents::class, DispatchServingFilamentEvent::class])
            ->authMiddleware([AuthenticateAdmin::class], isPersistent: true);
    }
}
