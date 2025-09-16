<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubscriptions extends ManageRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
