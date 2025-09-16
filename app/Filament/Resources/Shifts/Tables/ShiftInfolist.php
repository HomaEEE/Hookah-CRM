<?php

namespace App\Filament\Resources\Shifts\Tables;

use App\Models\Shift;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class ShiftInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Shift')->translateLabel()
                    ->schema([
                        TextEntry::make('cash')->translateLabel()
                            ->badge()->color('warning'),
                        TextEntry::make('terminal')->translateLabel()
                            ->badge(),

                        TextEntry::make('sum')->translateLabel()
                            ->badge()
                            ->color('gray')
                            ->getStateUsing(fn (Shift $record): int => $record->cash + $record->terminal ?? ''),

                        TextEntry::make('calc_shift')->translateLabel()
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('shift')->translateLabel()
                            ->badge()
                            ->color('info'),

                        TextEntry::make('different')->translateLabel()
                            ->badge()
                            ->color('danger')
                            ->getStateUsing(fn (Shift $record): int => $record->shift - $record->calc_shift ?? ''),
                    ])->columns(['md' => 2, 'xl' => 3]),

                Fieldset::make('Workshops')->translateLabel()
                    ->schema([
                        TextEntry::make('hookahs')->translateLabel()
                            ->getStateUsing(fn (Shift $record): int => $record->bulbs + $record->hookah ?? ''),
                        TextEntry::make('bar')->translateLabel(),
                        TextEntry::make('kitchen')->translateLabel(),
                    ])->columns(3),

                Fieldset::make('Expenses')->translateLabel()
                    ->schema([
                        TextEntry::make('wages')->translateLabel(),
                        TextEntry::make('expense')->translateLabel(),
                        TextEntry::make('tips')->translateLabel(),
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
            ]);
    }
}
