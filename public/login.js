const form = document.querySelector('.login-form');
const password = document.querySelector('#password');
const toggle = document.querySelector('.password-toggle');
const submit = form.querySelector('[type="submit"]');
const submitLabel = form.dataset.submitLabel || submit.textContent;
const status = document.querySelector('#verification-status');
const retry = document.querySelector('#verification-retry');
const widget = document.querySelector('#turnstile-widget');
let ready = !widget;
let verificationTimer;
let verificationRequested = false;
let widgetRendered = false;
const startButton = document.querySelector('#verification-start');
function startVerification() {
    if (!widget || verificationRequested) return;
    verificationRequested = true;
    if (startButton) startButton.hidden = true;
    showVerification('Menyiapkan verifikasi keamanan…', false);
    clearTimeout(verificationTimer);
    verificationTimer = setTimeout(() => {
        if (!ready) showVerification('Verifikasi belum merespons. Coba ulangi verifikasi.', false, true);
    }, 20000);
    const script = document.createElement('script');
    script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit';
    script.async = true;
    script.id = 'turnstile-api';
    script.onerror = () => {
        clearTimeout(verificationTimer);
        verificationRequested = false;
        script.remove();
        showVerification('Verifikasi gagal dimuat. Periksa koneksi lalu coba lagi.', false, true);
    };
    document.head.append(script);
}
startButton?.addEventListener('click', startVerification);
if (widget) {
    form.addEventListener('focusin', startVerification, { once: true });
    form.addEventListener('input', startVerification, { once: true });
}

function showVerification(message, verified, canRetry = false) {
    ready = verified;
    if (status) status.textContent = message;
    if (retry) retry.hidden = !canRetry;
    submit.disabled = !verified;
}

window.onTurnstileSuccess = () => {
    clearTimeout(verificationTimer);
    showVerification('Verifikasi berhasil. Kamu bisa melanjutkan.', true);
};
window.onTurnstileError = (errorCode) => {
    clearTimeout(verificationTimer);
    const code = String(errorCode ?? '');
    let message = 'Verifikasi Cloudflare gagal. Periksa koneksi dan coba lagi.';
    if (code === '110200') {
        message = 'Alamat situs ini belum diizinkan. Tambahkan ' + location.hostname + ' di Hostname Management widget Cloudflare, lalu simpan.';
    } else if (['110100', '110110', '400020', '400070'].includes(code)) {
        message = 'Site Key Cloudflare tidak valid atau tidak aktif. Periksa pengaturan widget dan Site Key aplikasi.';
    } else if (code === '200500') {
        message = 'Browser tidak dapat memuat Cloudflare. Periksa apakah challenges.cloudflare.com diblokir oleh ekstensi atau jaringan.';
    } else if (code === '200100' || code === '110600') {
        message = 'Periksa tanggal dan waktu perangkat, lalu muat ulang halaman untuk memperbarui verifikasi.';
    }
    if (/^\d+$/.test(code)) message += ' (Kode: ' + code + ')';
    showVerification(message, false, true);
    return true;
};
window.onTurnstileExpired = () => {
    showVerification('Verifikasi kedaluwarsa. Verifikasi ulang untuk melanjutkan.', false, true);
};
window.onTurnstileLoad = () => {
    if (!widget || widgetRendered) return;
    widgetRendered = true;
    window.turnstile.render(widget, {
        sitekey: widget.dataset.sitekey,
        theme: 'light',
        size: 'flexible',
        callback: window.onTurnstileSuccess,
        'error-callback': window.onTurnstileError,
        'expired-callback': window.onTurnstileExpired,
        'timeout-callback': window.onTurnstileExpired,
        'unsupported-callback': window.onTurnstileError,
    });
};

retry?.addEventListener('click', () => {
    if (window.turnstile) {
        showVerification('Memverifikasi ulang…', false);
        window.turnstile.reset();
    } else {
        verificationRequested = false;
        document.querySelector('#turnstile-api')?.remove();
        startVerification();
    }
});
form.addEventListener('submit', event => {
    const token = form.querySelector('[name="cf-turnstile-response"]');
    if (widget && (!ready || !token?.value)) {
        event.preventDefault();
        startVerification();
        showVerification('Selesaikan verifikasi Cloudflare terlebih dahulu.', false, true);
        return;
    }
    submit.disabled = true;
    submit.textContent = 'MEMPROSES…';
});
window.addEventListener('pageshow', event => {
    if (!event.persisted) return;
    submit.textContent = submitLabel;
    if (widget && window.turnstile) {
        showVerification('Memperbarui verifikasi…', false);
        window.turnstile.reset();
    } else {
        submit.disabled = false;
    }
});
toggle?.addEventListener('click', () => {
    const visible = password.type === 'password';
    password.type = visible ? 'text' : 'password';
    toggle.setAttribute('aria-pressed', String(visible));
    toggle.setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
});
document.querySelectorAll('[data-login]').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelector('#email').value = button.dataset.login + '@psseleraku.test';
        password.value = button.dataset.password;
        document.querySelector('#email').focus();
    });
});
document.querySelectorAll('[data-open-dialog]').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById(button.dataset.openDialog).showModal();
    });
});
document.querySelectorAll('[data-close-dialog]').forEach(button => {
    button.addEventListener('click', () => button.closest('dialog').close());
});
