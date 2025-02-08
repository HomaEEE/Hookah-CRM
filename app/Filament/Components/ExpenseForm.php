<?php

namespace App\Filament\Components;

use Auth;
use Carbon\Carbon;
use App\Models\{Expense, Supplier, Bill, ExpenseCategory};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;

use Filament\Forms\Components\{Select, TextInput, Hidden, DateTimePicker, SpatieMediaLibraryFileUpload, Textarea, Toggle, ToggleButtons};

class ExpenseForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            DateTimePicker::make('added_at')
                ->label(__('Date Added'))
                ->native(false)
                ->seconds(false)
                ->maxDate(now())->default(now()),
            Select::make('category_id')->label(__('Category'))
                ->searchable()
                ->required()
                ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
            TextInput::make('sum')->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->autocomplete(false)
                ->inputMode('decimal')
                ->stripCharacters(',')
                ->mask(RawJs::make('$money($input)')),
            SpatieMediaLibraryFileUpload::make('invoice')
                ->label(__('Invoice'))
                ->collection('supplies')
                ->multiple()
                ->imagePreviewHeight(450)
                ->imageResizeMode('cover')
                ->optimize('webp')
                ->imageEditor()
                ->resize(90),
            ToggleButtons::make('bill_id')->label(__('Type pay'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->grouped()
                ->live(),

            ToggleButtons::make('pay')->label(__('Pay'))
                ->options(
                    [
                        0 => __('No pay'),
                        1 => __('Paid'),
                    ]
                )
                ->colors([
                    0 => 'danger',
                    2 => 'success',
                ])
                ->default(0)
                ->inline()
                ->grouped()
                ->reactive(),

            Textarea::make('comment')->translateLabel()->rows(2),

        ];
    }
}
