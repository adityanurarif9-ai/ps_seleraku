const form = document.querySelector('.login-form');
const password = document.querySelector('#password');
const toggle = document.querySelector('.password-toggle');
const submit = document.querySelector('.submit');
const status = document.querySelector('#verification-status');
const retry = document.querySelector('#verification-retry');
const widget = document.querySelector('#turnstile-widget');
let ready = !widget;
let verificationTimer;

function showVerification(message, verified, canRetry = false) {
    ready = verified;
    if (status) status.textContent = message;
    if (retry) retry.hidden = !canRetry;
    submit.disabled = !verified;
}

window.onTurnstileSuccess = () => {
    clearTimeout(verificationTimer);
    showVerification('Verifikasi berhasil. Kamu bisa masuk.', true);
};
window.onTurnstileError = () => {
    clearTimeout(verificationTimer);
    showVerification('Cloudflare gagal dimuat. Periksa koneksi atau coba buka di Chrome / Edge.', false, true);
    return true;
};
window.onTurnstileExpired = () => {
    showVerification('Verifikasi kedaluwarsa. Verifikasi ulang untuk masuk.', false, true);
};
window.onTurnstileLoad = () => {
    if (!widget) return;
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

if (widget) {
    showVerification('Menyiapkan verifikasi Cloudflare…', false);
    verificationTimer = setTimeout(() => {
        if (!ready) showVerification('Cloudflare belum merespons. Coba ulangi verifikasi.', false, true);
    }, 20000);
}
retry?.addEventListener('click', () => {
    if (window.turnstile) {
        showVerification('Memverifikasi ulang…', false);
        window.turnstile.reset();
    } else {
        location.reload();
    }
});
form.addEventListener('submit', event => {
    const token = form.querySelector('[name="cf-turnstile-response"]');
    if (widget && (!ready || !token?.value)) {
        event.preventDefault();
        showVerification('Selesaikan verifikasi Cloudflare terlebih dahulu.', false, true);
        return;
    }
    submit.disabled = true;
    submit.textContent = 'MEMPROSES…';
});
window.addEventListener('pageshow', event => {
    if (!event.persisted) return;
    submit.textContent = 'MASUK SEKARANG';
    if (widget && window.turnstile) {
        showVerification('Memperbarui verifikasi…', false);
        window.turnstile.reset();
    } else {
        submit.disabled = false;
    }
});
toggle.addEventListener('click', () => {
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
