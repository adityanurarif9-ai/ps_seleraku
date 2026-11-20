# PS Seleraku

project MK 1, MK 2, MK 3, MK 4 dan kik semoga berhasil bismilah

Demo PSAS dengan Laravel, Filament, dan database MySQL Laragon. Katalog perlu login. Admin bisa mengelola konsol, menu, harga, dan stok. Pemain bisa menghitung perkiraan biaya rental.

## Jalankan demo

1. Buka Laragon dan klik **Start All** jika MySQL belum aktif.
2. Klik dua kali **Jalankan Demo.bat**.
3. Buka http://127.0.0.1:8000. Pilih tombol **Pemain** atau **Admin**, lalu klik **Masuk**.

| Akun | Email | Kata sandi demo |
| --- | --- | --- |
| Pemain | pemain@psseleraku.test | Mabar!2026 |
| Admin | admin@psseleraku.test | Seleraku!2026 |

Database yang dipakai adalah `ps_seleraku` di `127.0.0.1:3306`. Untuk instalasi baru, buat database itu, salin `.env.example` menjadi `.env`, gunakan PHP 8.3 atau lebih baru dengan ekstensi intl, zip, dan pdo_mysql, lalu jalankan `composer install`, `php artisan key:generate`, `php artisan migrate --seed`, dan `php artisan filament:assets`.

Untuk demo HTTPS dari HP, setelah web berjalan gunakan `cloudflared tunnel --url http://127.0.0.1:8000`. Quick Tunnel memberikan tautan sementara. Untuk mengaktifkan pemeriksaan Turnstile di login, isi `TURNSTILE_SITE_KEY` dan `TURNSTILE_SECRET_KEY` di `.env`.

## Memperbarui proyek setelah git pull

Gunakan PHP 8.3+ dari Laragon. Simpan pekerjaan lokal terlebih dahulu jika Git meminta, lalu jalankan di folder proyek:

~~~powershell
git pull --ff-only origin main
composer install
php artisan migrate
php artisan optimize:clear
php artisan filament:assets
php artisan serve
~~~

Buka http://127.0.0.1:8000/login dan lakukan hard refresh (Ctrl+F5). Desain login menggunakan aset di public/login.css, public/login.js, dan logo yang sudah disertakan.

File .env hanya berlaku di komputer masing-masing. Konfigurasi database Laragon perlu disesuaikan secara lokal. Untuk login Google, isi GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, dan GOOGLE_REDIRECT_URI sesuai .env.example. Daftarkan callback http://127.0.0.1:8000/auth/google/callback di Google OAuth. Untuk Turnstile, isi TURNSTILE_SITE_KEY dan TURNSTILE_SECRET_KEY dengan pasangan kunci yang sesuai hostname. Simpan secret hanya di .env, jangan di .env.example atau GitHub.
