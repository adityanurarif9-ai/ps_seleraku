<!-- Booking / Paket Modal -->
<div id="ps-booking-modal" class="ps-modal-backdrop" hidden role="dialog" aria-modal="true" aria-labelledby="ps-booking-title">
    <div class="ps-modal-card ps-modal-booking-card">
        <div class="ps-modal-head">
            <div>
                <h3 id="ps-booking-title">Reservasi Rental</h3>
                <p id="ps-booking-subtitle" style="margin:4px 0 0;color:#94a3b8;font-size:13px;">Pilih unit, layanan, durasi, jadwal, dan game favorit Anda.</p>
            </div>
            <button type="button" class="ps-modal-close" data-close-modal aria-label="Tutup modal">&times;</button>
        </div>

        <form id="ps-booking-form" class="ps-modal-form">
            <!-- 1. Unit / Paket Dipilih -->
            <div class="ps-field-group">
                <span class="ps-field-label">Unit / Paket Dipilih</span>
                <div class="ps-dropdown" data-dropdown="item">
                    <button type="button" id="ps-dd-item-btn" class="ps-dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ps-dd-trigger-text">
                            <strong id="ps-dd-item-title">PAKET PS5 NEXT-GEN</strong>
                            <small id="ps-dd-item-sub">Rp 150.000 / Hari • 1 Unit PS5 Ultra HD + 2 DualSense</small>
                        </span>
                        <svg class="ps-dd-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div id="ps-dd-item-menu" class="ps-dd-menu" role="listbox" hidden></div>
                </div>
            </div>

            <!-- 2. Metode Layanan & 3. Durasi Sewa -->
            <div class="ps-form-row-2">
                <div class="ps-field-group">
                    <span class="ps-field-label">Metode Layanan</span>
                    <div class="ps-dropdown" data-dropdown="service">
                        <button type="button" id="ps-dd-service-btn" class="ps-dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="ps-dd-trigger-text">
                                <strong id="ps-dd-service-title">Antar-Jemput Express</strong>
                                <small id="ps-dd-service-sub">Sewa bawa pulang (12 Jam – Harian)</small>
                            </span>
                            <svg class="ps-dd-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div id="ps-dd-service-menu" class="ps-dd-menu" role="listbox" hidden></div>
                    </div>
                </div>

                <div class="ps-field-group">
                    <span class="ps-field-label" id="ps-booking-unit-label">Durasi Sewa</span>
                    <div class="ps-dropdown" data-dropdown="duration">
                        <button type="button" id="ps-dd-duration-btn" class="ps-dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="ps-dd-trigger-text">
                                <strong id="ps-dd-duration-title">1 Hari (24 Jam)</strong>
                                <small id="ps-dd-duration-sub">Sewa harian penuh 24 jam</small>
                            </span>
                            <svg class="ps-dd-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div id="ps-dd-duration-menu" class="ps-dd-menu" role="listbox" hidden></div>
                    </div>
                </div>
            </div>

            <!-- 4. Jadwal Mulai -->
            <div class="ps-field-group">
                <span class="ps-field-label">Jadwal Mulai</span>
                <div class="ps-dropdown" data-dropdown="schedule">
                    <button type="button" id="ps-dd-schedule-btn" class="ps-dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ps-dd-trigger-text">
                            <strong id="ps-dd-schedule-title">Sekarang / Langsung Mulai</strong>
                            <small id="ps-dd-schedule-sub">Langsung disiapkan di tempat atau dikirim sekarang</small>
                        </span>
                        <svg class="ps-dd-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div id="ps-dd-schedule-menu" class="ps-dd-menu" role="listbox" hidden></div>
                </div>
            </div>

            <!-- 5. Request Game (Multi-Pilih Lengkap) -->
            <div class="ps-field-group">
                <span class="ps-field-label">Request Game <span style="font-weight:500;color:#94a3b8;">(bisa pilih lebih dari 1)</span></span>
                <div class="ps-dropdown" data-dropdown="games">
                    <button type="button" id="ps-dd-games-btn" class="ps-dd-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ps-dd-trigger-text">
                            <strong id="ps-dd-games-title">EA Sports FC 24, Spider-Man 2</strong>
                            <small id="ps-dd-games-sub">2 game dipilih • Klik untuk pilih atau ubah daftar game</small>
                        </span>
                        <svg class="ps-dd-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div id="ps-dd-games-menu" class="ps-dd-menu" role="listbox" aria-multiselectable="true" hidden></div>
                </div>
            </div>

            <div class="ps-modal-summary">
                <div>
                    <div style="font-size:11.5px;color:#cbd5e1;text-transform:uppercase;letter-spacing:0.06em;">Estimasi Total Biaya</div>
                    <div id="ps-booking-summary-note" style="font-size:12px;color:#94a3b8;">Sudah termasuk 2 stik &amp; kabel lengkap</div>
                </div>
                <strong id="ps-booking-total">Rp 150.000</strong>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
                <button type="button" class="ps-btn-outline" style="background:transparent;color:#e2e8f0;border-color:rgba(255,255,255,0.22);" data-close-modal>Batal</button>
                <button type="submit" class="ps-nav-cta" style="padding:11px 22px;border-radius:8px;">Konfirmasi Reservasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Unit Modal (Page Ke2) -->
