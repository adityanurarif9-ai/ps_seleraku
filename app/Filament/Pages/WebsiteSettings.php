<?php

namespace App\Filament\Pages;

use App\Models\SiteContent;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WebsiteSettings extends Page
{
    protected static ?string $title = 'Isi Website';

    protected static ?string $navigationLabel = 'Isi Website';

    protected static ?string $slug = 'isi-website';

    protected string $view = 'filament.pages.website-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
        $this->form->fill(SiteContent::values());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Beranda')->description('Teks utama yang dilihat pelanggan pada halaman beranda.')->schema([
                TextInput::make('hero_title')->label('Judul utama')->required()->maxLength(120),
                Textarea::make('hero_description')->label('Deskripsi beranda')->required()->rows(3)->maxLength(600),
            ]),
            Section::make('Tentang PS Seleraku')->schema([
                TextInput::make('about_title')->label('Judul tentang')->required()->maxLength(120),
                Textarea::make('about_body')->label('Cerita / deskripsi usaha')->required()->rows(5)->maxLength(3000),
            ]),
            Section::make('Kontak dan operasional')->description('Isi data usaha yang benar. Kolom kosong ditampilkan sebagai belum tersedia; nomor contoh tidak digunakan.')->schema([
                Textarea::make('address')->label('Alamat usaha')->rows(2)->maxLength(500),
                TextInput::make('whatsapp')->label('WhatsApp')->placeholder('628…')->regex('/^62[0-9]{8,13}$/')->maxLength(15)->helperText('Format kode negara 62, tanpa spasi atau tanda +.'),
                TextInput::make('contact_email')->label('Email kontak')->email()->maxLength(255),
                TextInput::make('opening_hours')->label('Jam operasional')->maxLength(200),
            ]),
            Section::make('English content')->description('Opsional. Isi terjemahan Inggris untuk teks usaha yang kamu ubah. Kolom kosong memakai teks asli, kecuali teks bawaan yang sudah diterjemahkan.')->schema([
                TextInput::make('hero_title_en')->label('English home title')->maxLength(120),
                Textarea::make('hero_description_en')->label('English home description')->maxLength(600),
                TextInput::make('about_title_en')->label('English about title')->maxLength(120),
                Textarea::make('about_body_en')->label('English business description')->maxLength(3000),
                TextInput::make('opening_hours_en')->label('English opening hours')->maxLength(200),
                Textarea::make('rental_terms_en')->label('English rental terms')->maxLength(3000),
            ]),
            Section::make('Ketentuan sewa')->schema([Textarea::make('rental_terms')->label('Ketentuan pelanggan')->required()->rows(5)->maxLength(3000)]),
        ])->statePath('data');
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);
        $data = $this->form->getState();
        SiteContent::updateOrCreate(['id' => 1], ['data' => array_intersect_key($data, SiteContent::defaults())]);
        Notification::make()->title('Isi website berhasil diperbarui')->success()->send();
    }
}
