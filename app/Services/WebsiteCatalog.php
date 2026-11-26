<?php

namespace App\Services;

use App\Models\CatalogItem;

class WebsiteCatalog
{
    public function data(): array
    {
        $items = CatalogItem::where('is_visible', true)->orderBy('id')->get();
        $classicUnits = [];
        $proUnits = [];
        $dailyUnits = [];
        $packages = [];
        $fnbItems = [];
        foreach ($items as $item) {
            $money = fn ($value) => 'Rp '.number_format($value, 0, ',', '.');
            if ($item->category !== 'Konsol') {
                $category = match ($item->category) {
                    'Makanan' => 'makanan-berat','Minuman' => 'minuman',default => 'camilan'
                };
                $fnbItems[] = ['id' => 'menu-'.$item->id, 'name' => $item->name, 'categories' => $category, 'badge' => $item->stock > 0 ? $item->category : 'Stok habis', 'badgeClass' => 'is-blue', 'rating' => null, 'image' => $item->image(), 'desc' => $item->description ?? 'Pilihan menu PS Seleraku.', 'price' => $item->price, 'priceText' => $money($item->price), 'search' => strtolower($item->name.' '.$item->description.' '.$category), 'stock' => $item->stock];

                continue;
            }
            $free = app(RentalAvailability::class)->available($item, now(), now()->addHour());
            $available = $free > 0;
            $platform = $item->platformKey();
            $status = $available ? 'available' : ($item->stock === 0 ? 'maintenance' : 'in-use');
            $label = $available ? __(':free dari :total unit tersedia', ['free'=>$free, 'total'=>$item->stock]) : ($item->stock === 0 ? 'Belum tersedia' : 'Penuh untuk 1 jam ke depan');
            $id = 'catalog-'.$item->id;
            $desc = $item->description ?? 'Pilih jadwal dan durasi pada formulir reservasi.';
            $games = $item->games ?? [];
            $base = ['id' => $id, 'stock' => $item->stock, 'platformKey' => $platform, 'platformLabel' => $item->name, 'statusKey' => $status, 'statusLabel' => $label, 'image' => $item->image(), 'name' => $item->name, 'rate' => $item->price, 'rateText' => $money($item->price), 'specFull' => $desc, 'allGames' => $games];
            if (in_array($platform, ['ps3', 'nintendo'])) {
                $classicUnits[] = $base + ['statusClass' => $available ? 'status-available' : 'status-rented', 'dotColor' => $available ? '#10b981' : '#64748b', 'muted' => ! $available, 'note' => $available ? null : 'Cek jadwal lain melalui reservasi.', 'noteType' => 'time', 'desc' => $desc, 'footerLabel' => $label, 'footerIcon' => $available ? 'check' : 'lock', 'btnText' => 'Pilih jadwal →', 'btnClass' => 'ps-btn-black', 'defaultSchedule' => 'now'];
            } else {
                $proUnits[] = $base + ['code' => $item->stock.' unit total', 'spec' => $desc, 'gamesHeading' => 'PILIHAN GAME:', 'tags' => array_slice($games, 0, 4), 'moreTag' => count($games) > 4 ? '+'.(count($games) - 4).' game' : '', 'ctaText' => 'Pilih jadwal', 'ctaClass' => 'ps-btn-booking', 'ctaIcon' => 'calendar'];
            }
            if ($item->daily_price !== null) {
                $dailyFree = app(RentalAvailability::class)->available($item, now(), now()->addDay());
                $dailyUnits[] = ['itemId' => $id, 'stock' => $item->stock, 'tag' => $item->name, 'platformKey' => $platform, 'statusKey' => $dailyFree > 0 ? 'available' : 'in-use', 'statusLabel' => $dailyFree > 0 ? __(':count unit tersedia', ['count'=>$dailyFree]) : 'Pilih jadwal lain', 'statusClass' => $dailyFree > 0 ? 'is-available' : 'is-rented', 'rate' => $item->daily_price, 'rateText' => $money($item->daily_price), 'image' => $item->image(), 'imgBadge' => null, 'overlayNote' => null, 'name' => $item->name, 'desc' => $desc, 'chips' => array_slice($games, 0, 2), 'ctaText' => 'Pilih jadwal harian', 'ctaIcon' => 'bag', 'defaultSchedule' => 'now', 'searchText' => strtolower($item->name.' '.$desc.' '.implode(' ', $games))];
                $packages[] = ['id' => 'paket-catalog-'.$item->id, 'image' => $item->image(), 'pill' => 'SEWA HARIAN', 'pillClass' => '', 'featured' => false, 'ribbon' => null, 'name' => __('SEWA').' '.mb_strtoupper($item->name), 'desc' => $desc, 'price' => $money($item->daily_price), 'rawPrice' => $item->daily_price, 'unit' => '/ 24 jam', 'unitType' => 'Hari', 'btnClass' => '', 'features' => ['Tarif harian dari katalog admin', $item->stock.' unit total', 'Jadwal dikonfirmasi pengelola', 'Pantau status di Rental Saya']];
            }
        }

        foreach ([&$classicUnits, &$proUnits, &$dailyUnits, &$packages, &$fnbItems] as &$group) {
            foreach ($group as &$entry) {
                foreach (['statusLabel', 'btnText', 'ctaText', 'gamesHeading', 'badge', 'pill', 'unitType', 'unit', 'note'] as $key) {
                    if (isset($entry[$key])) $entry[$key] = __($entry[$key]);
                }
            }
            unset($entry);
        }
        unset($group);
        return compact('classicUnits', 'proUnits', 'dailyUnits', 'packages', 'fnbItems');
    }
}
