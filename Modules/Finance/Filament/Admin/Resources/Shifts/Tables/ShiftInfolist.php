<?php

namespace Modules\Finance\Filament\Admin\Resources\Shifts\Tables;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Modules\Finance\Models\Shift;

class ShiftInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Shift')->translateLabel()
                    ->schema([
                        TextEntry::make('cash')->translateLabel()
                            ->color('warning')->money('UAH')
                            ->size(TextSize::Large),
                        TextEntry::make('terminal')->translateLabel()
                            ->color('success')->money('UAH')
                            ->size(TextSize::Large),

                        TextEntry::make('sum')->translateLabel()
                            ->color('info')->money('UAH')
                            ->size(TextSize::Large)
                            ->getStateUsing(fn (Shift $record): int => $record->cash + $record->terminal ?? ''),

                        TextEntry::make('calc_shift')->translateLabel()
                            ->money('UAH')
                            ->size(TextSize::Large)
                            ->color('gray'),

                        TextEntry::make('shift')->translateLabel()
                            ->money('UAH')
                            ->size(TextSize::Large)
                            ->color('info'),

                        TextEntry::make('different')->translateLabel()
                            ->money('UAH')
                            ->size(TextSize::Large)
                            ->color(fn (Shift $record): string => match (true) {
                                ($record->shift - $record->calc_shift) < 0 => 'danger',
                                ($record->shift - $record->calc_shift) > 0 => 'success',
                                default => 'gray',
                            }
                            )
                            ->getStateUsing(fn (Shift $record): int => $record->shift - $record->calc_shift ?? ''),
                    ])->columns(['md' => 2, 'xl' => 3]),

                Fieldset::make('Workshops')->translateLabel()
                    ->schema([
                        TextEntry::make('hookahs')->translateLabel()
                            ->size(TextSize::Large)
                            ->getStateUsing(fn (Shift $record): int => $record->bulbs + $record->hookah ?? ''),
                        TextEntry::make('bar')->translateLabel()
                            ->size(TextSize::Large)
                            ->money('UAH'),
                        TextEntry::make('kitchen')->translateLabel()
                            ->size(TextSize::Large)
                            ->money('UAH'),
                    ])->columns(3),

                Fieldset::make('Expenses')->translateLabel()
                    ->schema([
                        TextEntry::make('wages')->translateLabel()
                            ->size(TextSize::Large)
                            ->money('UAH'),
                        TextEntry::make('expense')->translateLabel()
                            ->size(TextSize::Large)
                            ->money('UAH'),
                        TextEntry::make('tips')->translateLabel()
                            ->size(TextSize::Large)
                            ->money('UAH'),
                    ])->columns(3)->columnSpanFull(),

                Fieldset::make('Reports')->translateLabel()
                    ->schema([
                        IconEntry::make('report.x-report')->label(__('X reports'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check')
                            ->falseIcon('heroicon-o-x-mark'),

                        IconEntry::make('report.z-report')->label(__('Z reports'))
                            ->trueIcon('heroicon-o-check')
                            ->falseIcon('heroicon-o-x-mark'),

                    ])->columns(3),
            ])->columns(1);
    }
}
