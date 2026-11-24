<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\ManageRentalExtensionRequests;
use App\Models\RentalExtensionRequest;
use App\Services\RentalReservationManager;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RentalExtensionRequestResource extends Resource
{
    protected static ?string $model = RentalExtensionRequest::class;

    protected static ?string $pluralModelLabel = 'Permintaan tambah waktu';

    public static function table(Table $t): Table
    {
        return $t->query(RentalExtensionRequest::query()->with(['rentalReservation.user', 'rentalReservation.catalogItem']))->poll('10s')->defaultSort('id', 'desc')->columns([
            TextColumn::make('rental_reservation_id')->label('Reservasi')->formatStateUsing(fn ($state) => 'PS-'.str_pad((string) $state, 6, '0', STR_PAD_LEFT)),
            TextColumn::make('rentalReservation.user.name')->label('Pelanggan'),
            TextColumn::make('rentalReservation.catalogItem.name')->label('Konsol'),
            TextColumn::make('minutes')->label('Tambahan menit'),
            TextColumn::make('status')->label('Status')->formatStateUsing(fn ($state) => match ($state) {
                'pending' => 'Menunggu','approved' => 'Disetujui',default => 'Ditolak'
            })->badge(),
            TextColumn::make('created_at')->label('Diajukan WIB')->dateTime('d M, H:i')->timezone('Asia/Jakarta'),
        ])->filters([SelectFilter::make('status')->options(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'])->default('pending')])->recordActions([
            Action::make('approve')->label('Setujui')->color('success')->requiresConfirmation()->visible(fn (RentalExtensionRequest $record) => $record->status === 'pending')->action(fn (RentalExtensionRequest $record) => app(RentalReservationManager::class)->decideExtension($record, true)),
            Action::make('reject')->label('Tolak')->color('danger')->requiresConfirmation()->visible(fn (RentalExtensionRequest $record) => $record->status === 'pending')->action(fn (RentalExtensionRequest $record) => app(RentalReservationManager::class)->decideExtension($record, false)),
        ])->emptyStateHeading('Tidak ada permintaan tambahan waktu');
    }

    public static function getPages(): array
    {
        return ['index' => ManageRentalExtensionRequests::route('/')];
    }
}
