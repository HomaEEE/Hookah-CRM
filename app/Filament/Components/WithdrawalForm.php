<?php

namespace App\Filament\Components;

use App\Models\Bill;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\RawJs;

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
