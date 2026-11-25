<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $fillable = ['data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public static function defaults(): array
    {
        return [
            'hero_title' => 'Selamat datang di PS Seleraku.',
            'hero_description' => 'Pilih konsol favoritmu, ajak teman, dan nikmati waktu main bersama. Pesan jadwalmu sebelum datang dan pantau rental dari akunmu.',
            'about_title' => 'Tempat bertemunya cerita dan permainan.',
            'about_body' => 'PS Seleraku membantu kamu memilih konsol dan merencanakan waktu bermain. Lihat pilihan game, cek tarif, lalu kirim reservasi. Pengelola akan mengonfirmasi jadwal dan menyiapkan sesi bermainmu.',
            'address' => '', 'whatsapp' => '', 'contact_email' => '', 'opening_hours' => '',
            'rental_terms' => 'Reservasi menunggu konfirmasi pengelola. Datang sesuai jadwal yang telah dikonfirmasi. Penambahan waktu mengikuti ketersediaan unit dan persetujuan admin.',
        ];
    }

    public static function values(): array
    {
        return array_replace(static::defaults(), static::find(1)?->data ?? []);
    }

    public static function whatsappUrl(?string $number): ?string
    {
        $digits = preg_replace('/\D/', '', $number ?? '');

        return $digits ? 'https://wa.me/'.$digits : null;
    }
}
