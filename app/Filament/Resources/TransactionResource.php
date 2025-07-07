<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use stdClass;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-bangladeshi';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Purchase';

    protected static ?string $navigationGroup = 'Billing & Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                DateTimePicker::make('transact_at')
                    ->label('Date')
                    ->required(),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required(),



                Repeater::make('productTransactions')
                    ->label('Products')
                    ->relationship()
                    ->schema([
                        Split::make([
                            Section::make([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->live(),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('unit_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                            Section::make([
                                Repeater::make('attributeValueProductTransactions')
                                    ->label('')
                                    ->relationship()
                                    ->schema([
                                        Select::make('attribute_value_id')
                                            ->label('Attributes')
                                            ->options(function (Get $get) {
                                                $product_id = $get('../../product_id');
                                                if ($product_id) {
                                                    return Product::find($product_id)
                                                        ->attributeProducts()
                                                        ->with('attributeValue:id,value_name')
                                                        ->get()
                                                        ->pluck('attributeValue.value_name', 'attributeValue.id');
                                                }
                                            })
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Attribute'),
                            ])
                        ])


                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        $product = Product::find($data['product_id']);

                        $product->quantity = $product->quantity + $data['quantity'];

                        $product->save();

                        return $data;
                    })
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    })
                    ->columnSpanFull()
                    ->addActionLabel('Add Product'),

                Section::make([
                    TextInput::make('delivery_cost')
                        ->numeric()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateTotals($get, $set);
                        })->default(0),
                    TextInput::make('discount')
                        ->numeric()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateTotals($get, $set);
                        })->default(0),
                    TextInput::make('total')
                        ->numeric()
                        ->readOnly()
                        ->prefix('$')
                        ->afterStateHydrated(function (Get $get, Set $set) {
                            self::updateTotals($get, $set);
                        }),
                ])->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                ColumnGroup::make('Product', [
                    TextColumn::make('productTransactions.product.name')
                        ->label('Name')
                        ->listWithLineBreaks(),
                    TextColumn::make('productTransactions.quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->listWithLineBreaks(),
                    TextColumn::make('productTransactions.product.unitType.name')
                        ->label('Unit')
                        ->listWithLineBreaks(),
                    TextColumn::make('productTransactions.AttributeValueProductTransactions.attributeValue.value_name')
                        ->label('Attribute')
                        ->listWithLineBreaks(),
                ])->alignCenter(),
                TextColumn::make('paid')
                    ->label('Paid')
                    ->state(fn (Model $record) => $record->payments->sum('amount'))
                    ->numeric(),
                TextColumn::make('due')
                    ->label('Due')
                    ->state(fn (Model $record) => $record->total - $record->payments->sum('amount'))
                    ->numeric(),
                TextColumn::make('total')
                    ->label('Grand Total')
                    ->numeric()
                    ->copyable()
                    ->summarize(Sum::make()->label('Total Purchase Amount')),
                TextColumn::make('transact_at')
                    ->label('Purchase Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])->defaultSort('transact_at', 'desc')
            ->filters([
                Filter::make('transact_at')
                    ->form([
                        DatePicker::make('purchase_from'),
                        DatePicker::make('purchase_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchase_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transact_at', '>=', $date),
                            )
                            ->when(
                                $data['purchase_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transact_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('product')
                    ->relationship('productTransactions.product', 'name')
                    ->multiple(),
            ], layout: FiltersLayout::Modal)
            ->actions([

                Action::make('pay')
                    ->url(fn (Transaction $record): string => PaymentResource::getUrl('create', ['transaction_id' => $record->id]))
                    ->openUrlInNewTab()
                    ->hidden(fn (Model $record) => $record->total - $record->payments->sum('amount') == 0)
                    ->icon('heroicon-m-currency-bangladeshi'),
                EditAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('productTransactions'))->filter(fn ($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ($product['unit_price'] * $product['quantity']);
        }, 0);

        $set('total', number_format($subtotal + $get('delivery_cost') - $get('discount'), 2, '.', ''));
    }
}
