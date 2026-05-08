<?php

namespace Modules\Finance\Filament\Admin\Resources\Subscriptions;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Finance\Filament\Admin\Resources\Subscriptions\Pages\ManageSubscriptions;
use Modules\Finance\Filament\Admin\Resources\Subscriptions\Schemas\SubscriptionForm;
use Modules\Finance\Filament\Admin\Resources\Subscriptions\Tables\SubscriptionTable;
use Modules\Finance\Models\Subscription;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getPluralModelLabel(): string
    {
        return __('Subscriptions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Subscriptions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                SubscriptionForm::configure($schema)
            );
    }

    public static function table(Table $table): Table
    {
        return SubscriptionTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubscriptions::route('/'),
        ];
    }
}
