<?php

namespace App\Services;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class AdminAlerts
{
    public function send(string $title, string $body, string $url): void
    {
        $message = Notification::make()->title($title)->body(e($body))
            ->actions([Action::make('view')->label('Lihat')->url($url)]);
        // Persist immediately; production does not rely on a queue worker.
        foreach (User::where('is_admin', true)->get() as $admin) {
            $admin->notifyNow($message->toDatabase());
        }
    }
}
