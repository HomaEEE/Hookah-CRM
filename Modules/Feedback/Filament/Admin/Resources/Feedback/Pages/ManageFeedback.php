<?php

namespace Modules\Feedback\Filament\Admin\Resources\Feedback\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Feedback\Filament\Admin\Resources\Feedback\FeedbackResource;

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
