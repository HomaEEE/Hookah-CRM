<?php

namespace Modules\Inventory\App\Filament\Froms;

use app\Models\Storage;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class AddItem
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            TextInput::make('name'),
            Select::make('storage_id')
                ->options(
                    Storage::query()->get()->pluck('name', 'id')
                ),
            TextInput::make('serial_number'),
            TextInput::make('price')->numeric(),
            TextInput::make('qty')->numeric(),
            Textarea::make('comment')->label(__('Comment')),
        ];
    }
}
