<?php

namespace App\Filament\Resources\Shifts\Pages;

use App\Filament\Resources\Shifts\ShiftResource;
use App\Models\Shift;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;

class EditShift extends EditRecord
{
    protected static string $resource = ShiftResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('shift')->translateLabel()
                ->default(function (Shift $record) {
                    return $record->terminal;
                })->numeric(),
            Grid::make()
                ->schema([
                    TextInput::make('cash')->translateLabel(),
                    TextInput::make('terminal')->translateLabel(),
                    TextInput::make('Total')->translateLabel()
                        ->default(function (Shift $record) {
                            return $record->cash + $record->terminal;
                        })->numeric(),
                    TextInput::make('Wages')->translateLabel(),

                    TextInput::make('calc_shift')->label(__('Calc shift'))
                        ->content(function (Shift $record) {
                            return $record->calc_shift;
                        }),
                    TextEntry::make('diff')->label(__('Different'))
                        ->content(function (Shift $record) {
                            $diff = $record->shift - $record->calc_shift;

                            return $diff;
                        }
                        ),

                    TextEntry::make('bar')->translateLabel()
                        ->content(function (Shift $record) {
                            return $record->bar;
                        }),
                    TextEntry::make('kitchen')->translateLabel()
                        ->content(function (Shift $record) {
                            return $record->kitchen;
                        }),
                    TextEntry::make('hookahs')->translateLabel()
                        ->content(function (Shift $record) {
                            return $record->hookah;
                        }),

                    TextEntry::make('income')->translateLabel()
                        ->content(function (Shift $record) {
                            return $record->income;
                        }),
                    TextEntry::make('expense')->translateLabel()
                        ->content(function (Shift $record) {
                            return $record->expense;
                        }),
                    TextEntry::make('tips')->translateLabel()
                        ->content(function (Shift $record) {
                            return $record->tips;
                        }),
                ])->columns(1)
                ->columnSpan(['lg' => 1]),

        ];
    }
}
