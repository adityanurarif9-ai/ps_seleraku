<?php

namespace App\Filament\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;

class AdminLogin extends Login
{
    public function mount(): void
    {
        if (Filament::auth()->user()?->is_admin) {
            $this->redirect(Filament::getUrl());

            return;
        }

        $this->form->fill();
    }

    public function getHeading(): string
    {
        return 'Masuk sebagai admin';
    }

    public function getTitle(): string
    {
        return 'Login Admin';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola reservasi, waktu main, konsol, dan menu kedai.';
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()->label('Email admin');
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()->label('Kata sandi');
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()->label('Ingat saya');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()->label('Masuk ke dashboard');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            Action::make('playerLogin')->label('Kembali ke login pemain')->url(route('login'))->color('gray')->link(),
        ];
    }
}
