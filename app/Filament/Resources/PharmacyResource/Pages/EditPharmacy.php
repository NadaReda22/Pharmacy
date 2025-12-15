<?php

namespace App\Filament\Resources\PharmacyResource\Pages;

use App\Filament\Resources\PharmacyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification; // <-- ADD THIS for user feedback

class EditPharmacy extends EditRecord
{
    protected static string $resource = PharmacyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. remove the  DeleteAction.
            // Actions\DeleteAction::make(), 

            // 2. Add a Custom Action to clear the leaderboard and cache.
            Actions\Action::make('clearLeaderboardCache')
                ->label('Remove from Leaderboard / Clear Cache') // Descriptive button label
                ->icon('heroicon-o-trash') // Use an icon for clarity
                ->color('warning')
                ->requiresConfirmation() // Ask the admin "Are you sure?"
                ->action(function () {
                    
                    $pharmacy = $this->getRecord();
                    
                    // --- Cleanup Logic (No database deletion occurs here) ---
                    
                    // 1. Clear the specific pharmacy's score from the leaderboard
                    // This command removes the member from the Sorted Set.
                    Redis::zrem('pharmacy:leaderboard', 'pharmacy:' . $pharmacy->id);

                  
                    
                    // --- Confirmation Feedback ---
                    Notification::make()
                        ->title('Success')
                        ->body("Pharmacy '{$pharmacy->name}' has been successfully removed from the Leaderboard and Caches.")
                        ->success()
                        ->send();
                }),
        ];
    }
}