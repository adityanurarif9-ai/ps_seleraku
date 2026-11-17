<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CatalogItemResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCatalogItems extends ManageRecords
{
    protected static string $resource = CatalogItemResource::class;
}