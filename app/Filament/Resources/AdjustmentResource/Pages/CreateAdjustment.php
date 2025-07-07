<?php

namespace App\Filament\Resources\AdjustmentResource\Pages;

use App\Filament\Resources\AdjustmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdjustment extends CreateRecord
{
    protected static string $resource = AdjustmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
