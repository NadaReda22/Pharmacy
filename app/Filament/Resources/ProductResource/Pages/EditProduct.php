<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;
use App\Jobs\SendProductAvailableNotification;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];

        
    }
        /**
     * This is called BEFORE saving the form data.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldStock = $this->record->quantity;  // quantity before update
        $newStock = (int) $data['quantity'];  // new quantity from form

        // Dispatch job if product was out-of-stock and now in-stock
        if ($oldStock <= 0 && $newStock > 0) {
            SendProductAvailableNotification::dispatch($this->record->id);
        }

        return $data; // continue with saving
    }
}
