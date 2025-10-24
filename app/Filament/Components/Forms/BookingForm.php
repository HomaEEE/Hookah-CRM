<?php

namespace App\Filament\Components\Forms;

use App\Models\Guest;
use App\Models\Seet;
use Auth;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Size;
use Filament\Support\RawJs;

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
                    Action::make('PersonPlus')
                        ->size(Size::Large)
                        ->color('success')
                        ->icon('heroicon-o-user-plus')
                        ->iconButton()
                        ->disabled(fn (Get $get): bool => empty($get('person')) || $get('person') >= 20)
                        ->action(function (Get $get, Set $set, $state): void {
                            $set('person', $get('person') + 1);
                        })
                )
                ->prefixAction(
                    Action::make('PersonMinus')
                        ->size(Size::Large)
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
