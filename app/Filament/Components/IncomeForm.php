<?php

namespace App\Filament\Components;

use Auth;
use App\Models\{Income, Bill};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;

use Filament\Forms\Components\{Select, TextInput, Hidden, DateTimePicker, KeyValue, Section, SpatieMediaLibraryFileUpload, Tabs, Textarea, Toggle, ToggleButtons};

class IncomeForm
{
    public static function schema(array $options = []): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            TextInput::make('sum')->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(','),
            Textarea::make('comment')->translateLabel()->rows(2),
        ];
    }
}