<div id="ps-detail-modal" class="ps-modal-backdrop" hidden role="dialog" aria-modal="true" aria-labelledby="ps-detail-title">
    <div class="ps-modal-card">
        <div class="ps-modal-head">
            <div>
                <h3 id="ps-detail-title">Detail Unit</h3>
                <p id="ps-detail-meta" style="margin:4px 0 0;color:#38bdf8;font-size:13px;font-weight:600;"></p>
            </div>
            <button type="button" class="ps-modal-close" data-close-modal aria-label="Tutup modal">&times;</button>
        </div>
        <p id="ps-detail-specs" style="margin:0 0 14px;color:#cbd5e1;font-size:13.5px;line-height:1.6;"></p>
        <div style="font-size:11.5px;font-weight:800;letter-spacing:0.06em;color:#94a3b8;text-transform:uppercase;">Game Terinstal &amp; Siap Main:</div>
        <div id="ps-detail-games" class="ps-detail-tags"></div>
        <div class="ps-modal-summary" style="margin-bottom:16px;">
            <span style="font-size:13px;color:#cbd5e1;">Tarif Sewa</span>
            <strong id="ps-detail-rate">Rp 0 / jam</strong>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" class="ps-btn-outline" style="background:transparent;color:#e2e8f0;border-color:rgba(255,255,255,0.22);" data-close-modal>Tutup</button>
            <button id="ps-detail-book-btn" type="button" class="ps-nav-cta" style="padding:11px 22px;border-radius:8px;">Booking Unit Ini</button>
        </div>
    </div>
</div>

<!-- Legal / Support Info Modal -->
<div id="ps-info-modal" class="ps-modal-backdrop" hidden role="dialog" aria-modal="true" aria-labelledby="ps-info-title">
    <div class="ps-modal-card">
        <div class="ps-modal-head">
            <h3 id="ps-info-title">Informasi</h3>
            <button type="button" class="ps-modal-close" data-close-modal aria-label="Tutup modal">&times;</button>
        </div>
        <p id="ps-info-body" style="white-space:pre-line;color:#cbd5e1;font-size:13.5px;line-height:1.7;margin:0 0 18px;"></p>
        <div style="display:flex;justify-content:flex-end;">
            <button type="button" class="ps-nav-cta" data-close-modal style="padding:10px 20px;border-radius:8px;">Mengerti</button>
        </div>
    </div>
</div>

<div id="ps-toast" class="ps-toast" hidden role="status" aria-live="polite"></div>
