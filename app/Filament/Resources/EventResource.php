<?php

namespace App\Filament\Resources;

use App\Enums\EventType;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }

    public static function getNavigationLabel(): string
    {
        return __('Events');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->translateLabel(),
                Select::make('type')->options(EventType::class)->native(false),
                DateTimePicker::make('datetime')->seconds(false)
                ->native(false)
                ->translateLabel(),
                DateTimePicker::make('end_time')->seconds(false)
                ->native(false)
                ->translateLabel(),
                Textarea::make('comment')->translateLabel()->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->translateLabel(),
                TextColumn::make('type')->label(__('Type'))->badge(),
                TextColumn::make('datetime')->label('Day')->since()
                ->badge()
                ->size('sm')
                ->description(fn (Event $record): string => $record->eventTime),
            ])->defaultSort('datetime', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\CalendarWidget::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEvents::route('/'),
        ];
    }
}
