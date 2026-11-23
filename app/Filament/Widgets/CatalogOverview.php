<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CatalogItemResource;
use App\Models\CatalogItem;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Unit konsol tersedia', CatalogItem::where('category', 'Konsol')->sum('stock'))
                ->description('Jumlah stok konsol dalam katalog')->color('primary')->url(CatalogItemResource::getUrl()),
            Stat::make('Menu kedai', CatalogItem::where('category', '!=', 'Konsol')->count())
                ->description('Jenis makanan, minuman, dan camilan')->color('primary')->url(CatalogItemResource::getUrl()),
            Stat::make('Akun pemain', User::where('is_admin', false)->count())
                ->description('Pemain yang terdaftar di website')->color('primary'),
            Stat::make('Stok menipis', CatalogItem::where('stock', '<=', 2)->count())
                ->description('Item dengan stok 0 sampai 2')->color('warning'),
        ];
    }
}
