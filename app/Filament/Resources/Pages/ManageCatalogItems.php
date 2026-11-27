<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CatalogItemResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCatalogItems extends ManageRecords
{
    public function getSubheading(): ?string
    {
        return 'Tambah, edit, atau hapus produk. Perubahan langsung tampil di website. Produk yang punya riwayat transaksi dapat disembunyikan lewat Edit agar riwayat tetap tersimpan.';
    }

    protected static string $resource = CatalogItemResource::class;
}
