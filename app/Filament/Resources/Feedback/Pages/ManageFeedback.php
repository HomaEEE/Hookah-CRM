<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use App\Filament\Resources\FeedbackResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFeedback extends ManageRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
