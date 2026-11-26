# Aktivasi pembayaran Midtrans

Status: desain website sudah disesuaikan. Integrasi checkout dan penerimaan pembayaran belum diaktifkan karena akun merchant belum tersedia. Tidak ada QRIS contoh yang dianggap sebagai pembayaran, dan status reservasi bukan bukti lunas.

## Yang perlu dilakukan pengelola

1. Buat akun bisnis melalui https://midtrans.com dan verifikasi email. Gunakan identitas pengelola yang berwenang.
2. Daftarkan situs https://ps.flexboxstudio.site dan lengkapi informasi usaha melalui dashboard resmi Midtrans. Dokumen usaha, identitas, serta rekening diisi langsung pada layanan tersebut.
3. Setelah dashboard Sandbox tersedia, lanjutkan integrasi dan uji pembayaran percobaan dahulu.
4. Untuk pembayaran sungguhan, selesaikan aktivasi merchant dan pastikan QRIS tersedia di lingkungan Production.
5. Kunci API disimpan secara privat di environment server. Jangan masukkan kunci, data rekening, atau dokumen identitas ke Git maupun chat.

## Tahap integrasi setelah akun tersedia

- Buat transaksi dari reservasi milik pengguna yang telah dikonfirmasi admin, dengan nominal dari database server.
- Alihkan pengguna ke Midtrans Snap Redirect. Tampilkan metode yang memang aktif pada merchant.
- Terima notifikasi server Midtrans secara idempoten; verifikasi keaslian, order, mata uang, dan jumlah. Periksa status langsung dari API bila diperlukan.
- Halaman kembali dari browser tidak boleh menetapkan pembayaran sebagai lunas.
- Pisahkan status pembayaran dari status reservasi dan timer bermain. Tangani pembayaran gagal, kedaluwarsa, pengembalian dana, serta reservasi yang berubah setelah transaksi dibuat.
- Uji alur Sandbox dari browser hingga database dan admin. Aktifkan Production hanya setelah akun disetujui, konfigurasi notifikasi diuji, dan transaksi nyata diuji pengelola.

## Referensi resmi

- https://docs.midtrans.com/docs/dashboard-basics
- https://docs.midtrans.com/docs/snap-snap-integration-guide
- https://docs.midtrans.com/docs/https-notification-webhooks
- https://docs.midtrans.com/docs/access-keys
- https://docs.midtrans.com/docs/bagaimana-cara-memindahkan-akun-dari-sandbox-ke-production
