<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\ShiftResource;
use App\Models\Shift;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShift extends EditRecord
{
    protected static string $resource = ShiftResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
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
                            Placeholder::make('diff')->label(__('Different'))
                                ->content(function (Shift $record) {
                                    $diff = $record->shift - $record->calc_shift;

                                    return $diff;
                                }
                                ),

                            Placeholder::make('bar')->translateLabel()
                                ->content(function (Shift $record) {
                                    return $record->bar;
                                }),
                            Placeholder::make('kitchen')->translateLabel()
                                ->content(function (Shift $record) {
                                    return $record->kitchen;
                                }),
                            Placeholder::make('hookahs')->translateLabel()
                                ->content(function (Shift $record) {
                                    return $record->hookah;
                                }),

                            Placeholder::make('income')->translateLabel()
                                ->content(function (Shift $record) {
                                    return $record->income;
                                }),
                            Placeholder::make('expense')->translateLabel()
                                ->content(function (Shift $record) {
                                    return $record->expense;
                                }),
                            Placeholder::make('tips')->translateLabel()
                                ->content(function (Shift $record) {
                                    return $record->tips;
                                }),
                        ])->columns(1)
                        ->columnSpan(['lg' => 1]),

        ];
    }
}
