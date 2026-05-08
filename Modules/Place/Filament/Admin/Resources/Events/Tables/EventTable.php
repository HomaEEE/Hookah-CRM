<?php

namespace Modules\Place\Filament\Admin\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Place\Models\Event;

class EventTable
{
    public static function configure(Table $table): Table
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
            ->recordActions([
                ReplicateAction::make()
                    ->schema([
                        DateTimePicker::make('datetime')->seconds(false)
                            ->native(false)
                            ->minDate(now())->default(now())
                            ->translateLabel(),
                        TimePicker::make('end_time')->seconds(false)
                            ->native(false)
                            ->minDate(now())->default(now()->subHours(2))
                            ->translateLabel(),
                    ]),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
