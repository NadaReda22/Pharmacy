<?php

namespace App\Filament\Resources\ProductNotificationResource\Pages;

use App\Filament\Resources\ProductNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductNotification extends EditRecord
{
    protected static string $resource = ProductNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
