<?php

namespace App\Filament\Components\Forms;

use Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IncomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                TextInput::make('sum')->label(__('Sum'))
                    ->numeric()
                    ->prefix('₴')
                    ->autocomplete(false),
                Textarea::make('comment')->translateLabel()->rows(2),
            ]);
    }
}
