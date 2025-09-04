<?php

namespace App\Filament\Resources\TestStepResource\Pages;

use App\Filament\Resources\TestStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestSteps extends ListRecords
{
    protected static string $resource = TestStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
