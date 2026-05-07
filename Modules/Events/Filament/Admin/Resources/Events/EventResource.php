<?php

namespace Modules\Events\Filament\Admin\Resources\Events;

use App\Enums\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Events\Filament\Admin\Resources\Events\Pages\ManageEvents;
use Modules\Events\Filament\Admin\Resources\Events\Schemas\EventForm;
use Modules\Events\Filament\Admin\Resources\Events\Tables\EventTable;
use Modules\Events\Models\Event;
use UnitEnum;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Place;

    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }

    public static function getNavigationLabel(): string
    {
        return __('Events');
    }

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventTable::configure($table);
    }

    public static function getHeaderWidgets(): array
    {
        return [
            // \App\Filament\Widgets\CalendarWidget::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEvents::route('/'),
        ];
    }
}
