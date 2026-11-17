document.querySelectorAll('[data-login]').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelector('#email').value = button.dataset.login + '@psseleraku.test';
        document.querySelector('#password').value = button.dataset.password;
        document.querySelector('.fields button').focus();
    });
});

const filters = document.querySelectorAll('[data-filter]');
const items = document.querySelectorAll('[data-category]');
filters.forEach(button => {
    button.addEventListener('click', () => {
        filters.forEach(filter => filter.setAttribute('aria-pressed', String(filter === button)));
        items.forEach(item => {
            item.hidden = button.dataset.filter !== 'Semua' && item.dataset.category !== button.dataset.filter;
        });
        document.querySelector('#empty-menu').hidden = Array.from(items).some(item => !item.hidden);
    });
});

const unit = document.querySelector('#unit');
if (unit) {
    const hours = document.querySelector('#hours');
    const money = new Intl.NumberFormat('id-ID');
    const update = () => {
        document.querySelector('#total').textContent = 'Rp' + money.format(unit.selectedOptions[0].dataset.rate * hours.value);
    };
    unit.addEventListener('change', update);
    hours.addEventListener('change', update);
    document.querySelectorAll('[data-unit]').forEach(button => {
        button.addEventListener('click', () => {
            unit.value = button.dataset.unit;
            update();
            unit.focus();
        });
    });
    update();
}