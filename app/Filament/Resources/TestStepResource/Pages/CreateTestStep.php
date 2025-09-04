<?php

namespace App\Filament\Resources\TestStepResource\Pages;

use App\Filament\Resources\TestStepResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestStep extends CreateRecord
{
    protected static string $resource = TestStepResource::class;
}
