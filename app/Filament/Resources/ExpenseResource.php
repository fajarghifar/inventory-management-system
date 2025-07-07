<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Account;
use App\Models\Expense;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Accounts';

    public static function form(Form $form): Form
    {
        $isCreate = $form->getOperation() === "create";

        return $form
            ->schema([
                Section::make([
                    DatePicker::make('expense_date')
                        ->label('Date')
                        ->required(),
                    Select::make('account_id')
                        ->relationship(name: 'account', titleAttribute: 'name')
                        ->required()
                ])->columns(2),
                Section::make([
                    Select::make('expense_category_id')
                        ->relationship(name: 'expenseCategory', titleAttribute: 'name')
                        ->required(),
                    Select::make('payment_method_id')
                        ->relationship(name: 'paymentMethod', titleAttribute: 'name')
                        ->required(),
                    TextInput::make('amount')
                        ->rules([
                            fn (Get $get, string $operation, ?Model $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $operation, $record) {
                                $account_id = $get('account_id');
                                if ($account_id) {
                                    $account = Account::find($account_id);
                                    $balance = 0;
                                    if ($operation == 'create')
                                        $balance = $account->balance;
                                    else
                                        $balance = $account->balance + Expense::where('id', $record->id)->value('amount');
                                    if ($balance < $value) {
                                        $fail("You do not have suffcient balance in your account");
                                    }
                                }
                            },
                        ])
                        ->required()
                        ->numeric(),
                ])->columns(3),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expenseCategory.name')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                TextColumn::make('paymentMethod.name'),
                TextColumn::make('description'),

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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
