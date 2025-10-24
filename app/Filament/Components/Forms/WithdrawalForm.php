<?php

namespace App\Filament\Components\Forms;

use App\Models\Bill;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;

class WithdrawalForm
{
    public static function schema(array $options = []): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            TextInput::make('sum')->label(__('Sum'))->numeric()->prefix('₴')->default(3000),
            ToggleButtons::make('bill_id')->label(__('Type pay'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->extraAttributes(['style' => 'width: 100%'])
                ->grouped()
                ->live(),
        ];
    }
}
