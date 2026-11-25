<x-filament-panels::page>
    <p>Perubahan di sini langsung dipakai beranda, halaman Tentang, Kontak, footer, dan jawaban informasi Selo. Konsol, game, tarif, dan stok tetap dikelola di menu Katalog.</p>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="save">Simpan perubahan</x-filament::button>
    </form>
</x-filament-panels::page>
