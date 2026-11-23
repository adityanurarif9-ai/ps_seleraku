<header class="ps-header">
    <div class="ps-container ps-nav-wrap">
        <a class="ps-brand" href="{{ route('home-page') }}" aria-label="PS Seleraku Beranda">
            <svg width="46" height="34" viewBox="0 0 64 46" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M19 6H25L27 10H37L39 6H45C52 6 58 13 59 24L60 33C60.5 38 56.5 41 52 40C48.5 39.2 46 36 44.5 32H19.5C18 36 15.5 39.2 12 40C7.5 41 3.5 38 4 33L5 24C6 13 12 6 19 6Z" fill="#0B132B" stroke="#00F0FF" stroke-width="3" stroke-linejoin="round"/>
                <path d="M9.5 36.5C12.5 35.5 14.8 33 16 30" stroke="#A855F7" stroke-width="3" stroke-linecap="round"/>
                <path d="M54.5 36.5C51.5 35.5 49.2 33 48 30" stroke="#A855F7" stroke-width="3" stroke-linecap="round"/>
                <path d="M29 16L38 21.5L29 27V16Z" fill="#00F0FF"/>
                <path d="M16 18V25M12.5 21.5H19.5" stroke="#334155" stroke-width="2.4" stroke-linecap="round"/>
                <circle cx="46" cy="18" r="1.8" fill="#334155"/>
                <circle cx="49.5" cy="21.5" r="1.8" fill="#334155"/>
                <circle cx="46" cy="25" r="1.8" fill="#334155"/>
                <circle cx="42.5" cy="21.5" r="1.8" fill="#334155"/>
            </svg>
            <span class="ps-brand-text">PS SELERAKU</span>
        </a>

        <div class="ps-nav-right">
            <nav class="ps-nav-links" aria-label="Navigasi Utama">
                <a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}">BERANDA</a>
                <a class="ps-nav-link" href="{{ route('home-page') }}#tentang">TENTANG</a>
                <a class="ps-nav-link" href="{{ route('home-page') }}#paket">PAKET</a>
                <a class="ps-nav-link {{ $activePage === 'page-ke2' ? 'is-active' : '' }}" href="{{ route('page-ke2') }}">GAME</a>
                <a class="ps-nav-link" href="{{ route('home-page') }}#cara-sewa">CARA SEWA</a>
                <a class="ps-nav-link" href="#kontak">KONTAK</a>
                <a class="ps-nav-link" href="{{ route('catalog') }}">KATALOG</a>
            </nav>

            @if($activePage === 'home-page')
                <a class="ps-nav-cta" href="{{ route('page-ke2') }}#unit-catalog">PESAN SEKARANG</a>
            @else
                <button class="ps-nav-cta" type="button" data-open-booking data-item-id="ps5-vip-01" data-title="Reservasi Unit PlayStation" data-subtitle="Pilih unit, layanan, durasi, jadwal, dan game favorit Anda.">PESAN SEKARANG</button>
            @endif

            <div class="ps-account-wrap">
                <button id="ps-account-btn" class="ps-avatar-btn" type="button" aria-label="Menu Akun" aria-expanded="false" aria-controls="ps-account-menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </button>
                <div id="ps-account-menu" class="ps-account-dropdown" hidden>
                    @auth
                        <div class="ps-account-meta">
                            <strong>{{ auth()->user()?->name }}</strong>
                            <span>{{ auth()->user()?->email }}</span>
                        </div>
                        <a class="ps-dropdown-item" href="{{ route('catalog') }}">Katalog &amp; Kedai <span>↗</span></a>
                        <a class="ps-dropdown-item" href="{{ route('home-page') }}">Home Page <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('page-ke2') }}">Unit PS (Page Ke2) <span>→</span></a>
                        @if(auth()->user()?->is_admin)
                            <a class="ps-dropdown-item" href="{{ url('/admin') }}">Panel Admin <span>↗</span></a>
                        @endif
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="ps-dropdown-item ps-dropdown-logout">Keluar</button>
                        </form>
                    @else
                        <a class="ps-dropdown-item" href="{{ route('login') }}">Masuk ke Akun <span>→</span></a>
                    @endauth
                </div>
            </div>

            <button id="ps-mobile-toggle" class="ps-mobile-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="ps-mobile-drawer">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="ps-mobile-drawer" class="ps-mobile-drawer">
        <a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}">BERANDA</a>
        <a class="ps-nav-link" href="{{ route('home-page') }}#tentang">TENTANG</a>
        <a class="ps-nav-link" href="{{ route('home-page') }}#paket">PAKET</a>
        <a class="ps-nav-link {{ $activePage === 'page-ke2' ? 'is-active' : '' }}" href="{{ route('page-ke2') }}">GAME</a>
        <a class="ps-nav-link" href="{{ route('home-page') }}#cara-sewa">CARA SEWA</a>
        <a class="ps-nav-link" href="#kontak">KONTAK</a>
        <a class="ps-nav-link" href="{{ route('catalog') }}">KATALOG &amp; KEDAI</a>
    </div>
</header>
