<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // Redirect ke list akun setelah create
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
