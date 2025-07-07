<?php

namespace App\Filament\Resources\DepositCategoryResource\Pages;

use App\Filament\Resources\DepositCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepositCategory extends EditRecord
{
    protected static string $resource = DepositCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
