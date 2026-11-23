<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CatalogItemResource;
use App\Models\CatalogItem;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LowStockItems extends TableWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Perlu diperiksa')
            ->description('Stok katalog, bukan status pemakaian atau reservasi langsung.')
            ->query(CatalogItem::query()->where('stock', '<=', 2))
            ->defaultSort('stock')
            ->columns([
                TextColumn::make('name')->label('Nama item')->searchable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('stock')->label('Stok')->badge()
                    ->color(fn ($state) => $state === 0 ? 'danger' : 'warning')->sortable(),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->headerActions([Action::make('manage')->label('Kelola stok')->url(CatalogItemResource::getUrl())])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Stok dalam kondisi cukup')
            ->emptyStateDescription('Tidak ada item dengan stok 2 atau kurang.');
    }
}
