<header class="ps-header">
    <div class="ps-container ps-nav-wrap">
        <a class="ps-brand" href="{{ route('home-page') }}" aria-label="{{ __('PS Seleraku Beranda') }}">
            <img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" class="ps-nav-brand-img" width="2172" height="724" decoding="async">
        </a>

        <div class="ps-nav-right">
            @include('partials.language-switch')
            <nav class="ps-nav-links" aria-label="{{ __('Navigasi Utama') }}">
                <a class="ps-nav-link {{ $activePage === 'rentals' ? 'is-active' : '' }}" href="{{ route('reservations.index') }}">{{ __('RENTAL SAYA') }}</a>
                <a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}">{{ __('BERANDA') }}</a>
                <a class="ps-nav-link {{ $activePage === 'tentang' ? 'is-active' : '' }}" href="{{ route('tentang') }}">{{ __('TENTANG') }}</a>
                <a class="ps-nav-link {{ $activePage === 'sewa-harian' ? 'is-active' : '' }}" href="{{ route('sewa-harian') }}">{{ __('SEWA HARIAN') }}</a>
                <a class="ps-nav-link {{ $activePage === 'page-ke2' ? 'is-active' : '' }}" href="{{ route('page-ke2') }}">{{ __('KONSOL & GAME') }}</a>
                <a class="ps-nav-link {{ $activePage === 'cara-sewa' ? 'is-active' : '' }}" href="{{ route('cara-sewa') }}">{{ __('CARA SEWA') }}</a>
                <a class="ps-nav-link {{ $activePage === 'kontak' ? 'is-active' : '' }}" href="{{ route('kontak') }}">{{ __('KONTAK') }}</a>
            </nav>

            <a class="ps-nav-cta" href="{{ route('reservations.create') }}">{{ __('PESAN SEKARANG') }}</a>

            <div class="ps-account-wrap">
                <button id="ps-account-btn" class="ps-avatar-btn" type="button" aria-label="{{ __('Menu Akun') }}" aria-expanded="false" aria-controls="ps-account-menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </button>
                <div id="ps-account-menu" class="ps-account-dropdown" hidden>
                    @auth
                        <a class="ps-dropdown-item" href="{{ route('reservations.index') }}">{{ __('Rental Saya') }} <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('reservations.create') }}">{{ __('Reservasi Baru') }} <span>↗</span></a>
                        <div class="ps-account-meta">
                            <strong>{{ auth()->user()?->name }}</strong>
                            <span>{{ auth()->user()?->email }}</span>
                        </div>
                        <a class="ps-dropdown-item" href="{{ route('home-page') }}">{{ __('Beranda') }} <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('page-ke2') }}">{{ __('Konsol & Game') }} <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('sewa-harian') }}">{{ __('Sewa Harian (Bawa Pulang)') }} <span>→</span></a>
                        <a class="ps-dropdown-item" href="{{ route('menu-fnb') }}">{{ __('Menu F&B Lounge') }} <span>→</span></a>
                        @if(auth()->user()?->is_admin)
                            <a class="ps-dropdown-item" href="{{ url('/admin') }}">{{ __('Panel Admin') }} <span>↗</span></a>
                        @endif
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="ps-dropdown-item ps-dropdown-logout">{{ __('Keluar') }}</button>
                        </form>
                    @else
                        <a class="ps-dropdown-item" href="{{ route('login') }}">{{ __('Masuk ke Akun') }} <span>→</span></a>
                    @endauth
                </div>
            </div>

            <button id="ps-mobile-toggle" class="ps-mobile-toggle" type="button" aria-label="{{ __('Buka menu navigasi') }}" aria-expanded="false" aria-controls="ps-mobile-drawer">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="ps-mobile-drawer" class="ps-mobile-drawer">
        <a class="ps-nav-link" href="{{ route('reservations.index') }}">{{ __('RENTAL SAYA') }}</a>
        <a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}">{{ __('BERANDA') }}</a>
        <a class="ps-nav-link {{ $activePage === 'tentang' ? 'is-active' : '' }}" href="{{ route('tentang') }}">{{ __('TENTANG') }}</a>
        <a class="ps-nav-link" href="{{ route('sewa-harian') }}">{{ __('SEWA HARIAN') }}</a>
        <a class="ps-nav-link {{ $activePage === 'page-ke2' ? 'is-active' : '' }}" href="{{ route('page-ke2') }}">{{ __('KONSOL & GAME') }}</a>
        <a class="ps-nav-link {{ $activePage === 'cara-sewa' ? 'is-active' : '' }}" href="{{ route('cara-sewa') }}">{{ __('CARA SEWA') }}</a>
        <a class="ps-nav-link {{ $activePage === 'kontak' ? 'is-active' : '' }}" href="{{ route('kontak') }}">{{ __('KONTAK') }}</a>
    </div>
</header>
