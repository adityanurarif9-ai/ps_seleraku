<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page · PS Seleraku</title>
    <link rel="stylesheet" href="{{ asset('seleraku-pages.css') }}">
    <script src="{{ asset('seleraku-pages.js') }}" defer></script>
</head>
<body class="ps-page-body">
@include('partials.ps-navbar', ['activePage' => 'home-page'])

<main>
    <!-- Hero Section -->
    <section class="ps-hero-home">
        <div class="ps-hero-inner">
            <div class="ps-hero-actions">
                <button
                    type="button"
                    class="ps-btn-dark"
                    data-open-booking
                    data-item-id="paket-ps5-nextgen"
                    data-title="Pesan Konsol Sekarang"
                    data-subtitle="Pilih paket atau unit favorit Anda — siap antar cepat atau main di tempat."
                >
                    PESAN SEKARANG &rarr;
                </button>
                <a class="ps-btn-gold" href="#paket">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/>
                        <circle cx="15" cy="13" r="1"/><circle cx="18" cy="11" r="1"/>
                        <rect x="2" y="6" width="20" height="12" rx="6"/>
                    </svg>
                    LIHAT PAKET
                </a>
            </div>

            <h1 class="ps-hero-title">
                RENTAL <span class="ps-text-ps4">PS4</span> &amp; <span class="ps-text-ps5">PS5</span>
            </h1>

            <div class="ps-hero-pill">
                <span class="ps-dot-red" aria-hidden="true"></span>
                RENTAL PLAYSTATION TERPERCAYA
            </div>

            <div class="ps-hero-divider" aria-hidden="true"></div>

            <div class="ps-stats-grid">
                <div class="ps-stat-card">
                    <div class="ps-stat-val">500<span class="ps-stat-plus-pink">+</span></div>
                    <div class="ps-stat-label">PELANGGAN PUAS</div>
                </div>
                <div class="ps-stat-card">
                    <div class="ps-stat-val">50<span class="ps-stat-plus-gold">+</span></div>
                    <div class="ps-stat-label">GAME TERSEDIA</div>
                </div>
                <div class="ps-stat-card">
                    <div class="ps-stat-val"><span class="ps-stat-star">&#9733;</span>5.0</div>
                    <div class="ps-stat-label">RATING KEPUASAN</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami / Cara Sewa Section -->
    <section id="tentang" class="ps-section-about">
        <div class="ps-container">
            <div class="ps-about-grid">
                <div class="ps-showcase-card">
                    <img class="ps-showcase-img" src="{{ asset('hero.png') }}" alt="Setup Konsol PlayStation 5 Premium PS Seleraku">
                    <div class="ps-showcase-glow" aria-hidden="true"></div>
                    <div class="ps-badge-original">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2l2.9 2.9 4.1-.5.5 4.1L22 12l-2.5 3.5-.5 4.1-4.1-.5L12 22l-2.9-2.9-4.1.5-.5-4.1L2 12l2.5-3.5.5-4.1 4.1.5L12 2z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                        UNIT PREMIUM ORIGINAL
                    </div>

                    <div class="ps-showcase-bottom">
                        <div>
                            <h3>LAYANAN ANTAR-JEMPUT EXPRESS</h3>
                            <p>Teknisi profesional siap pasang rapi langsung di tempat Anda</p>
                        </div>
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;">
                            <rect x="1" y="3" width="15" height="13" rx="2"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <div class="ps-eyebrow">TENTANG KAMI</div>
                    <h2 class="ps-section-heading">
                        KENALAN DENGAN <span>PS<br>SELERAKU</span>
                    </h2>
                    <p class="ps-about-copy">
                        PS SELERAKU hadir untuk memberikan pengalaman bermain PlayStation yang menyenangkan, berkelas, nyaman, dan terjangkau. Kami menyediakan opsi konsol PS4 dan PS5 terbaik dalam kondisi prima untuk menemani momen seru Anda bersama sahabat, keluarga, hingga agenda turnamen komunitas.
                    </p>

                    <div id="cara-sewa" class="ps-feature-grid">
                        <article class="ps-feature-box">
                            <div class="ps-feature-icon ps-icon-sky" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 3h6v3H9z"/><path d="M7 6h10l1 13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L7 6z"/>
                                    <path d="M12 10v5M9.5 12.5h5"/>
                                </svg>
                            </div>
                            <h3>UNIT TERAWAT &amp; HIGIENIS</h3>
                            <p>Selalu dibersihkan dan disterilisasi sebelum dikirim ke pelanggan.</p>
                        </article>

                        <article class="ps-feature-box">
                            <div class="ps-feature-icon ps-icon-purple" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="6" width="20" height="12" rx="6"/>
                                    <line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/>
                                    <circle cx="15" cy="13" r="1"/><circle cx="18" cy="11" r="1"/>
                                </svg>
                            </div>
                            <h3>STIK BEBAS DRIFT</h3>
                            <p>DualSense &amp; DualShock original responsif tanpa kendala drifting.</p>
                        </article>

                        <article class="ps-feature-box">
                            <div class="ps-feature-icon ps-icon-sky" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                                    <path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                                </svg>
                            </div>
                            <h3>PEMASANGAN CEPAT</h3>
                            <p>Teknisi profesional kami bantu setup langsung sampai siap main.</p>
                        </article>

                        <article class="ps-feature-box">
                            <div class="ps-feature-icon ps-icon-purple" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19.439 7.85c-.049.322.059.648.289.878l1.568 1.568c.47.47.706 1.087.706 1.704s-.235 1.233-.706 1.704l-1.611 1.611a.98.98 0 0 1-.837.276c-.47-.07-.802-.48-.968-.925a2.501 2.501 0 1 0-3.214 3.214c.446.166.855.497.925.968a.979.979 0 0 1-.276.837l-1.61 1.61a2.404 2.404 0 0 1-1.705.707 2.402 2.402 0 0 1-1.704-.706l-1.568-1.568a1.026 1.026 0 0 0-.877-.29c-.493.074-.84.504-1.02.968a2.5 2.5 0 1 1-3.237-3.237c.464-.18.894-.527.967-1.02a1.026 1.026 0 0 0-.289-.877l-1.568-1.568A2.402 2.402 0 0 1 1.998 12c0-.617.236-1.234.706-1.704L4.23 8.77c.24-.24.581-.353.917-.303.515.077.877.528 1.073 1.01a2.5 2.5 0 1 0 3.259-3.259c-.482-.196-.933-.558-1.01-1.073-.05-.336.062-.676.303-.917l1.525-1.525A2.402 2.402 0 0 1 12 2c.617 0 1.234.236 1.704.706l1.568 1.568c.23.23.556.338.877.29.493-.074.84-.504 1.02-.968a2.5 2.5 0 1 1 3.237 3.237c-.464.18-.894.527-.967 1.02Z"/>
                                </svg>
                            </div>
                            <h3>BEBAS REQUEST GAME</h3>
                            <p>Daftar judul favorit dapat diunduh sebelum pengantaran unit.</p>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing & Bundle Section -->
    <section id="paket" class="ps-section-pricing">
        <div class="ps-container">
            <div class="ps-pricing-header">
                <div class="ps-eyebrow-center">PRICING &amp; BUNDLE</div>
                <h2 class="ps-section-heading" style="margin-bottom:10px;">PILIHAN PAKET RENTAL TERBAIK</h2>
                <p>Pilih konsol impian Anda dengan paket harian, weekend, maupun mingguan bergaransi resmi.</p>
            </div>

            @php
                $packages = [
                    [
                        'id' => 'paket-ps4-reguler',
                        'image' => 'images/paket-ps4.png',
                        'pill' => 'ENTRY PRO',
                        'pillClass' => 'pill-cyan',
                        'featured' => false,
                        'ribbon' => null,
                        'name' => 'PAKET PS4 REGULER',
                        'desc' => 'Cocok untuk mabar seru bareng teman di rumah atau kos.',
                        'price' => 'Rp 70.000',
                        'rawPrice' => 70000,
                        'unit' => '/ Hari',
                        'unitType' => 'Hari',
                        'btnClass' => '',
                        'features' => [
                            '1 Unit Konsol PS4 Slim / Pro',
                            '2 Wireless DualShock 4 Controller',
                            'Kabel HDMI & Kabel Daya Lengkap',
                            '10+ Game Terinstall Populer',
                            'Free Pengantaran Radius 5km',
                        ],
                    ],
                    [
                        'id' => 'paket-ps5-nextgen',
                        'image' => 'images/paket-ps5.png',
                        'pill' => 'NEXT-GEN ULTRA',
                        'pillClass' => 'pill-cyan',
                        'featured' => true,
                        'ribbon' => 'BEST SELLER',
                        'name' => 'PAKET PS5 NEXT-GEN',
                        'desc' => 'Grafis 4K 120Hz & haptic feedback imersif tanpa tanding.',
                        'price' => 'Rp 150.000',
                        'rawPrice' => 150000,
                        'unit' => '/ Hari',
                        'unitType' => 'Hari',
                        'btnClass' => '',
                        'features' => [
                            '1 Unit PS5 Disc / Digital Ultra HD',
                            '2 Wireless DualSense Controller',
                            'Kabel Ultra High Speed HDMI',
                            '15+ Game Terbaru (FC 24, Spiderman 2)',
                            'Akun PS Plus Deluxe Aktif',
                        ],
                    ],
                    [
                        'id' => 'paket-weekend',
                        'image' => 'images/paket-weekend.png',
                        'pill' => 'WEEKEND PASS',
                        'pillClass' => 'pill-purple',
                        'featured' => false,
                        'ribbon' => null,
                        'name' => 'PAKET WEEKEND',
                        'desc' => 'Main maraton dari Jumat sore hingga Senin pagi tenang.',
                        'price' => 'Rp 200.000',
                        'rawPrice' => 200000,
                        'unit' => '/ 3 Hari',
                        'unitType' => 'Paket (3 Hari)',
                        'btnClass' => 'is-lavender',
                        'features' => [
                            'Pilihan Unit Bebas: PS4 / PS5',
                            'Durasi 72 Jam Penuh Non-Stop',
                            '2 Stik Original Responsif',
                            'Free Delivery & Setup Langsung',
                            'Bonus Exclusive Snack Gamer Pack',
                        ],
                    ],
                    [
                        'id' => 'paket-mingguan-pro',
                        'image' => 'images/paket-mingguan.png',
                        'pill' => 'HEMAT 30%',
                        'pillClass' => 'pill-mint',
                        'featured' => false,
                        'ribbon' => null,
                        'name' => 'PAKET MINGGUAN PRO',
                        'desc' => 'Solusi sepuasnya untuk tamatin game story panjang.',
                        'price' => 'Rp 450.000',
                        'rawPrice' => 450000,
                        'unit' => '/ 7 Hari',
                        'unitType' => 'Paket (7 Hari)',
                        'btnClass' => '',
                        'features' => [
                            'Unit Konsol PS4 / PS5 Full Set',
                            'Bebas Tukar Judul Game 2x Selama Sewa',
                            'Maintenance Garansi Tukar Cepat 24 Jam',
                            'Free Antar Jemput All-Area Radius 15km',
                            'Support Online & VIP Priority CS',
                        ],
                    ],
                ];
            @endphp

            <div class="ps-pricing-grid">
                @foreach($packages as $pkg)
                    <article class="ps-plan-card {{ $pkg['featured'] ? 'is-featured' : '' }}">
                        <div class="ps-plan-top">
                            <img class="ps-plan-img" src="{{ asset($pkg['image']) }}" alt="{{ $pkg['name'] }}">
                            <span class="ps-plan-pill {{ $pkg['pillClass'] }}">{{ $pkg['pill'] }}</span>
                            @if($pkg['ribbon'])
                                <span class="ps-ribbon">{{ $pkg['ribbon'] }}</span>
                            @endif
                        </div>

                        <div class="ps-plan-body">
                            <h3>{{ $pkg['name'] }}</h3>
                            <p class="ps-plan-desc">{{ $pkg['desc'] }}</p>

                            <div class="ps-plan-price">
                                <strong>{{ $pkg['price'] }}</strong>
                                <span>{{ $pkg['unit'] }}</span>
                            </div>

                            <ul class="ps-plan-list">
                                @foreach($pkg['features'] as $feat)
                                    <li>
                                        <svg class="ps-check-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="m9 12 2 2 4-4"/>
                                        </svg>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <button
                                type="button"
                                class="ps-btn-plan {{ $pkg['btnClass'] }}"
                                data-open-booking
                                data-item-id="{{ $pkg['id'] }}"
                                data-title="Pesan {{ $pkg['name'] }}"
                                data-subtitle="{{ $pkg['desc'] }}"
                            >
                                PILIH PAKET
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</main>

@include('partials.ps-footer')
@include('partials.ps-modals')
</body>
</html>
