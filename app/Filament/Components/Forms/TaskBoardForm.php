<?php

namespace App\Filament\Components\Forms;

use App\Models\User;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;

class TaskBoardForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),

            Flex::make([
                Section::make([
                    TextInput::make('title')->translateLabel(),
                    Textarea::make('text')->translateLabel(),
                ]),
                Section::make([
                    Select::make('for_user_id')->label(__('For User'))
                        ->native(false)
                        ->options(
                            User::orderBy('name', 'asc')
                                ->orderBy('id', 'desc')
                                ->visible(true)->pluck('name', 'id')
                        ),
                    DatePicker::make('due_date')
                        ->label(__('Due Date'))
                        ->native(false)
                        ->date()
                        ->minDate(now())->default(now()),
                    ToggleButtons::make('status')
                        ->options([
                            '1' => 'Todo',
                            '2' => 'In progress',
                            '3' => 'Completed',
                        ])->default('1')->grouped(),
                ]),
            ]),
        ];
    }
}
