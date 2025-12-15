<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductNotificationResource\Pages;
use App\Filament\Resources\ProductNotificationResource\RelationManagers;
use App\Models\ProductNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductNotificationResource extends Resource
{
    protected static ?string $model = ProductNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
       
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('notified')->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->filters([
                // Filter by product
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name'),

                // Most requested products
                Tables\Filters\Filter::make('most_requested')
                    ->query(fn ($q) =>
                        $q->selectRaw('product_id, COUNT(*) as requests')
                          ->groupBy('product_id')
                          ->orderByDesc('requests')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductNotifications::route('/'),
            'create' => Pages\CreateProductNotification::route('/create'),
            'edit' => Pages\EditProductNotification::route('/{record}/edit'),
        ];
    }
}
