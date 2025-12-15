<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pharmacy;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Redis;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PharmacyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PharmacyResource\RelationManagers;
use Illuminate\Database\Eloquent\Collection; 
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
class PharmacyResource extends Resource
{
    protected static ?string $model = Pharmacy::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Define form fields here
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // --- SORTING BY REDIS SCORE (Most Popular First) ---
            ->modifyQueryUsing(function (Builder $query) {
                
                // 1. Get ALL pharmacy IDs, sorted by the Redis score (highest score first)
                $sortedMembers = Redis::zrevrange('pharmacy:leaderboard', 0, -1);
                
                // 2. Clean the IDs to get a pure array of integers
                $sortedIds = collect($sortedMembers)
                    ->map(function ($id) {
                        // Use string replacement to reliably strip 'pharmacy:'
                        return (int) str_replace('pharmacy:', '', $id); 
                    })
                    ->filter()
                    ->toArray();
                
                // If the leaderboard is empty, return the query as is (default sort)
                if (empty($sortedIds)) {
                    return $query;
                }

                // 3. Use the FIELD() function in SQL to enforce the Redis order
                $sortedIdsString = implode(',', $sortedIds);

                $query->whereIn('id', $sortedIds)
                      ->orderByRaw("FIELD(id, {$sortedIdsString})");

                return $query;
            })
            // --- END SORTING ---
            
            ->columns([
                // ESSENTIAL: Basic Pharmacy Information
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Pharmacy Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Location')
                    ->sortable(),

                // LEADERBOARD SCORE: Custom calculated column from Redis
                TextColumn::make('leaderboard_score')
                    ->label('Leaderboard Score')
                    ->alignCenter()
                    ->sortable(false) 
                    ->searchable(false) 
                    ->color('success')
                    ->getStateUsing(function (Model $record): int {
                        $memberKey = 'pharmacy:' . $record->id;
                        $score = Redis::zscore('pharmacy:leaderboard', $memberKey) ?? 0;
                        return (int) $score;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // The 'Edit' button remains, allowing modification of existing pharmacies
            ])
            ->bulkActions([
               // --- CUSTOM BULK ACTION: REMOVE FROM LEADERBOARD ---
                    Tables\Actions\BulkAction::make('clear_leaderboard_cache')
                        ->label('Remove from score counter') 
                        ->icon('heroicon-o-archive-box-x-mark') 
                        ->color('warning')
                        ->requiresConfirmation()
                        
                        ->action(function (Collection $records) {
                            
                            $removedCount = 0;

                            foreach ($records as $pharmacy) {
                                // 1. Remove the specific score from the Redis Sorted Set
                                Redis::zrem('pharmacy:leaderboard', 'pharmacy:' . $pharmacy->id);
                                $removedCount++;
                            }

                            // 2. Clear Critical Cache Keys
                            // Cache::forget('products:sorted:popularity'); 
                            // Cache::forget('pharmacies');                
                            // Cache::forget('locations');                 

                            // 3. Notify the admin
                            Notification::make()
                                ->title('Leaderboard Cleanup Complete')
                                ->body("Successfully removed {$removedCount} pharmacies from the leaderboard and cleared related caches.")
                                ->success()
                                ->send();

                        }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // --- MODIFICATION HERE ---
    public static function getPages(): array
    {
        return [
            // This is the default List page
            'index' => Pages\ListPharmacies::route('/'),
            
            // REMOVED: 'create' => Pages\CreatePharmacy::route('/create'),
            // By commenting out or deleting the 'create' route, the button is hidden.
            
            // This allows editing existing pharmacies
            'edit' => Pages\EditPharmacy::route('/{record}/edit'),
        ];
    }
}