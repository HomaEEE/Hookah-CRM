<?php

namespace Modules\Finance\Filament\Admin\Resources\Subscriptions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Finance\Filament\Admin\Resources\Subscriptions\SubscriptionResource;

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
