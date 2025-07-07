<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use App\Models\Account;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    protected function afterCreate(): void
    {
        $deposit = $this->record;
        
        $account = Account::find($deposit->account_id);
        $account->balance += $deposit->amount;
        $account->save();
    }
}
