<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\SiteContent;
use Illuminate\Support\Str;

class SeloAssistant
{
    public function answer(string $message): array
    {
        if (app()->getLocale() === 'en') return $this->answerEnglish($message);

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
        } elseif ($has('/\b(alamat|lokasi|buka|tutup|operasional|bayar|pembayaran|refund|telepon|whatsapp|kontak|email)\b/')) {
            $site = SiteContent::values();
            $parts = array_filter([
                $site['address'] ? 'Alamat: '.$site['address'] : null,
                $site['opening_hours'] ? 'Jam operasional: '.$site['opening_hours'] : null,
                $site['whatsapp'] ? 'WhatsApp: '.$site['whatsapp'] : null,
                $site['contact_email'] ? 'Email: '.$site['contact_email'] : null,
            ]);
            $reply = $parts ? implode("\n", $parts)."\nUntuk pembayaran atau pengembalian dana, konfirmasikan langsung ke pengelola." : 'Pengelola belum menambahkan informasi kontak dan jam buka. Aku belum memiliki informasi terverifikasi tentang hal itu.';
            $links = [['label' => 'Kontak pengelola', 'url' => route('kontak')]];
        } else {
            $reply = 'Hai, aku Selo, asisten info PS Seleraku. Aku bisa bantu cek tarif konsol, pilihan game, cara reservasi, sisa waktu, dan cara memasang aplikasi. Coba tanya “Berapa tarif PS5?” atau pilih pertanyaan di bawah.';
        }

        return ['reply' => str_replace('\\n', "\n", $reply), 'links' => $links];
    }

    private function answerEnglish(string $message): array
    {
        $text = Str::lower(Str::ascii($message));
        $has = fn ($pattern) => preg_match($pattern, $text) === 1;
        $links = [['label'=>'My Rentals', 'url'=>route('reservations.index')]];
        if ($has('/\b(password|login|account|google|sandi|akun|daftar)\b/')) {
            $reply = 'Create an account on the sign-up page or sign in with Google. For help accessing a local account, contact the team. Never share passwords or verification codes in chat.';
            $links = [['label'=>'Sign in', 'url'=>route('login')], ['label'=>'Create account', 'url'=>route('register')]];
        } elseif ($has('/\b(timer|time|remaining|pause|status|history|extend|extension|cancel|waktu|sisa|tambah|batal)\b/')) {
            $reply = 'Open My Rentals and choose your booking to see its status and remaining time. Our team starts and pauses the timer. You can request an extension; extra time is added only after approval and an availability check. You can cancel before your session starts. I cannot change timers or show private booking data in chat.';
        } elseif ($has('/\b(price|cost|rate|tarif|harga|biaya|console|konsol|game|ps[345]|playstation|nintendo|switch|available|availability|stok)\b/')) {
            $items = CatalogItem::where('is_visible', true)->where('category', 'Konsol')->orderBy('id')->get();
            foreach (['ps5'=>'/\b(ps\s*5|playstation\s*5)\b/','ps4'=>'/\b(ps\s*4|playstation\s*4)\b/','ps3'=>'/\b(ps\s*3|playstation\s*3)\b/','nintendo'=>'/\b(nintendo|switch)\b/'] as $platform=>$pattern) {
                if ($has($pattern)) { $items=$items->filter(fn ($i)=>$i->platformKey()===$platform); break; }
            }
            $daily=$has('/\b(day|daily|harian|hari|24)\b/');
            if ($has('/\b(available|availability|stok|tersedia)\b/')) {
                $reply="Availability for the next hour:\n".$items->map(fn ($i)=>$i->name.' — '.app(RentalAvailability::class)->available($i, now(), now()->addHour()).' available')->implode("\n")."\nCheck the booking form for other dates and durations. Confirmation is still required.";
            } elseif ($has('/\b(game|permainan)\b/')) {
                $reply=$items->map(fn ($i)=>$i->name.': '.(count($i->games ?? [])?implode(', ', $i->games):'No games have been listed yet.'))->implode("\n");
            } else {
                $reply=($daily?"Daily rental prices:\n":"Hourly prices:\n").$items->map(fn ($i)=>$i->name.' — '.($daily?($i->daily_price!==null?$this->money($i->daily_price).'/24 hours':'Daily rental unavailable'):$this->money($i->price).'/hour'))->implode("\n");
            }
            if ($items->isEmpty()) $reply='No matching consoles are currently listed. Please check the console catalog.';
            $links=[['label'=>'Check availability and cost','url'=>route('reservations.create')]];
        } elseif ($has('/\b(book|booking|reservation|reserve|reservasi|jadwal|sewa|pesan)\b/')) {
            $reply='Sign in, choose your console, date, time, and duration, then review the estimated cost and submit your booking. Track confirmation in My Rentals. Sending a request does not confirm a console.';
            $links=[['label'=>'Book a session','url'=>route('reservations.create')], ...$links];
        } elseif ($has('/\b(food|drink|snack|menu|makanan|minuman)\b/')) {
            $reply='Browse Food & Drinks for the current menu. Check stock and ordering details with the team.';
            $links=[['label'=>'Food & Drinks','url'=>route('menu-fnb')]];
        } elseif ($has('/\b(install|app|iphone|android|pasang)\b/')) {
            $reply='On the sign-in page, choose Install. Use Chrome on Android and follow the instructions. On iPhone, open Safari, tap Share, then Add to Home Screen. This installs a web app; no APK is required.';
            $links=[['label'=>'Open sign-in page','url'=>route('login')]];
        } elseif ($has('/\b(contact|address|location|hours|payment|pay|refund|kontak|alamat|buka|bayar|whatsapp)\b/')) {
            $site=SiteContent::localizedValues();
            $parts=array_filter([$site['address']?'Address: '.$site['address']:null, $site['opening_hours']?'Opening hours: '.$site['opening_hours']:null, $site['whatsapp']?'WhatsApp: '.$site['whatsapp']:null, $site['contact_email']?'Email: '.$site['contact_email']:null]);
            $reply=($parts?implode("\n",$parts):'Contact details have not been added by the team yet.')."\nPlease confirm payments and refunds directly with the team.";
            $links=[['label'=>'Contact the team','url'=>route('kontak')]];
        } else {
            $reply="Hi, I'm Selo! I can help with console prices, games, bookings, remaining time, and installing the app. Try asking: How much is a PS5 session?";
            $links=[['label'=>'Browse consoles','url'=>route('page-ke2')]];
        }
        return compact('reply', 'links');
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
