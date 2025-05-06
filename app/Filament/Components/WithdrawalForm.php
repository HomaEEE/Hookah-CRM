<?php

namespace App\Filament\Components;

use Auth;
use App\Models\{Withdrawal, Bill};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;

use Filament\Forms\Components\{TextInput, Hidden, ToggleButtons};

class WithdrawalForm
{
    public static function schema(array $options = []): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            TextInput::make('sum')->label(__('Sum'))
                ->numeric()->prefix('₴')
                ->default(3000)
                ->autocomplete(false)
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(','),
            ToggleButtons::make('bill_id')->label(__('Bill'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->grouped(),
        ];
    }
}
