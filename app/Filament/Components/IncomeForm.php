<?php

namespace App\Filament\Components;

use Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class IncomeForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            TextInput::make('sum')->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->autocomplete(false),
            Textarea::make('comment')->translateLabel()->rows(2),
        ];
    }
}
