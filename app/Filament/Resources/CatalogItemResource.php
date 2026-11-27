<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\ManageCatalogItems;
use App\Models\CatalogItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
            Select::make('category')->label('Kategori')->options(CatalogItem::CATEGORIES)->required()->live(),
            TextInput::make('price')->label(fn (Get $get) => $get('category') === 'Konsol' ? 'Tarif per jam' : 'Harga per item')->integer()->minValue(0)->maxValue(100000000)->prefix('Rp')->required(),
            TextInput::make('daily_price')->label('Tarif harian / 24 jam')->integer()->minValue(0)->maxValue(100000000)->prefix('Rp')->helperText('Kosongkan jika konsol tidak tersedia untuk sewa harian.')->visible(fn (Get $get) => $get('category') === 'Konsol'),
            TextInput::make('stock')->label('Stok / total unit')->integer()->minValue(0)->maxValue(100000)->required()->helperText('Untuk konsol: jumlah perangkat bernomor 01, 02, dan seterusnya. Jangan turunkan jumlah jika unit terakhir masih digunakan.'),
            Toggle::make('is_visible')->label('Tampilkan di website')->default(true),
            Select::make('platform')->label('Platform konsol')->options(['ps5' => 'PlayStation 5', 'ps4' => 'PlayStation 4', 'ps3' => 'PlayStation 3', 'nintendo' => 'Nintendo', 'other' => 'Lainnya'])->visible(fn (Get $get) => $get('category') === 'Konsol')->required(fn (Get $get) => $get('category') === 'Konsol'),
            Textarea::make('description')->label('Deskripsi / spesifikasi')->rows(3)->maxLength(2000),
            TextInput::make('image_path')->label('Gambar')->maxLength(500)->regex('~^(?:https://[^\\s]+|images/[A-Za-z0-9/_.-]+)$~')->helperText('Gunakan URL HTTPS atau gambar yang sudah ada, misalnya images/ps5-ultra.png.'),
            TagsInput::make('games')->label('Daftar game')->visible(fn (Get $get) => $get('category') === 'Konsol')->rules(['array', 'max:30'])->nestedRecursiveRules(['string', 'max:100']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('price')->label('Harga / tarif')->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.'))->sortable(),
                TextColumn::make('stock')->label('Stok / total unit')->sortable(),
                TextColumn::make('daily_price')->label('Tarif harian')->formatStateUsing(fn ($state) => $state === null ? '—' : 'Rp'.number_format($state, 0, ',', '.')),
                TextColumn::make('is_visible')->label('Website')->formatStateUsing(fn ($state) => $state ? 'Tampil' : 'Disembunyikan')->badge(),
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
