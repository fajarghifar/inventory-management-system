<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdjustmentResource\Pages;
use App\Models\Adjustment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdjustmentResource extends Resource
{
    protected static ?string $model = Adjustment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationGroup = 'Product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('adjustment_date')
                    ->label('Date')
                    ->required(),

                Select::make('warehouse_id')
                    ->relationship(name: 'warehouse', titleAttribute: 'name')
                    ->required(),
                Repeater::make('adjustmentProducts')
                    ->label('Products')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->relationship(name: 'product', titleAttribute: 'name')
                            ->required()->columnSpan(2),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric(),
                        Select::make('type')
                            ->options([
                                'addition' => 'Addition',
                                'subtraction' => 'Subtraction',
                            ])
                            ->required(),
                    ])
                    ->addActionLabel('Add Product')
                    ->columns(4)
                    ->columnSpanFull(),
                Textarea::make('reason')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adjustment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('adjustmentProducts.product.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('adjustmentProducts.quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('adjustmentProducts.type')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListAdjustments::route('/'),
            'create' => Pages\CreateAdjustment::route('/create'),
            'edit' => Pages\EditAdjustment::route('/{record}/edit'),
        ];
    }
}
