<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Include notifiedUsers count in all queries.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('notifiedUsers'); // adds notified_users_count
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required(),

            Forms\Components\Textarea::make('description'),

            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->required(),

            // Forms\Components\FileUpload::make('image')
            //     ->image()
            //     ->directory('products'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('quantity'),
                // ImageColumn::make('image'),
                TextColumn::make('notified_users_count')
                    ->label('Notifications')
                    ->sortable(),
            ])
            ->filters([
                // Only min_notifications filter remains
                Filter::make('min_notifications')
                    ->form([
                        Forms\Components\TextInput::make('count')
                            ->numeric()
                            ->label('Minimum Notifications'),
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $query->has('notifiedUsers', '>=', $data['count'] ?? 0)
                    ),
            ])
            ->defaultSort('notified_users_count', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(), // only edit action remains
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
            'index' => Pages\ListProducts::route('/'),
            // 'create' page removed
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
