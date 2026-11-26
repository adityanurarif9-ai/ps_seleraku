<form method="post" action="{{ route('language.update') }}" class="ps-language" aria-label="{{ app()->getLocale() === 'en' ? 'Choose language' : 'Pilih bahasa' }}">
    @csrf
    <button type="submit" name="locale" value="id" lang="id" aria-label="Bahasa Indonesia" aria-pressed="{{ app()->getLocale() === 'id' ? 'true' : 'false' }}">ID</button>
    <span aria-hidden="true">/</span>
    <button type="submit" name="locale" value="en" lang="en" aria-label="English" aria-pressed="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}">EN</button>
</form>
