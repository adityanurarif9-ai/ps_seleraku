<header class="ps-header">
    <div class="ps-container ps-nav-wrap">
        <a class="ps-brand" href="{{ route('home-page') }}" aria-label="{{ __('PS Seleraku Beranda') }}">
            <img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" class="ps-nav-brand-img" width="2172" height="724" decoding="async">
        </a>

        <div class="ps-nav-right">
            <nav class="ps-nav-links" aria-label="{{ __('Navigasi Utama') }}">
                @include('partials.navigation-links')
            </nav>

            <a class="ps-nav-cta" href="{{ route('reservations.create') }}">{{ __('PESAN SEKARANG') }}</a>

            <a class="ps-cart-link" href="{{ route('cart.index') }}" aria-label="{{ __('Keranjang') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h2l3 12h10l3-9H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                @if(count(session('shopping_cart', [])))<span class="ps-cart-count">{{ min(99,collect(session('shopping_cart', []))->sum('quantity')) }}</span>@endif
            </a>
            @include('partials.language-switch')
            <div class="ps-account-wrap">
                <button id="ps-account-btn" class="ps-avatar-btn" type="button" aria-label="{{ __('Menu Akun') }}" aria-expanded="false" aria-controls="ps-account-menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </button>
                <div id="ps-account-menu" class="ps-account-dropdown" hidden>
                    @auth
                        <a class="ps-dropdown-item" href="{{ route('food-orders.index') }}">{{ __('Pesanan Makanan') }}</a>
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
        @include('partials.navigation-links')
    </div>
</header>

@include('partials.mobile-bottom-nav')
