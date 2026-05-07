<?php

namespace Modules\Events\Filament\Admin\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Events\Enums\EventType;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->translateLabel(),
                Select::make('type')->options(EventType::class)->native(false),
                DateTimePicker::make('datetime')->seconds(false)
                    ->native(false)
                    ->minDate(now())
                    ->default(now())
                    ->translateLabel()
                    ->live()
                    ->afterStateUpdated(
                        function (Set $set, Get $get) {
                            $set('end_time', $get('datetime'));
                        }
                    ),
                TimePicker::make('end_time')->seconds(false)
                    ->native(false)
                    ->default(now())
                    ->translateLabel(),
                Textarea::make('comment')->translateLabel()->columns(1),
            ]);
    }
}
