<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\ManageRentalReservations;
use App\Models\RentalReservation;
use App\Services\PlaySessionManager;
use App\Services\RentalReservationManager;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class RentalReservationResource extends Resource
{
    protected static ?string $model = RentalReservation::class;

    protected static ?string $pluralModelLabel = 'Reservasi pengguna';

    public static function table(Table $t): Table
    {
        return $t->query(RentalReservation::query()->with(['user', 'catalogItem', 'playSession']))->poll('10s')->defaultSort('id', 'desc')->columns([
            TextColumn::make('id')->label('Kode')->formatStateUsing(fn ($state) => 'PS-'.str_pad((string) $state, 6, '0', STR_PAD_LEFT))->sortable(),
            TextColumn::make('user.name')->label('Pelanggan')->searchable(),
            TextColumn::make('contact_name')->label('Nama penyewa')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('contact_phone')->label('WhatsApp')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('catalogItem.name')->label('Konsol'),
            TextColumn::make('unit_number')->label('Unit')->formatStateUsing(fn ($state) => str_pad((string) $state, 2, '0', STR_PAD_LEFT))->placeholder('Otomatis'),
            TextColumn::make('mode')->label('Layanan')->formatStateUsing(fn ($state) => $state === 'daily' ? 'Bawa pulang' : 'Main di tempat'),
            TextColumn::make('starts_at')->label('Jadwal WIB')->dateTime('d M Y, H:i')->timezone('Asia/Jakarta')->sortable(),
            TextColumn::make('duration_minutes')->label('Menit'),
            TextColumn::make('testPayment.status')->label('Pembayaran uji')->formatStateUsing(fn ($state) => match ($state) {
                'simulated_paid' => 'Simulasi berhasil (bukan lunas)','failed' => 'Simulasi gagal',default => 'Belum dibayar (uji)'
            })->placeholder('Belum dibayar (uji)')->toggleable(),
            TextColumn::make('total_price')->label('Biaya')->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.')),
            TextColumn::make('status')->label('Status')->formatStateUsing(fn (RentalReservation $record) => $record->statusLabel())->badge()->color(fn ($state) => match ($state) {
                'pending' => 'warning','confirmed' => 'info','playing' => 'success','rejected','cancelled' => 'danger',default => 'gray'
            }),
        ])->filters([SelectFilter::make('status')->label('Status')->options(RentalReservation::LABELS)])
            ->recordActions([
                Action::make('receipt')->label('Struk PDF')->url(fn (RentalReservation $record) => route('receipts.rental', $record))->extraAttributes(['download' => '']),
                Action::make('detail')->label('Detail')->url(fn (RentalReservation $record) => route('reservations.show', $record)),
                Action::make('confirm')->label('Konfirmasi')->color('success')->disabled(fn (RentalReservation $record) => ! $record->paymentReady())->tooltip('Pembayaran demo harus selesai dahulu')->requiresConfirmation()->visible(fn (RentalReservation $record) => $record->status === 'pending')->action(fn (RentalReservation $record) => self::perform($record, 'confirm')),
                Action::make('start')->label('Mulai sesi')->modalDescription(fn (RentalReservation $record) => 'Jadwal: '.$record->starts_at->timezone('Asia/Jakarta')->format('d M Y H:i').' WIB. Paling awal 15 menit sebelum jadwal.')->visible(fn (RentalReservation $record) => $record->status === 'confirmed')->requiresConfirmation()->action(fn (RentalReservation $record) => self::perform($record, 'start')),
                Action::make('startNow')->label('Mulai sekarang')->color('warning')->visible(fn (RentalReservation $record) => $record->status === 'confirmed')->requiresConfirmation()->modalDescription('Waktu mulai diubah menjadi sekarang dan durasi tetap utuh. Sistem memeriksa ketersediaan unit sebelum memulai.')->action(fn (RentalReservation $record) => self::perform($record, 'start_now')),
                Action::make('reject')->label('Tolak')->color('danger')->visible(fn (RentalReservation $record) => in_array($record->status, ['pending', 'confirmed']))->schema([Textarea::make('reason')->label('Alasan')->required()->maxLength(500)])->action(fn (RentalReservation $record, array $data) => self::perform($record, 'reject', $data['reason'])),
                Action::make('finish')->label('Akhiri sesi')->color('danger')->requiresConfirmation()->visible(fn (RentalReservation $record) => $record->status === 'playing')->action(fn (RentalReservation $record) => app(PlaySessionManager::class)->change($record->playSession, 'finish')),
            ])->emptyStateHeading('Belum ada permintaan reservasi')->emptyStateDescription('Reservasi yang dikirim pengguna akan muncul di sini.');
    }

    public static function perform(RentalReservation $record, string $action, ?string $note = null): void
    {
        try {
            app(RentalReservationManager::class)->adminAction($record, $action, $note);
            Notification::make()->title(in_array($action, ['start', 'start_now']) ? 'Sesi berhasil dimulai' : 'Reservasi diperbarui')->success()->send();
        } catch (ValidationException $e) {
            Notification::make()->title('Reservasi belum dapat diproses')->body(collect($e->errors())->flatten()->first())->danger()->persistent()->send();
        }
    }

    public static function getPages(): array
    {
        return ['index' => ManageRentalReservations::route('/')];
    }
}
