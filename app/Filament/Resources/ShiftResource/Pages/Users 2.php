<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\ShiftResource;
use App\Models\{Wage, User, Workshop};
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\{Get, Set};
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\DeleteAction as ActionsDeleteAction;

class Users extends ManageRelatedRecords
{
    protected static string $resource = ShiftResource::class;

    protected static string $relationship = 'shift_users';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return __('Shift Users');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Shift Users');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('shift_id')->default($this->record->id),
                Select::make('user_id')->translateLabel()
                                ->options(
                                    User::whereHas('place', function ($query) {
                                        $query->where('place_id', Filament::getTenant()->id);
                                    })->get()
                                        ->pluck('name', 'id')
                                )
                                ->required()
                                ->reactive()
                                ->searchable()
                                ->afterStateUpdated(
                                    function (callable $set, callable $get) {
                                        self::WageCalc($set, $get);
                                     }
                                ),

                            Select::make('wage_id')->translateLabel()
                                ->options(
                                    Wage::where('place_id', Filament::getTenant()->id)
                                        ->get()
                                        ->pluck('name', 'id')
                                )
                                ->preload()
                                ->required()
                                ->live(onBlur: true)
                                ->searchable()
                                ->hidden(fn (Get $get) => $get('user_id') == null)
                                ->afterStateUpdated(
                                    function (callable $set, callable $get) {
                                        self::WageCalc($set, $get);
                                     }
                                ),

                            Select::make('days')->translateLabel()
                                ->options(
                                    [
                                        '0' => '0',
                                        '0.5' => '0.5',
                                        '1' => '1',
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                        '5' => '5',
                                        '6' => '6',
                                        '7' => '7',
                                    ]
                                )->default(0)
                                ->hidden(fn (Get $get) => $get('user_id') == null)
                                ->required()
                                ->live(debounce: 500)
                                ->afterStateUpdated(
                                    function (callable $set, callable $get) {
                                        self::WageCalc($set, $get);
                                     }
                                )
                                ->reactive(),
                            TextInput::make('wage')->translateLabel()->numeric()
                                ->hidden(fn (Get $get) => $get('wage_id') == null)
                                ->columns(2)
                                ->live(debounce: 500),
                            TextInput::make('procent_day')->translateLabel()->numeric()
                                ->prefix('%')
                                ->suffixAction(
                                    FormAction::make('Reset')
                                        ->size(ActionSize::Large)
                                        ->icon('heroicon-o-x-mark')
                                        ->iconButton()
                                        ->color('danger')
                                        ->disabled(fn (Get $get): bool => empty($get('procent_day')) || $get('procent_day') == 0)
                                        ->action(function (Get $get, Set $set, $state): void {
                                            $set('procent_day', 0);
                                        })
                                )
                                ->suffixAction(
                                    FormAction::make('Round')
                                        ->size(ActionSize::Large)
                                        ->icon('heroicon-o-arrow-path')
                                        ->iconButton()
                                        ->color('info')
                                        ->disabled(fn (Get $get): bool => empty($get('procent_day')))
                                        ->action(function (Get $get, Set $set, $state): void {
                                            $set('procent_day', round($get('procent_day')));
                                        })
                                )
                                ->hidden(fn (Get $get) => $get('wage_id') == null)
                                ->live(debounce: 500)
                                ->afterStateUpdated(
                                    function (callable $set, callable $get) {
                                        self::WageCalc($set, $get);
                                     }
                                )
                                ->live(debounce: 500),
                            TextInput::make('cashout')->translateLabel()
                                ->live(debounce: 500)
                                ->readOnly()
                                ->hidden(fn (Get $get) => $get('wage_id') == null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('shift_id', $this->getOwnerRecord()->id) )
            ->columns([
                TextColumn::make('user.name')->translateLabel(),
                TextColumn::make('days')->translateLabel(),
                TextColumn::make('wage')->translateLabel()->summarize(Sum::make()->money('UAH', 100)->label(__('Wage'))),
                TextColumn::make('procent_day')->translateLabel()->summarize(Sum::make()->money('UAH', 100)->label(__('Procent'))),
                TextColumn::make('cashout')->translateLabel()->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label(__('Add'))
            ])
            ->actions([
                ActionsDeleteAction::make()
            ])
            ->bulkActions([
            ]);
    }

    private static function WageCalc( Set $set, Get $get) {
        if ( $get('user_id') !== null ){
             $user = User::where('id', $get('user_id'))->first();
             $set('wage_id', $user->wage_id );
             $set('wage', $user->wage->wage );

             if ( $get('days') == null ){
                 $set('days', $user->shift_day);
            };

            if (isset ( $user->wage )) {
                if ($get('procent_day') == null) {
                    $all_procent = 0;
                    foreach ($user->wage->procent as $workshop) {
                        $field = Workshop::where('workshop_id', $workshop['workshop_id'])->first()->toArray();
                        $all_procent += round ($get('../../'.$field['field']) * $workshop['num'] / 1);
                    };
                    $set('procent_day', $all_procent );
            } else {
                $set('procent_day', $get('procent_day') );
            }

            };
            $set('cashout', ($get('wage') *$get('days')) + $get('procent_day'));
        }
         else {
             $set('wage',0 );
             $set('procent_day', 0 );
             $set('cashout', 0 );

         }
     }

}
