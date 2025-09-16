<?php

namespace App\Filament\Resources\Feedback\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Feedback\FeedbackResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFeedback extends ManageRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
