<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Account;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    public function mount(): void
    {
        $purchaseId = request()->input('transaction_id');
        
        if ($purchaseId) {
            $this->form->fill([
                'transaction_id' => $purchaseId,
            ]);
        }
    }

    protected function afterCreate() : void
    {
        $payment = $this->record;

        $account = Account::find($payment->account_id);
        $account->balance -= $payment->amount;
        $account->save();
    }
}
