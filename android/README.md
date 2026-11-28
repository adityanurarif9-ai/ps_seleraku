# PS Seleraku — proyek Android Studio

Proyek Kotlin untuk menampilkan website PS Seleraku yang sudah online dalam Trusted Web Activity (TWA). Tampilan halaman tetap berasal dari HTML/CSS/JavaScript website Laravel, bukan ditulis ulang sebagai layout Kotlin/Compose. MainActivity Kotlin adalah pintu masuk Android, memvalidasi tautan ke domain sendiri, lalu membuka browser tepercaya. Ini perlu dijelaskan apa adanya saat presentasi ke guru; proyek ini bukan aplikasi dengan seluruh layar native.

## Buka dan jalankan

1. Android Studio → Open → pilih folder android ini (D:/ps_seleraku/android pada laptop ini).
2. Tunggu Gradle Sync. Gunakan Gradle JDK bawaan Android Studio (Java 21), SDK Platform 36.1 (Android 16), Android minimal 7.0 dan Build Tools 36.0.0.
3. Pilih HP Android atau emulator yang memiliki Chrome terbaru, lalu klik Run. APK release yang sudah ditandatangani adalah hasil yang dipakai untuk pemasangan/demo.
4. Website membutuhkan internet. Akun, katalog, reservasi, keranjang, menu kedai, pembayaran demo, timer, dan admin tetap menggunakan server yang sama.

## File coding utama

- app/src/main/java/site/flexboxstudio/psseleraku/MainActivity.kt — Activity Android.
- app/src/main/java/site/flexboxstudio/psseleraku/LaunchPolicy.kt — pemeriksaan URL yang boleh dibuka oleh Intent.
- app/src/main/AndroidManifest.xml — konfigurasi aplikasi, ikon, splash, dan tautan domain.
- app/build.gradle.kts — SDK, dependensi, versi aplikasi, dan signing.
- website/assetlinks.json — pernyataan domain untuk sertifikat release yang dibuat di laptop ini.

## Build dari terminal Android Studio

    .\gradlew.bat assembleDebug testDebugUnitTest lintDebug
    .\gradlew.bat assembleRelease

Tanpa signing.properties, release belum ditandatangani. signing.properties dan keystore/ bersifat privat, tidak masuk Git. Simpan cadangan keduanya dengan aman untuk membuat pembaruan APK yang kompatibel. Jangan mengganti kunci untuk APK dengan applicationId yang sama. APP_KEY, password TiDB, secret Google/Turnstile, dan kredensial backend tidak dimasukkan ke APK.

## Domain dan browser

TWA memakai sesi browser, sehingga login Google tidak dipaksa berjalan di WebView tertanam. Chrome menampilkan identitas domain ketika membuka situs lain (misalnya Google), sesuai perlindungan browser. Sertifikat release perlu dicantumkan di https://ps.flexboxstudio.site/.well-known/assetlinks.json agar website sendiri terbuka penuh tanpa bilah alamat. Build debug menggunakan sertifikat berbeda dan dapat menampilkan bilah alamat; sertifikat debug tidak dipublikasikan ke domain produksi. Jika browser tidak mendukung TWA, library memakai Custom Tab, bukan mengabaikan pemeriksaan sertifikat atau mematikan validasi domain.

Foto identitas, unduhan PDF, dan QR tetap dikelola website/browser. Aplikasi tidak meminta izin membaca seluruh penyimpanan, kamera, lokasi, atau kontak. Alarm tetap mengikuti izin suara/notifikasi dan pembatasan browser/Android; APK ini tidak menjanjikan alarm saat aplikasi ditutup paksa atau HP dimatikan. Pembayaran masih DEMO dan tidak memindahkan saldo.

## Validasi

Unit test memeriksa tautan reservasi/QR tetap utuh serta URL HTTP, domain palsu, userinfo, port asing, file/content/intent/javascript ditolak. Build dan lint diverifikasi terpisah dari uji pada HP; lihat BUILD-RESULT.md untuk hasil aktual.

Referensi resmi: [Android Browser Helper](https://github.com/GoogleChrome/android-browser-helper), [TWA](https://developer.chrome.com/docs/android/trusted-web-activity), [Kotlin bawaan AGP](https://developer.android.com/build/migrate-to-built-in-kotlin).
