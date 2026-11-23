<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\ManagePlaySessions;
use App\Models\CatalogItem;
use App\Models\PlaySession;
use App\Services\PlaySessionManager;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlaySessionResource extends Resource
{
    protected static ?string $model = PlaySession::class;

    protected static ?string $pluralModelLabel = 'Sesi main';

    protected static ?string $modelLabel = 'sesi main';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('customer_name')->label('Nama pemain / kelompok')->required()->maxLength(100),
            Select::make('catalog_item_id')->label('Konsol')->options(fn () => CatalogItem::where('category', 'Konsol')->where('stock', '>', 0)->pluck('name', 'id'))->required()->searchable(),
            TextInput::make('minutes')->label('Durasi (menit)')->numeric()->integer()->minValue(1)->maxValue(1440)->default(60)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->poll('5s')->defaultSort('id', 'desc')->columns([
            TextColumn::make('customer_name')->label('Pemain')->searchable(),
            TextColumn::make('catalogItem.name')->label('Konsol'),
            TextColumn::make('status')->label('Status')->formatStateUsing(fn (PlaySession $record) => match ($record->displayStatus()) {
                'running' => 'Berjalan','paused' => 'Dijeda','expired' => 'Waktu habis',default => 'Selesai'
            })->badge()->color(fn (PlaySession $record) => match ($record->displayStatus()) {
                'running' => 'success','paused' => 'warning','expired' => 'danger',default => 'gray'
            }),
            TextColumn::make('remaining_seconds')->label('Sisa waktu')->state(fn (PlaySession $record) => $record->countdown())->view('filament.tables.session-countdown'),
            TextColumn::make('total_seconds')->label('Durasi total')->formatStateUsing(fn ($state) => ($state / 60).' menit'),
            TextColumn::make('hourly_rate')->label('Estimasi biaya durasi')->state(fn (PlaySession $record) => 'Rp'.number_format($record->estimatedPrice(), 0, ',', '.'))->tooltip('Berdasarkan durasi yang dipesan dan tarif saat sesi dimulai; bukan status pembayaran.'),
            TextColumn::make('created_at')->label('Mulai')->dateTime('d M, H:i')->sortable(),
        ])->filters([SelectFilter::make('status')->label('Status tersimpan')->options(['running' => 'Berjalan / waktu habis', 'paused' => 'Dijeda', 'finished' => 'Selesai'])])
            ->headerActions([CreateAction::make()->label('Mulai sesi')->using(fn (array $data) => app(PlaySessionManager::class)->start($data))])
            ->recordActions([
                Action::make('pause')->label('Jeda')->color('warning')->visible(fn (PlaySession $record) => $record->displayStatus() === 'running')->action(fn (PlaySession $record) => app(PlaySessionManager::class)->change($record, 'pause')),
                Action::make('resume')->label('Lanjutkan')->color('success')->visible(fn (PlaySession $record) => $record->status === 'paused')->action(fn (PlaySession $record) => app(PlaySessionManager::class)->change($record, 'resume')),
                Action::make('extend')->label('Tambah waktu')->visible(fn (PlaySession $record) => in_array($record->displayStatus(), ['running', 'paused']))->schema([TextInput::make('minutes')->label('Tambahan menit')->integer()->required()->minValue(1)->maxValue(1440)->default(30)])->action(fn (PlaySession $record, array $data) => app(PlaySessionManager::class)->change($record, 'extend', (int) $data['minutes'])),
                Action::make('finish')->label('Akhiri')->color('danger')->requiresConfirmation()->modalHeading('Akhiri sesi main?')->modalDescription('Timer berhenti dan unit dapat digunakan untuk sesi lain. Riwayat tetap tersimpan.')->visible(fn (PlaySession $record) => $record->status !== 'finished')->action(fn (PlaySession $record) => app(PlaySessionManager::class)->change($record, 'finish')),
            ])->emptyStateHeading('Belum ada sesi main')->emptyStateDescription('Mulai sesi untuk mengatur waktu main konsol. Jeda dan lanjutkan tanpa kehilangan sisa waktu.');
    }

    public static function getPages(): array
    {
        return ['index' => ManagePlaySessions::route('/')];
    }
}
