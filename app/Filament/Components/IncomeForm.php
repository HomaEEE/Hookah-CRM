<?php

namespace App\Filament\Components;

use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

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
                ->autocomplete(false),
            Textarea::make('comment')->translateLabel()->rows(2),
        ];
    }
}
