<?php

namespace App\Filament\Resources\ProductNotificationResource\Pages;

use App\Filament\Resources\ProductNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductNotifications extends ListRecords
{
    protected static string $resource = ProductNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
