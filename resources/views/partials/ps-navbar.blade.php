<header class="ps-header">
    <div class="ps-container ps-nav-wrap">
        <a class="ps-brand" href="{{ route('home-page') }}" aria-label="PS Seleraku Beranda">
            <img src="{{ asset('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" class="ps-nav-brand-img" width="2172" height="724">
        </a>

        <div class="ps-nav-right">
            <nav class="ps-nav-links" aria-label="Navigasi Utama">
                <a class="ps-nav-link {{ $activePage === 'rentals' ? 'is-active' : '' }}" href="{{ route('reservations.index') }}">RENTAL SAYA</a>
                <a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}">BERANDA</a>
                <a class="ps-nav-link" href="{{ route('home-page') }}#tentang">TENTANG</a>
                <a class="ps-nav-link" href="{{ route('home-page') }}#paket">PAKET</a>
                <a class="ps-nav-link {{ $activePage === 'page-ke2' ? 'is-active' : '' }}" href="{{ route('page-ke2') }}">GAME</a>
                <a class="ps-nav-link" href="{{ route('home-page') }}#cara-sewa">CARA SEWA</a>
                <a class="ps-nav-link" href="#kontak">KONTAK</a>
            </nav>

            @if($activePage === 'home-page')
                <a class="ps-nav-cta" href="{{ route('reservations.create') }}">PESAN SEKARANG</a>
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
                        <a class="ps-dropdown-item" href="{{ route('reservations.index') }}">Rental Saya <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('reservations.create') }}">Reservasi Baru <span>↗</span></a>
                        <div class="ps-account-meta">
                            <strong>{{ auth()->user()?->name }}</strong>
                            <span>{{ auth()->user()?->email }}</span>
                        </div>
                        <a class="ps-dropdown-item" href="{{ route('home-page') }}">Home Page <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('page-ke2') }}">Unit PS (Page Ke2) <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('sewa-harian') }}">Sewa Harian (Bawa Pulang) <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('menu-fnb') }}">Menu F&amp;B Lounge <span>→</span></a>
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
        <a class="ps-nav-link" href="{{ route('reservations.index') }}">RENTAL SAYA</a>
        <a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}">BERANDA</a>
        <a class="ps-nav-link" href="{{ route('home-page') }}#tentang">TENTANG</a>
        <a class="ps-nav-link" href="{{ route('home-page') }}#paket">PAKET</a>
        <a class="ps-nav-link {{ $activePage === 'page-ke2' ? 'is-active' : '' }}" href="{{ route('page-ke2') }}">GAME</a>
        <a class="ps-nav-link" href="{{ route('home-page') }}#cara-sewa">CARA SEWA</a>
        <a class="ps-nav-link" href="#kontak">KONTAK</a>
    </div>
</header>
