@php
    $mapsQuery = 'Jl. Boulevard Raya Gaming Arc No. 77, Senopati Cyber District, Jakarta Selatan';
    $mapsExternalUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapsQuery);
    $mapsEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode('Jl. Senopati, Kebayoran Baru, Jakarta Selatan') . '&t=&z=16&hl=id&ie=UTF8&iwloc=&output=embed';
@endphp

<footer id="kontak" class="ps-footer ps-footer-figma">
    <div class="ps-container">
        <div class="ps-footer-grid ps-footer-grid-figma">
            <!-- Kolom 1: Identitas Brand & Kontak -->
            <div class="ps-footer-col ps-footer-brand-col">
                <a class="ps-footer-logo-lockup" href="{{ route('home-page') }}" aria-label="PS Seleraku Beranda">
                    <img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" class="ps-footer-brand-img" width="2172" height="724" decoding="async">
                </a>

                <p class="ps-footer-desc">
                    Destinasi gaming lounge premium &amp; rental PlayStation berstandar turnamen. Nikmati kenyamanan sofa ergonomis, display 4K OLED, serta pilihan konsol terkini.
                </p>

                <p class="ps-footer-op-plain">Operasional: Buka 24 Jam Non-Stop</p>

                <div class="ps-footer-contact-group">
                    <h4>Kontak</h4>
                    <ul class="ps-footer-list">
                        <li>
                            <a href="https://wa.me/6281288997755" target="_blank" rel="noopener noreferrer">
                                WhatsApp: +62 812-8899-PS55
                            </a>
                        </li>
                        <li>
                            <a href="mailto:concierge@psseleraku.id">
                                Email: concierge@psseleraku.id
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Kolom 2: Jam & Fasilitas -->
            <div class="ps-footer-col">
                <h4>Jam &amp; Fasilitas</h4>
                <ul class="ps-footer-list">
                    <li>Setiap Hari: 24 Jam / Everyday</li>
                    <li>VIP Private Room &amp; Soundproof</li>
                    <li>PlayStation 5 Pro &amp; PS4 Pro Lounge</li>
                    <li>High-Speed Fiber Esports Internet</li>
                    <li>DualSense Edge &amp; Racing Wheel Set</li>
                </ul>
            </div>

            <!-- Kolom 3: Eksplorasi & Follow kami di (Referensi Traveloka) -->
            <div class="ps-footer-col ps-footer-explore-follow">
                <div class="ps-footer-subblock">
                    <h4>Eksplorasi</h4>
                    <ul class="ps-footer-list">
                        <li><a href="{{ route('page-ke2') }}">Katalog Unit &amp; Game</a></li>
                        <li><a href="{{ route('sewa-harian') }}">Paket Harian &amp; Mingguan</a></li>
                        <li><a href="{{ route('menu-fnb') }}">Lounge F&amp;B Menu</a></li>
                        <li><a href="{{ route('page-ke2') }}#unit-catalog">Reservasi Station Online</a></li>
                    </ul>
                </div>

                <div class="ps-footer-subblock">
                    <h4>Follow kami di</h4>
                    <ul class="ps-footer-social-list">
                        <li>
                            <a href="https://facebook.com/psseleraku" target="_blank" rel="noopener noreferrer">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" fill="#1877F2"/>
                                    <path d="M13.5 19V12.6H15.6L15.9 10.1H13.5V8.6C13.5 7.9 13.7 7.4 14.7 7.4H16V5.2C15.8 5.2 15 5.1 14.1 5.1C12.2 5.1 10.9 6.3 10.9 8.4V10.1H8.8V12.6H10.9V19H13.5Z" fill="#FFFFFF"/>
                                </svg>
                                <span>Facebook</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://instagram.com/psseleraku" target="_blank" rel="noopener noreferrer">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="psIgGrad" x1="2" y1="22" x2="22" y2="2" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#F58529"/>
                                            <stop offset="50%" stop-color="#DD2A7B"/>
                                            <stop offset="100%" stop-color="#8134AF"/>
                                        </linearGradient>
                                    </defs>
                                    <rect x="3" y="3" width="18" height="18" rx="5" stroke="url(#psIgGrad)" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="4" stroke="url(#psIgGrad)" stroke-width="2"/>
                                    <circle cx="17.2" cy="6.8" r="1.2" fill="#DD2A7B"/>
                                </svg>
                                <span>Instagram</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://tiktok.com/@psseleraku" target="_blank" rel="noopener noreferrer">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M16.5 4C16.8 6 18.2 7.4 20 7.7V10.5C18.6 10.5 17.3 10 16.2 9.2V14.8C16.2 18.1 13.5 20.5 10.4 20.5C7.3 20.5 4.8 18 4.8 14.9C4.8 11.8 7.3 9.3 10.4 9.3C10.9 9.3 11.4 9.4 11.8 9.5V12.4C11.4 12.2 10.9 12.1 10.4 12.1C8.9 12.1 7.6 13.3 7.6 14.9C7.6 16.5 8.9 17.7 10.4 17.7C12.1 17.7 13.4 16.5 13.4 14.6V4H16.5Z" fill="#FFFFFF"/>
                                </svg>
                                <span>TikTok</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://youtube.com/@psseleraku" target="_blank" rel="noopener noreferrer">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="2" y="5" width="20" height="14" rx="4" fill="#FF0000"/>
                                    <path d="M10 9.2L15.5 12L10 14.8V9.2Z" fill="#FFFFFF"/>
                                </svg>
                                <span>Youtube</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://x.com/psseleraku" target="_blank" rel="noopener noreferrer">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4.5 4.5L10.3 12.5L4.4 19.5H6.4L11.2 13.8L15.4 19.5H19.6L13.5 11.1L19 4.5H17L12.6 9.8L8.7 4.5H4.5Z" stroke="#E2E8F0" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                                <span>Twitter</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/6281288997755" target="_blank" rel="noopener noreferrer">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9.5" fill="#25D366"/>
                                    <path d="M8.9 8.2C8.7 7.8 8.5 7.8 8.2 7.8C7.9 7.8 7.6 7.9 7.4 8.2C7.1 8.5 6.5 9.1 6.5 10.3C6.5 11.5 7.4 12.7 7.5 12.9C7.6 13.1 9.2 15.6 11.7 16.6C13.8 17.4 14.2 17.2 14.7 17.2C15.2 17.1 16.2 16.5 16.4 15.9C16.6 15.3 16.6 14.8 16.5 14.7C16.4 14.6 16.2 14.5 15.9 14.3C15.6 14.2 14.3 13.5 14.1 13.4C13.8 13.3 13.7 13.3 13.5 13.6C13.3 13.9 12.8 14.5 12.7 14.6C12.5 14.8 12.4 14.8 12.1 14.7C11.8 14.5 10.9 14.2 9.9 13.3C9.1 12.6 8.6 11.7 8.4 11.4C8.3 11.1 8.4 11 8.6 10.8C8.7 10.7 8.9 10.4 9 10.3C9.2 10.1 9.2 10 9.3 9.8C9.4 9.6 9.3 9.5 9.3 9.3C9.2 9.2 9.1 8.6 8.9 8.2Z" fill="#FFFFFF"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Kolom 4: Temukan Lokasi Kami (Google Maps Langsung) -->
            <div class="ps-footer-col ps-footer-map-col">
                <h4>Temukan Lokasi Kami</h4>
                <div class="ps-footer-map-frame">
                    <iframe
                        title="Google Maps Lokasi PS Seleraku"
                        src="{{ $mapsEmbedUrl }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
                <p class="ps-footer-address">
                    {{ $mapsQuery }}
                </p>
                <a class="ps-footer-map-link" href="{{ $mapsExternalUrl }}" target="_blank" rel="noopener noreferrer">
                    Lihat di Google Maps
                </a>
            </div>
        </div>

        <div class="ps-footer-bottom">
            <span>&copy; 2025 PS SELERAKU Gaming Lounge. Hak Cipta Dilindungi.</span>
            <div class="ps-footer-legal">
                <button type="button" data-info-modal="ketentuan">Ketentuan Sewa</button>
                <button type="button" data-info-modal="privasi">Kebijakan Privasi</button>
                <button type="button" data-info-modal="bantuan">Bantuan Support</button>
            </div>
        </div>
    </div>
</footer>
