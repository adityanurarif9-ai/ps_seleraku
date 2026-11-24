<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\ManageCatalogItems;
use App\Models\CatalogItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class CatalogItemResource extends Resource
{
    protected static ?string $model = CatalogItem::class;

    protected static ?string $pluralModelLabel = 'Katalog';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama item')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('category', $get('category'))),
            Select::make('category')->label('Kategori')->options(CatalogItem::CATEGORIES)->required(),
            TextInput::make('price')->label('Harga / tarif per jam')->integer()->minValue(0)->maxValue(100000000)->prefix('Rp')->required(),
            TextInput::make('daily_price')->label('Tarif harian / 24 jam')->integer()->minValue(0)->maxValue(100000000)->prefix('Rp')->helperText('Kosongkan jika konsol tidak tersedia untuk sewa harian.'),
            TextInput::make('stock')->label('Stok / unit tersedia')->integer()->minValue(0)->maxValue(100000)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('price')->label('Harga / tarif')->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.'))->sortable(),
                TextColumn::make('stock')->label('Stok / unit')->sortable(),
            ])
            ->filters([SelectFilter::make('category')->label('Kategori')->options(CatalogItem::CATEGORIES)])
            ->headerActions([CreateAction::make()->label('Tambah item')])
            ->recordActions([EditAction::make()->label('Edit'), DeleteAction::make()->label('Hapus')]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCatalogItems::route('/')];
    }
}
