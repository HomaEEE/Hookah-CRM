<?php

namespace Modules\Finance\Filament\Admin\Resources\Expenses\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Models\ExpenseCategory;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),
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
                    ->options(BillType::class)
                    ->default(0)
                    ->required()
                    ->inline()
                    ->grouped()
                    ->extraAttributes(['style' => 'width: 100%'])
                    ->colors([
                        0 => 'info',
                        1 => 'success',
                    ])
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
                        1 => 'success',
                    ])
                    ->default(0)
                    ->inline()
                    ->grouped()
                    ->extraAttributes(['style' => 'width: 100%'])
                    ->live(),

                Textarea::make('comment')->translateLabel()->rows(2),

            ]);
    }
}
