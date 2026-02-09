<?php

namespace App\Filament\Resources\Shifts\Schemas;

use App\Http\Controllers\LocalApiController;
use App\Http\Controllers\PosterApiController;
use App\Models\Place;
use App\Models\Shift;
use App\Models\Workshop;
use Auth;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

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
                            $place = Place::where('id', Filament::getTenant()->id)->first();
                            //  $posterShift =  (new PosterApi())->getCurrentShift($poster_api->id);  API
                            $posterShift = Shift::where('place_id', $place->id)->select('shift')->latest()->first();
                            $posterTodayShift = (new PosterApiController)->getTodayShift(Filament::getTenant()->id);

                            $set('old_shift', $posterShift != null ? $posterShift->shift : 0);

                            $set('total', $posterTodayShift['payed_sum_sum'] / 100);

                            $set('cash', $posterTodayShift['payed_cash_sum'] / 100);

                            $set('terminal', $posterTodayShift['payed_card_sum'] / 100);

                            $posterLocalExpense = (new LocalApiController)->getLocalExpenses(Filament::getTenant()->id);
                            $set('expense', (int) $posterLocalExpense);

                            $posterLocalIncomes = (new LocalApiController)->getLocalIncomes(Filament::getTenant()->id);
                            $set('income', (int) $posterLocalIncomes);

                            $LocalWithdrawal = (new LocalApiController)->getLocalWithdrawal(Filament::getTenant()->id);
                            $set('withdrawal', $LocalWithdrawal);

                            $localTips = (new LocalApiController)->getLocalTips(Filament::getTenant()->id);
                            $set('tips', $localTips);

                            $posterTodayWorkshops = (new PosterApiController)->getTodayWorkshops(Filament::getTenant()->id);

                            foreach ($posterTodayWorkshops as $ws) {

                                $field = Workshop::where('workshop_id', $ws['workshop_id'])->first();
                                if (! $field) {
                                    continue; // Пропустить, если Workshop не найден
                                }
                                if ($ws[$field['type']] >= 1) {
                                    $set($field['field'], $ws[$field['type']] / 1);
                                } else {
                                    $set($field[$ws['field']], 0);
                                }
                            }

                            Notification::make()
                                ->title(__('Loaded data from Poster'))
                                ->icon('heroicon-o-building-storefront')
                                ->iconColor('success')
                                ->send();
                        }),
                ])->fullWidth(),
            ]),

            Fieldset::make(__('Poster'))->schema([

                TextInput::make('total')->label(__('Total Poster'))
                    ->numeric()
                    ->readOnly()
                    ->default(0),

                TextInput::make('cash')->label(__('Cash Poster'))
                    ->numeric()
                    ->readOnly()
                    ->default(0)
                    ->live()
                    ->suffixAction(
                        Action::make('copy')
                            ->icon('heroicon-s-clipboard-document-check')
                            ->action(function ($livewire, $state) {
                                $livewire->js(
                                    'window.navigator.clipboard.writeText("'.$state.'");
                                $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                );
                            })
                    ),

                TextInput::make('terminal')->label(__('Terminal Poster'))
                    ->default(0)
                    ->readOnly()
                    ->numeric()
                    ->live(),

                Hidden::make('old_shift')->label(__('Shift Poster'))
                    ->default(0),

            ])->columns(3)
                ->hidden(fn (Get $get): bool => ! $get('total')),

            Fieldset::make(__('Workshops'))
                ->schema([
                    TextInput::make('hookah')->label(__('Hookahs'))
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->default(0),
                    TextInput::make('bulbs')->label(__('Bulbs'))
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->hidden(fn (Get $get) => $get('bulbs') == 0),
                    TextInput::make('bar')->label(__('Bar'))
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->default(0)
                        ->hidden(fn (Get $get) => $get('bar') == 0),
                    TextInput::make('kitchen')->label(__('Kitchen'))
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->default(0),
                ])
                ->hidden(fn (Get $get): bool => ! $get('total')),
        ];
    }
}
