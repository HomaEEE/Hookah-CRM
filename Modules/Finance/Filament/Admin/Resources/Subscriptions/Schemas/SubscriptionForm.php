<?php

namespace Modules\Finance\Filament\Admin\Resources\Subscriptions\Schemas;

use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Finance\Enums\Period as EnumsPeriod;
use Modules\Finance\Models\ExpenseCategory;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')->label(__('Category'))
                    ->searchable()
                    ->preload()
                    ->options([
                        ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id'),
                    ])
                    ->createOptionForm([
                        Select::make('place_id')->translateLabel()
                            ->options(
                                Auth::user()->place->pluck('nameCity', 'id')
                            ),
                        TextInput::make('name')->translateLabel(),
                        TextInput::make('category_id')->translateLabel()->default(0),
                    ]),

                TextInput::make('name')->label('Услуга'),
                TextInput::make('price')->label(__('price')),
                Select::make('period')
                    ->label(__('Payment period'))
                    ->native(false)
                    ->options(EnumsPeriod::class),
                DatePicker::make('next_date')->label(__('next date'))->default(now())->native(false),
                TextInput::make('notes')->label(__('Notes')),
            ]);

    }
}
