<?php

namespace App\Filament\Components;

use Auth;
use App\Models\{Income, Bill};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;

use Filament\Forms\Components\{TextInput, Hidden, Textarea};

class IncomeForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            TextInput::make('sum')->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->autocomplete(false)
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(','),
            Textarea::make('comment')->translateLabel()->rows(2),
        ];
    }
}
