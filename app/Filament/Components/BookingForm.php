<?php

namespace App\Filament\Components;

use Auth;
use Carbon\Carbon;
use App\Models\{Guest, Seet};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Support\RawJs;

use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Support\Enums\ActionSize;

use Filament\Forms\Components\{Select, TextInput, Hidden, DateTimePicker, Textarea, Toggle, ToggleButtons};

class BookingForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            Hidden::make('place_id')->default(Filament::getTenant()->id),
            Select::make('guest_id')->label(__('Guest'))
                ->relationship('guest', 'name')
                ->options(Guest::where('place_id', Filament::getTenant()->id)->get()->pluck('FullName', 'id'))
                ->searchable(['name', 'comment', 'phone'])
                ->native(false)
                ->preload()
                ->required()
                ->createOptionForm([
                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                    TextInput::make('name')->label(__('Name'))->required(),
                    TextInput::make('phone')->label(__('Phone'))->required()
                        ->tel()
                        ->mask(RawJs::make(<<<'JS'
                    $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                JS))
                    ->translateLabel(),
                    TextInput::make('comment')->translateLabel(),
                ])
                ->editOptionForm([
                    Hidden::make('place_id')->default(Filament::getTenant()->id),
                    TextInput::make('name')->label(__('Name'))->required(),
                    TextInput::make('phone')->label(__('Phone'))->required()
                        ->tel()
                        ->mask(RawJs::make(<<<'JS'
                    $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                JS))
                        ->translateLabel(),
                    TextInput::make('comment')->translateLabel(),
                ]),
            Select::make('seet_ids')->label(__('Table'))->options(
                Seet::where('place_id', Filament::getTenant()->id)->pluck('number', 'id')
            )->multiple(),
            DateTimePicker::make('time')->label(__('Booking Time'))
                ->reactive()->required()->native(false)
                ->seconds(false)
                ->minDate(now())->default(now()),
            TextInput::make('person')->label(__('Person'))->numeric()->default(3)

                ->suffixAction(
                    FormAction::make('PersonPlus')
                        ->size(ActionSize::Large)
                        ->color('success')
                        ->icon('heroicon-o-user-plus')
                        ->iconButton()
                        ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') >= 20)
                        ->action(function (Get $get, Set $set, $state): void {
                            $set('person', $get('person') + 1);
                        })
                )
                ->prefixAction(
                    FormAction::make('PersonMinus')
                        ->size(ActionSize::Large)
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->iconButton()
                        ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') <= 1)
                        ->action(function (Get $get, Set $set, $state): void {
                            $set('person', $get('person') - 1);
                        })
                ),
            Textarea::make('comment')->label(__('Comment')),
        ];
    }
}
