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


## Admin dan waktu main

Buka /admin/login untuk masuk sebagai pengelola. Dashboard dan pengelolaan katalog hanya tersedia untuk akun dengan is_admin=true.
Menu Sesi main (/admin/play-sessions) menyediakan mulai sesi, jeda, lanjutkan, tambah menit, akhiri, dan riwayat.
Sisa waktu dihitung dari waktu server dan disimpan di database. Refresh atau browser tertutup tidak mengulang timer.
Sesi dijeda tetap memakai kapasitas konsol. Sesi yang habis waktunya otomatis tidak dihitung sebagai unit terpakai.
Timer hanya mengelola pencatatan waktu; tidak mematikan perangkat PlayStation secara fisik.
Estimasi biaya mengikuti tarif saat sesi dibuat dan total durasi pesanan, bukan bukti pembayaran.

## Hosting produksi

Target: https://ps.flexboxstudio.site, proyek Vercel ps-seleraku, database TiDB melalui TLS.
Dockerfile.vercel menggunakan FrankenPHP; web root /app/public. Session dan cache menggunakan database eksternal.
File .env.production.local hanya untuk konfigurasi privat operator dan tidak boleh di-commit atau masuk image.
Jalankan migration sebelum deployment memakai kredensial database target. Jangan menjalankan migrate:fresh pada produksi.
Daftarkan https://ps.flexboxstudio.site/auth/google/callback pada OAuth Client Google.
Daftarkan hostname ps.flexboxstudio.site pada Turnstile. Kredensial demo lokal tidak otomatis dibuat di produksi.


## Reservasi pelanggan

Pelanggan membuka /reservasi/baru, memilih konsol, cara sewa, durasi dan jadwal WIB. Formulir menampilkan tarif server serta kapasitas yang tersedia untuk seluruh rentang waktu.
Permintaan tersimpan sebagai menunggu konfirmasi. Permintaan belum mengunci stok sampai admin mengonfirmasi di menu Reservasi pengguna.
Admin memulai sesi paling awal 15 menit sebelum jadwal. Sesi ini tertaut ke pemilik reservasi dan tampil di /rental-saya.
Permintaan tambah waktu tersedia saat sesi aktif. Admin menyetujui atau menolak melalui menu Permintaan tambah waktu. Persetujuan memeriksa ulang benturan reservasi.
Riwayat dan rincian tarif tetap tersimpan. Biaya merupakan rincian durasi, bukan status pembayaran. Tidak ada integrasi pembayaran otomatis.
Sewa harian mengikuti daily_price di katalog admin. Tarif tambahan menit dihitung proporsional dari tarif yang dikunci saat reservasi dibuat.
Sesi yang dijeda tetap memakai kapasitas; admin perlu menyelesaikan atau melanjutkannya sebelum memulai pemakaian unit lain yang bentrok.
Pengujian memakai SQLite in-memory, tidak menghapus data Laragon atau produksi.


## Pendaftaran dan Google

/register menyediakan pendaftaran pemain dengan nama, email unik, kata sandi minimal 8 karakter dan konfirmasinya. Cloudflare diperiksa pada server sebelum akun dibuat. Akun manual tidak otomatis ditandai sebagai email terverifikasi.
Login Google memakai state satu kali dengan masa berlaku 10 menit, maksimal 5 tab, dan tetap menjalankan validasi Socialite. Email yang sudah memiliki akun manual memerlukan konfirmasi kata sandi melalui /auth/google/link sebelum Google dihubungkan; tidak ada penggabungan otomatis berdasarkan email. Permintaan konfirmasi berlaku 5 menit.
Koneksi Google memiliki batas waktu; kegagalan dibedakan menjadi sesi, koneksi, dan penolakan provider. Log hanya mencatat jenis/status error, tanpa token, kode OAuth atau client secret.


## Katalog admin dan website

Kartu konsol, tarif per jam/harian, pilihan sewa di beranda, menu kedai, gambar, deskripsi dan game memakai catalog_items. Ubah melalui /admin/catalog-items; muat ulang website untuk melihat perubahan. Menonaktifkan Tampilkan di website juga menutup reservasi baru untuk item tersebut.
Stok konsol adalah kapasitas total. Label tersedia menghitung sesi berjalan/dijeda dan reservasi terkonfirmasi untuk 1 jam (lounge) atau 24 jam (harian) ke depan; formulir memeriksa rentang jadwal pilihan pengguna. Katalog tidak lagi menampilkan unit VIP, maintenance atau jam selesai contoh.
Migrasi mempertahankan harga dan stok lama, menambahkan metadata konsol, dan memasukkan menu yang sebelumnya hanya ada di halaman. Menu baru memiliki stok awal nol; admin perlu mengisi stok nyata. Pengelolaan katalog ini tidak mengubah pencatatan pesanan F&B lokal menjadi pemrosesan pesanan server.


## Aplikasi web dan mobile

Halaman /login dan /register menyediakan tombol Pasang. Android memakai beforeinstallprompt jika browser menawarkannya; jika belum tersedia, tombol membuka panduan menu browser. iPhone/iPad memakai panduan Safari → Bagikan → Tambah ke Layar Utama. Hasilnya aplikasi web terpasang (PWA), bukan berkas APK/IPA atau publikasi Play Store/App Store.
Manifest dan metadata disertakan pada seluruh halaman pelanggan. Ikon persegi tersedia untuk Android, maskable dan Apple. Tampilan mobile memakai safe area, kolom minimal 16px untuk mencegah zoom fokus iOS, serta target sentuh lebih besar.
Service worker hanya menyimpan halaman offline dan ikon publik. Tidak menyimpan halaman akun, respons API, token, callback Google, atau transaksi reservasi. Saat offline, pengguna diminta terhubung kembali; reservasi tidak dikirim atau diantrekan secara offline.
Pengujian tambahan: node --test tests/Browser/pwa.test.cjs. Pengujian instalasi pada perangkat Android/iPhone fisik tetap perlu dilakukan di domain HTTPS produksi.
