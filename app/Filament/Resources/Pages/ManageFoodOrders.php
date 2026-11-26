<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\FoodOrderResource;
use Filament\Resources\Pages\ManageRecords;

class ManageFoodOrders extends ManageRecords
{
    protected static string $resource = FoodOrderResource::class;
}
