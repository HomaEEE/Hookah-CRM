<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\ExpenseCategory;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                ToggleButtons::make('type')
                    ->label(__('Type'))
                    ->options(TransactionType::class)
                    ->default(1)
                    ->inline()
                    ->live()
                    ->grouped()
                    ->required(),

                DatePicker::make('added_at')->label(__('Added at'))->required()->native(false)->default(now())->maxDate(now()),

                Select::make('expense_category_id')->label(__('Category'))
                    ->options(ExpenseCategory::get()->pluck('name', 'id'))
                    ->native(false)
                    ->searchable()
                    ->hidden(fn (Get $get): bool => $get('type') != TransactionType::Expense),
                TextInput::make('sum')
                    ->label(__('Sum'))
                    ->numeric()
                    ->prefix('₴')
                    ->inputMode('decimal'),

                ToggleButtons::make('bill_id')->label(__('Bill'))
                    ->options(BillType::class)->default(1)
                    ->required()
                    ->inline()
                    ->grouped()
                    ->extraAttributes(['style' => 'width: 100%'])
                    ->colors([
                        0 => 'info',
                        1 => 'success',
                    ])
                    ->live(),

                SpatieMediaLibraryFileUpload::make('invoice')
                    ->hidden(fn (Get $get): bool => $get('type') != TransactionType::Expense)
                    ->label(__('Invoice'))
                    ->collection('expenses')
                    ->multiple()
                    ->imagePreviewHeight(350)
                    ->imageEditor()
                    ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 80])
                    ->downloadable()
                    ->deletable(),

                Textarea::make('comment')
                    ->label(__('Comment'))
                    ->maxLength(255),
            ])->columns(1);
    }
}
