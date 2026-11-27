<?php

namespace App\Filament\Resources;

use App\Models\FoodOrder;
use App\Services\FoodOrderManager;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class FoodOrderResource extends Resource
{
    protected static ?string $model = FoodOrder::class;

    protected static ?string $pluralModelLabel = 'Pesanan kedai';

    public static function getNavigationBadge(): ?string
    {
        $n = FoodOrder::where('status', 'pending')->count();

        return $n ? (string) $n : null;
    }

    public static function table(Table $t): Table
    {
        $actions = [];
        foreach (['preparing' => ['Siapkan', ['pending']], 'ready' => ['Siap diambil', ['preparing']], 'completed' => ['Selesai', ['ready']], 'rejected' => ['Tolak', ['pending', 'preparing']]] as $status => [$label,$from]) {
            $actions[] = Action::make($status)->label($label)->color($status === 'rejected' ? 'danger' : 'primary')->requiresConfirmation()
                ->visible(fn (FoodOrder $record) => in_array($record->status, $from))->action(function (FoodOrder $record) use ($status) {
                    try {
                        app(FoodOrderManager::class)->change($record, $status);
                        Notification::make()->title('Status pesanan diperbarui')->success()->send();
                    } catch (ValidationException $e) {
                        Notification::make()->title('Pesanan belum dapat diproses')->body(collect($e->errors())->flatten()->first())->danger()->persistent()->send();
                    }
                });
        }

        return $t->query(FoodOrder::query()->with(['user', 'items']))->poll('10s')->defaultSort('id', 'desc')->columns([
            TextColumn::make('id')->label('Kode')->formatStateUsing(fn (FoodOrder $record) => $record->reference()),
            TextColumn::make('user.name')->label('Pelanggan')->searchable(),
            TextColumn::make('items.name')->label('Menu')->state(fn (FoodOrder $record) => $record->items->map(fn ($i) => $i->quantity.' × '.$i->name)->all())->listWithLineBreaks()->wrap(),
            TextColumn::make('notes')->label('Catatan')->wrap(),
            TextColumn::make('testPayment.status')->label('Pembayaran uji')->formatStateUsing(fn ($state) => match ($state) {
                'simulated_paid' => 'Simulasi berhasil (bukan lunas)','failed' => 'Simulasi gagal',default => 'Belum dibayar (uji)'
            })->placeholder('Belum dibayar (uji)')->toggleable(),
            TextColumn::make('total_price')->label('Total')->formatStateUsing(fn ($state) => 'Rp'.number_format($state, 0, ',', '.')),
            TextColumn::make('status')->label('Status')->formatStateUsing(fn (FoodOrder $record) => $record->statusLabel())->badge(),
            TextColumn::make('created_at')->label('Waktu WIB')->dateTime('d M H:i')->timezone('Asia/Jakarta'),
        ])->filters([SelectFilter::make('status')->options(FoodOrder::LABELS)])->recordActions($actions)->emptyStateHeading('Belum ada pesanan kedai');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageFoodOrders::route('/')];
    }
}
