<?php

namespace App\Filament\Resources\WriteoffResource\Pages;

use App\Filament\Resources\WriteoffResource;
use App\Models\Writeoff;
use Auth;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageWriteoffs extends ManageRecords
{
    protected static string $resource = WriteoffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
