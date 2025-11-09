<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Enums\Subscription;
use App\Models\ExpenseCategory;
use Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')->label(__('Category'))
                    ->searchable()
                    ->preload()
                    ->options(function (callable $get) {
                        if (null == ($get('place_id'))) {
                            return null;
                        } else {
                            return ExpenseCategory::where('place_id', $get('place_id'))->pluck('name', 'id');
                        }
                    })
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
                    ->options(Subscription::class),
                DatePicker::make('next_date')->label(__('next date'))->default(now())->native(false),
                TextInput::make('notes')->label(__('Notes')),
            ]);

    }
}
