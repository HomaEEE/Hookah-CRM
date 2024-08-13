<?php

namespace App\Filament\Components\Shift;

use Auth;

use App\Http\Controllers\{LocalApiController, PosterApiController};
use App\Models\{Place, Shift};
use Filament\Facades\Filament;
use Filament\Forms\Components\{Fieldset, Hidden, Section, TextInput};
use Filament\Forms\{Get, Set};
use Filament\Notifications\Notification;


use Filament\Forms\Components\{Actions, Actions\Action};

class PosterWizard
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
                        Section::make()->schema([
                            Hidden::make('place_id')->default(Filament::getTenant()->id),
                            Actions::make([
                                Action::make('load_poster')->translateLabel()
                                    ->icon('heroicon-s-cloud-arrow-down')
                                    ->color('success')
                                    ->action(function (Get $get, Set $set) {
                                            $place = Place::where('id', $get('place_id'))->first();
                                            //  $posterShift =  (new PosterApi())->getCurrentShift($poster_api->id);  API
                                            $posterShift = Shift::where('place_id', $place->id)->select('shift')->latest()->first();
                                            $posterTodayShift = (new PosterApiController())->getTodayShift($place->id);

                                            $set('old_shift', $posterShift != null ? $posterShift->shift : 0);

                                            $set('total', $posterTodayShift['payed_sum_sum'] / 100);

                                            $set('cash', $posterTodayShift['payed_cash_sum'] / 100 );

                                            $set('terminal', $posterTodayShift['payed_card_sum'] / 100);

                                            $posterLocalExpense = (new LocalApiController())->getLocalExpenses($place->id);
                                            $set('expense', (int) $posterLocalExpense / 100 );

                                            $posterLocalIncomes = (new LocalApiController())->getLocalIncomes(Filament::getTenant()->id);
                                            $set('income', (int) $posterLocalIncomes / 100 );

                                            $LocalWithdrawal = (new LocalApiController())->getLocalWithdrawal($place->id);
                                            $set('withdrawal', $LocalWithdrawal / 100);

                                            $localTips = (new LocalApiController())->getLocalTips($place->id);
                                            $set('tips', $localTips /100);

                                            $posterTodayWorkshops = (new PosterApiController())->getTodayWorkshops($place->id);

                                            foreach ($posterTodayWorkshops as $ws) {
                                                if ($ws['workshop_name'] == 'Кальян') {
                                                    if ($ws['count'] != 0) {
                                                        $set('hookah', $ws['count'] / 1);
                                                    } else {
                                                        $set('hookah', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Колбы') {
                                                    if ($ws['count'] != 0) {
                                                        $set('bulbs', $ws['count'] / 1);
                                                    } else {
                                                        $set('bulbs', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Колбы') {
                                                    if ($ws['count'] != 0) {
                                                        $set('bulbs_price', $ws['revenue'] / 1);
                                                    } else {
                                                        $set('bulbs_price', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Кухня') {
                                                    if ($ws['revenue'] != 0) {
                                                        $set('kitchen', $ws['revenue'] / 1);
                                                    } else {
                                                        $set('kitchen', 0);
                                                    }
                                                }

                                                if ($ws['workshop_name'] == 'Бар') {
                                                    if ($ws['revenue'] != 0) {
                                                        $set('bar', $ws['revenue'] / 1);
                                                    } else {
                                                        $set('bar', 0);
                                                    }
                                                }
                                            }

                                            Notification::make()
                                                ->title(__('Loaded data from Poster'))
                                                ->icon('heroicon-o-building-storefront')
                                                ->iconColor('success')
                                                ->send();
                                        })
                            ])->fullWidth()
                        ]),

                        Fieldset::make(__('Poster'))->schema([

                            TextInput::make('total')->label(__('Total Poster'))
                                ->reactive()
                                ->numeric()
                                ->readOnly()
                                ->disabled()
                                ->default(0),

                            TextInput::make('cash')->label(__('Cash Poster'))
                                ->numeric()
                                ->default(0)
                                ->reactive(),

                            TextInput::make('terminal')->label(__('Terminal Poster'))
                                ->default(0)
                                ->numeric()
                                ->reactive(),

                            TextInput::make('old_shift')->label(__('Shift Poster'))
                                ->numeric()
                                ->readOnly()
                                ->default(0)

                        ])->columns(4)
                        ->hidden(fn (Get $get): bool => !$get('total')),


                        Fieldset::make(__('Workshops'))
                            ->schema([
                                TextInput::make('bulbs')->label(__('Bulbs'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->hidden(fn (Get $get) => $get('bulbs') == 0),
                                TextInput::make('hookah')->label(__('Hookahs'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('bar')->label(__('Bar'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->default(0)
                                    ->hidden(fn (Get $get) => $get('bar') == 0),
                                TextInput::make('kitchen')->label(__('Kitchen'))
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->numeric()
                                    ->default(0),
                            ])->columns(4)
                            ->hidden(fn (Get $get): bool => !$get('total'))
        ];
}
}