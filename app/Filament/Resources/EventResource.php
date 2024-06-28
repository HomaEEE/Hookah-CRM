<?php

namespace App\Filament\Resources;

use App\Enums\EventType;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Carbon\Carbon;
use Filament\Forms\Components\{DateTimePicker,TimePicker,Select,Textarea, TextInput};
use Filament\Forms\{Set, Get, Form};
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

    public static function getNavigationBadge(): ?string
    {
        return Event::query()->whereDate('datetime', '>=', Carbon::today())->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->translateLabel(),
                Select::make('type')->options(EventType::class)->native(false),
                DateTimePicker::make('datetime')->seconds(false)
                ->native(false)
                ->minDate(now())
                ->default(now())
                ->translateLabel()
                ->reactive()->afterStateUpdated(
                    function (Set $set, Get $get) {
                        $set('end_time', $get('datetime'));
                    }
                ),
                TimePicker::make('end_time')->seconds(false)
                ->native(false)
                ->default(now())
                ->translateLabel(),
                Textarea::make('comment')->translateLabel()->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label(__('Type'))->badge()
                ->description(fn (Event $record): string => $record->title, position: 'above'),
                TextColumn::make('datetime')->label('Day')->since()
                ->badge()
                ->description(fn (Event $record): string => $record->eventTime, position: 'above'),
            ])->defaultSort('datetime', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                ->form([
                    DateTimePicker::make('datetime')->seconds(false)
                    ->native(false)
                    ->minDate(now())->default(now())
                    ->translateLabel(),
                    TimePicker::make('end_time')->seconds(false)
                    ->native(false)
                    ->minDate(now())->default(now()->subHours(2))
                    ->translateLabel(),
                ]),
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
