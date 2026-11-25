<?php

namespace App\Services;

use App\Models\CatalogItem;
use Illuminate\Support\Str;

class SeloAssistant
{
    public function answer(string $message): array
    {
        $text = Str::lower(Str::ascii($message));
        $has = fn (string $pattern) => preg_match($pattern, $text) === 1;
        $reply = '';
        $links = [];
        if ($has('/\b(password|sandi|login|masuk|daftar|akun|google)\b/')) {
            $reply = 'Untuk akun baru, pilih Daftar di sini pada halaman login. Kamu juga bisa masuk dengan Google. Jika lupa kata sandi akun lokal, hubungi pengelola PS Seleraku. Jangan kirim kata sandi atau kode verifikasi lewat chat.';
            $links = [['label' => 'Halaman login', 'url' => route('login')], ['label' => 'Buat akun', 'url' => route('register')]];
        } elseif ($has('/\b(tambah|perpanjang|extend)\b/')) {
            $reply = 'Buka Rental Saya, pilih pesananmu, lalu ajukan tambahan waktu. Admin akan memeriksa jadwal unit dan menyetujui permintaan jika masih tersedia. Waktu bertambah setelah disetujui.';
            $links = $this->rentals();
        } elseif ($has('/\b(timer|waktu|sisa|pause|jeda|berhenti|status|riwayat)\b/')) {
            $reply = 'Sisa waktu, status pesanan, dan riwayat rental ada di Rental Saya setelah kamu masuk. Timer dimulai oleh admin saat sesi bermain dimulai. Penjedaan dan penghentian sesi dilakukan admin. Aku tidak bisa mengubah timer atau membuka data pesanan melalui chat ini.';
            $links = $this->rentals();
        } elseif ($has('/\b(batal|cancel)\b/')) {
            $reply = 'Buka Rental Saya lalu pilih detail pesanan. Tombol batalkan tersedia sebelum sesi bermain dimulai. Untuk sesi yang sudah dimulai, hubungi pengelola.';
            $links = $this->rentals();
        } elseif ($has('/\b(pesan|reservasi|booking|jadwal|sewa|rental)\b/') && ! $has('/\b(harga|tarif|biaya|berapa|harian|hari)\b/')) {
            $reply = 'Cara reservasi:\n1. Masuk atau buat akun.\n2. Pilih konsol, tanggal, jam, dan durasi.\n3. Periksa perkiraan biaya lalu kirim reservasi.\n4. Pantau konfirmasi admin di Rental Saya.\nKetersediaan diperiksa kembali ketika reservasi dikonfirmasi.';
            $links = [['label' => 'Buat reservasi', 'url' => route('reservations.create')], ...$this->rentals()];
        } elseif ($has('/\b(makan|makanan|minum|minuman|menu|camilan|snack)\b/')) {
            $items = CatalogItem::where('is_visible', true)->where('category', '!=', 'Konsol')->orderBy('id')->limit(12)->get();
            $reply = $items->isEmpty() ? 'Belum ada menu yang ditampilkan pengelola.' : "Menu dari katalog saat ini:\n".$items->map(fn ($i) => $i->name.' — '.$this->money($i->price).($i->stock > 0 ? '' : ' (stok habis)'))->implode("\n");
            $reply .= '\nLihat Menu & Kedai untuk rinciannya. Pembelian dan ketersediaan akhir perlu dikonfirmasi ke pengelola.';
            $links = [['label' => 'Menu & Kedai', 'url' => route('menu-fnb')]];
        } elseif ($has('/\b(harga|tarif|biaya|berapa|konsol|playstation|ps\s*[345]|nintendo|switch|harian|hari|tersedia|kosong|stok|game|permainan)\b/')) {
            $items = CatalogItem::where('is_visible', true)->where('category', 'Konsol')->orderBy('id')->get();
            foreach (['ps5' => '/\b(ps\s*5|playstation\s*5)\b/', 'ps4' => '/\b(ps\s*4|playstation\s*4)\b/', 'ps3' => '/\b(ps\s*3|playstation\s*3)\b/', 'nintendo' => '/\b(nintendo|switch)\b/'] as $platform => $pattern) {
                if ($has($pattern)) {
                    $items = $items->filter(fn ($i) => $i->platformKey() === $platform);
                    break;
                }
            }
            if ($items->isEmpty()) {
                $reply = 'Pilihan konsol itu belum ditampilkan di katalog. Coba lihat pilihan yang tersedia di formulir reservasi.';
            } elseif ($has('/\b(tersedia|kosong|stok)\b/')) {
                $from = now();
                $to = $from->copy()->addHour();
                $reply = "Ketersediaan untuk 1 jam mulai sekarang:\n".$items->map(fn ($i) => $i->name.' — '.app(RentalAvailability::class)->available($i, $from, $to).' unit')->implode("\n")."\nUntuk tanggal atau durasi lain, cek lewat formulir reservasi. Unit tetap perlu dikonfirmasi admin.";
            } elseif ($has('/\b(game|permainan)\b/')) {
                $reply = $items->map(fn ($i) => $i->name.': '.(count($i->games ?? []) ? implode(', ', $i->games) : 'Daftar game belum diisi pengelola.'))->implode("\n");
            } else {
                $daily = $has('/\b(harian|hari|24)\b/');
                $reply = ($daily ? "Tarif sewa 24 jam:\n" : "Tarif main per jam:\n").$items->map(fn ($i) => $i->name.' — '.($daily ? ($i->daily_price !== null ? $this->money($i->daily_price).'/24 jam' : 'Tarif harian belum tersedia') : $this->money($i->price).'/jam'))->implode("\n")."\nTarif mengikuti katalog admin. Pilih jadwal dan durasi untuk melihat perkiraan total.";
            }
            $links = [['label' => 'Cek jadwal & biaya', 'url' => route('reservations.create')]];
        } elseif ($has('/\b(install|instal|aplikasi|iphone|android|pasang)\b/')) {
            $reply = 'Di halaman login, ketuk Pasang aplikasi. Di Android gunakan Chrome dan ikuti petunjuk instalasi. Di iPhone buka Safari, ketuk Bagikan, lalu Tambah ke Layar Utama. Website dapat dipasang sebagai aplikasi web, tanpa file APK.';
            $links = [['label' => 'Buka halaman login', 'url' => route('login')]];
        } elseif ($has('/\b(alamat|lokasi|buka|tutup|bayar|pembayaran|refund|telepon|whatsapp|kontak)\b/')) {
            $reply = 'Untuk memastikan lokasi, jam buka, metode pembayaran, atau pengembalian dana, hubungi pengelola PS Seleraku. Aku belum memiliki informasi terverifikasi tentang hal itu.';
        } else {
            $reply = 'Hai, aku Selo, asisten info PS Seleraku. Aku bisa bantu cek tarif konsol, pilihan game, cara reservasi, sisa waktu, dan cara memasang aplikasi. Coba tanya “Berapa tarif PS5?” atau pilih pertanyaan di bawah.';
        }

        return ['reply' => str_replace('\\n', "\n", $reply), 'links' => $links];
    }

    private function money($value): string
    {
        return 'Rp'.number_format($value, 0, ',', '.');
    }

    private function rentals(): array
    {
        return [['label' => 'Buka Rental Saya', 'url' => route('reservations.index')]];
    }
}
