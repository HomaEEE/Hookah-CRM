<?php

namespace App\Filament\Components\Forms;

use App\Models\Bill;
use App\Models\ExpenseCategory;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Support\Facades\Auth;

class ExpenseForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            DateTimePicker::make('added_at')
                ->label(__('Date Added'))
                ->native(false)
                ->seconds(false)
                ->maxDate(now())->default(now()),
            Select::make('category_id')
                ->label(__('Category'))
                ->searchable()
                ->required()
                ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
            TextInput::make('sum')
                ->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->inputMode('decimal'),
            SpatieMediaLibraryFileUpload::make('invoice')
                ->label(__('Invoice'))
                ->collection('supplies')
                ->multiple()
                ->imagePreviewHeight(350)
                ->imageEditor()
                ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 80])
                ->downloadable()
                ->deletable(),
            ToggleButtons::make('bill_id')->label(__('Type pay'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->grouped()
                ->extraAttributes(['style' => 'width: 100%'])
                ->colors([
                    0 => 'info',
                    1 => 'success',
                ])
                ->reactive(),
            ToggleButtons::make('pay')->label(__('Pay'))
                ->options(
                    [
                        0 => __('No pay'),
                        1 => __('Paid'),
                    ]
                )
                ->colors([
                    0 => 'danger',
                    1 => 'success',
                ])
                ->default(0)
                ->inline()
                ->grouped()
                ->extraAttributes(['style' => 'width: 100%'])
                ->reactive(),

            Textarea::make('comment')->translateLabel()->rows(2),

        ];
    }
}
