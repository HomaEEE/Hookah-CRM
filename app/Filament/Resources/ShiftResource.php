<?php

namespace App\Filament\Resources;

use Auth;
use Carbon\Carbon;

use Filament\Forms\Get;
use Filament\Forms\Set;

use App\Filament\Resources\ShiftResource\Pages;
use App\Filament\Resources\ShiftResource\RelationManagers;

use App\Http\Controllers\PosterApiController as PosterApi;
use App\Models\{Bill, Expense, ExpenseCategory, Place, Workshop, Shift, Supplier, User, Wage, Withdrawal};
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Tabs;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard;
use Filament\Infolists\Infolist;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Fieldset as InfolistFeildSet;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Grouping\Group;

use Filament\Tables\Columns\Summarizers\Sum;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $recordTitleAttribute = 'id';

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('Place');
    // }

    public static function getPluralModelLabel(): string
    {
        return __('Shifts');
    }

    public static function getNavigationLabel(): string
    {
        return __('Shifts');
    }

    protected function getTableQuery(): Builder
    {
        return Shift::query()->with('place');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Section::make('poster')
                            ->schema(static::getFormSchema('poster'))
                            ->hidden(fn (Get $get) => $get('place_id') == null),

                        Section::make('Personal')->label(__('Personal'))
                            ->schema(static::getFormSchema('wages'))
                            ->visible(fn (Get $get) => $get('terminal') != null),

                        Section::make('Shift')->label(__('Shift'))
                            ->schema(static::getFormSchema('shift'))
                            ->visible(fn (Get $get) => $get('terminal') != null),


                        Section::make('report')
                            ->schema(static::getFormSchema('z-report'))
                            ->hidden(fn (Get $get) => $get('place_id') == null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('d/m/Y')
                    ->badge()->translateLabel(),

                TextColumn::make('terminal')->translateLabel()
                    ->numeric()
                    ->state(function (Shift $record): int {
                        return $record->terminal * 0.987;
                    })
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),

                TextColumn::make('cash')->translateLabel()
                    ->numeric()
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),

                TextColumn::make('expense')->translateLabel()
                    ->numeric()
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),

                TextColumn::make('wages')->translateLabel()
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),

                TextColumn::make('shift')
                    ->badge()
                    ->money('uah', true)->translateLabel(),

                //   TextColumn::make('shift_users.user_id')->badge(),

            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->subMonth()->format('d/m/Y')),
                        DatePicker::make('created_until')->translateLabel()->native(false)
                            ->placeholder(fn ($state): string => now()->format('d/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Смены с ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'до ' . Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\ExpenseRelationManager::class,
        ];
    }


    public static function getFormSchema(string $section = null): array
    {
        if ($section === 'report') {
            return [
                Toggle::make('report.x-report-terminal')->label(__('X report terminal')),
                Toggle::make('report.x-report-poster')->label(__('X report poster')),
                Toggle::make('report.incassation')->label(__('Incassation')),
                Toggle::make('report.z-report-terminal')->label(__('Z report terminal')),
                Toggle::make('report.z-report-poster')->label(__('Z report poster')),
            ];
        }

        if ($section === 'poster') {
            return [
                Hidden::make('user_id')->default(Auth::user()->id),
                Section::make()->schema([

                    Hidden::make('place_id')->default(Filament::getTenant()->id),

                    Actions::make([
                        Action::make('load_poster')->translateLabel()
                            ->icon('heroicon-s-cloud-arrow-down')
                            ->color('success')
                            ->action(function (Get $get, Set $set) {
                                try {
                                    $place = Place::where('id', $get('place_id'))->first();
                                    //  $posterShift =  (new PosterApi())->getCurrentShift($poster_api->id);  API
                                    $posterShift = Shift::where('place_id', $place->id)->select('shift')->latest()->first();
                                    $posterTodayShift = (new PosterApi())->getTodayShift($place->id);

                                    $set('old_shift', $posterShift != null ? $posterShift->shift : 0);

                                    $set('total', $posterTodayShift['payed_sum_sum'] / 100);

                                    $set('cash', $posterTodayShift['payed_sum_sum'] / 100 - $posterTodayShift['payed_card_sum'] / 100);

                                    $set('terminal', $posterTodayShift['payed_card_sum'] / 100);

                                    $posterLocalExpense = (new PosterApi())->getLocalExpenses($place->id);

                                    $set('expense', (int) $posterLocalExpense);

                                    $LocalWithdrawal = (new PosterApi())->getLocalWithdrawal($place->id);
                                    $set('withdrawal', $LocalWithdrawal);

                                    $localTips = (new PosterApi())->getLocalTips($place->id);
                                    $set('tips', $localTips);

                                    $posterTodayWorkshops = (new PosterApi())->getTodayWorkshops($place->id);

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
                                } catch (RequestException $e) {
                                    Notification::make()
                                        ->title(__('Please, select place'))
                                        ->icon('heroicon-o-building-storefront')
                                        ->iconColor('danger')
                                        ->send();
                                    return;
                                }
                            })
                    ])->fullWidth()
                        ->hidden(fn (Get $get): bool => !$get('place_id')),
                ]),

                Fieldset::make(__('Poster'))->schema([
                    TextInput::make('total')->label(__('Total Poster'))
                        ->reactive()
                        ->numeric()
                        ->disabled()
                        ->default(0)

                        ->afterStateUpdated(
                            function (Set $set, Get $get) {
                                $set('cash', $get('total') - $get('terminal'));
                            }
                        ),

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
                        ->default(0)

                ])->hidden(fn (Get $get): bool => !$get('total')),


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
                            ->default(0)
                            ->hidden(fn (Get $get) => $get('kitchen') == 0),
                    ])->columns(3)
                    ->hidden(fn (Get $get): bool => !$get('total'))
            ];
        }

        if ($section === 'wages') {
            return [
                Repeater::make('users')
                    ->translateLabel()
                    ->relationship()
                    ->schema([
                        Select::make('user_id')->translateLabel()
                            ->options(
                                User::whereHas('place', function ($query) {
                                    $query->where('place_id', Filament::getTenant()->id);
                                })->get()
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            ->reactive()
                            ->searchable(),

                        Select::make('wage_id')->translateLabel()
                            ->options(
                                Wage::where('place_id', Filament::getTenant()->id)
                                    ->get()
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->afterStateUpdated(
                                function (Set $set, Get $get) {

                                    $wage = Wage::where('id', $get('wage_id'))->first();
                                    $all_procents = 0;
                                    if (isset($wage)) {
                                        foreach ($wage->procent as $num) {
                                            $workshop = Workshop::where('id', $num['workshop_id'])->first();

                                            if ($workshop->name == 'Бар') {
                                                $all_procents += $get('../../bar') * $num['num'] / 100;
                                            }
                                            if ($workshop->name == 'Кухня') {
                                                $all_procents += $get('../../kitchen') * $num['num'] / 100;
                                            }
                                            if ($workshop->name == 'Кальян') {
                                                $all_procents += $get('../../hookah') * $num['num'] / 1;
                                            }
                                            if ($workshop->name == 'Колбы') {
                                                $all_procents += $get('../../bulbs') * $num['num'] / 1;
                                            }
                                        }

                                        if ($get('wage_id') != null) {
                                            $set('wage', $wage->wage * $get('days'));
                                        } else {
                                            $set('wage', 0);
                                            $set('days', 0);
                                        }
                                    } else {
                                        $all_procents = 0;
                                        $set('days', 0);
                                    }

                                    $set('procent_day', round($all_procents, 0));
                                    $set('cashout', $get('procent_day') + $get('wage'));
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
                            )
                            ->disabled(fn (Get $get) => $get('wage_id') == null)
                            ->rules(['required'])
                            ->afterStateUpdated(
                                function (Set $set, Get $get, $state) {
                                    $wage = Wage::where('id', $get('wage_id'))->first();
                                    if ($get('days') != 0 || $get('wage_id') != null) {
                                        $set('wage', $state * $wage->wage);
                                        $set('cashout', $get('procent_day') + $get('wage'));
                                    }
                                }
                            )
                            ->reactive(),

                        TextInput::make('wage')->translateLabel()->numeric()
                            ->hidden(fn (Get $get) => $get('wage_id') == null)
                            ->afterStateUpdated(
                                function (Set $set, Get $get) {
                                    if ($get('wage') != null) {
                                        $set('cashout', $get('procent_day') + $get('wage'));
                                    } else {
                                        $set('cashout', 0);
                                    }
                                }
                            )->columns(2)
                            ->reactive(),
                        TextInput::make('procent_day')->translateLabel()->numeric()
                            ->hidden(fn (Get $get) => $get('wage_id') == null)
                            ->afterStateUpdated(
                                function (Set $set, Get $get) {
                                    if ($get('procent_day') != null) {
                                        $set('cashout', $get('procent_day') + $get('wage'));
                                    }
                                }
                            )
                            ->reactive(),
                        TextInput::make('cashout')->translateLabel()
                            ->hidden(fn (Get $get) => $get('wage_id') == null),
                    ])->columns(3)
                    ->itemLabel(fn (array $state): ?string => $state['user_id.name'] ?? null)

            ];
        }
        if ($section === 'shift') {
            return [
                Actions::make([
                    Action::make('Add Withdrawal')->label(__('Add Withdrawal'))
                        ->icon('heroicon-s-banknotes')
                        ->color('success')
                        ->modalHeading(__('Add Withdrawal'))
                        ->requiresConfirmation()
                        ->form([
                            Hidden::make('user_id')->default(Auth::user()->id),
                            Hidden::make('place_id')->default(Filament::getTenant()->id),
                            TextInput::make('sum')->label(__('Sum'))->numeric()->prefix('₴')->default(3000),
                            Select::make('bill_id')->label(__('Bill'))
                            ->relationship('bills', 'name')
                            ->options(Bill::get()->pluck('name', 'id'))
                        ])
                        ->action(function (Set $set, array $data): void {
                            Withdrawal::create(
                                [
                                    'place_id' => Filament::getTenant()->id,
                                    'user_id' => Auth::user()->id,
                                    'sum' => $data['sum']
                                ]
                            );


                            $LocalWithdrawal = (new PosterApi())->getLocalWithdrawal(Filament::getTenant()->id);
                            $set('withdrawal', $LocalWithdrawal);
                        }),


                    Action::make('add_expense')->label(__('Add Expense'))
                        ->icon('heroicon-o-document-minus')
                        ->color('info')
                        ->modalHeading(__('Add Expense'))
                        ->requiresConfirmation()
                        ->form([
                                DateTimePicker::make('added_at')
                                    ->label(__('Date Added'))
                                    ->native(false)
                                    ->maxDate(now())->default(now()),
                                Select::make('category_id')->label(__('Category'))
                                    ->relationship('categories', 'name')
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->options(ExpenseCategory::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')),
                                TextInput::make('sum')->label(__('Sum'))
                                    ->numeric()
                                    ->prefix('₴')
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                                Select::make('bill_id')->label(__('Type pay'))
                                    ->options(Bill::get()->pluck('name', 'id'))
                                    ->required(),
                                Textarea::make('comment')->translateLabel()->rows(2),
                        ])
                        ->action(function (Set $set, array $data): void {
                            Expense::create(
                                [
                                    'place_id' => Filament::getTenant()->id,
                                    'user_id' => Auth::user()->id,
                                    'category_id' => $data['category_id'],
                                    'sum' => $data['sum'],
                                    'added_at' => $data['added_at'],
                                    'comment' => $data['comment'],
                                    'bill_id' => $data['bill_id'],
                                    'pay' => 1
                                ],
                            );

                            Notification::make()
                            ->title(__('Expense added'))
                            ->send();

                            $posterLocalExpense = (new PosterApi())->getLocalExpenses(Filament::getTenant()->id);
                            $set('expense', (int) $posterLocalExpense);

                            $LocalWithdrawal = (new PosterApi())->getLocalWithdrawal(Filament::getTenant()->id);
                            $set('withdrawal', $LocalWithdrawal);
                        }),

                    Action::make('update_expenses')->translateLabel()
                        ->icon('heroicon-s-arrow-path')
                        ->color('danger')
                        ->action(function ( Set $set) {
                            $posterLocalExpense = (new PosterApi())->getLocalExpenses(Filament::getTenant()->id);
                            $set('expense', (int) $posterLocalExpense);

                            $localTips = (new PosterApi())->getLocalTips(Filament::getTenant()->id);
                            $set('tips', $localTips);

                            $LocalWithdrawal = (new PosterApi())->getLocalWithdrawal(Filament::getTenant()->id);
                            $set('withdrawal', $LocalWithdrawal);
                        }),

                ]),

                FieldSet::make(__('Expenses & Income'))->schema([
                    TextInput::make('income')->translateLabel()
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->reactive()
                        ->minValue(0)
                        ->default(0),

                    TextInput::make('expense')->translateLabel()
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->default(0)
                        ->hidden(fn (Get $get) => $get('expense') == 0),

                    TextInput::make('tips')->translateLabel()
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()->default(0),
                    TextInput::make('withdrawal')->translateLabel()
                        ->disabled()
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])

                        ->hidden(fn (Get $get) => $get('withdrawal') == 0)
                        ->numeric()->default(0),

                    Hidden::make('wages'),
                    Placeholder::make('wages_calc')->translateLabel()
                        ->content(function (Set $set, $get) {
                            $totalWage = collect($get('users'))
                                ->pluck('cashout')
                                ->sum();
                            $set('wages', $totalWage);

                            return $totalWage;
                        })
                        ->reactive(),
                ])->columns(3),



                // Placeholder::make('coupurs_calc')->translateLabel()
                // ->content(function (Set $set, $get) {
                //     $total = collect($get('coupurs'))
                //         ->pluck('sum')
                //         ->sum();
                //     $set('shift', $total);
                //     return $total;
                // })
                // ->reactive(),

                FieldSet::make('Shift')->translateLabel()->schema([

                    Placeholder::make('total_shift')->translateLabel()
                        ->content(function (Set $set, Get $get) {
                            $totalCash =
                                $get('old_shift') + $get('cash') - $get('wages')
                                + $get('income') - $get('expense') - $get('withdrawal');
                            $set('calc_shift', $totalCash);
                            return $totalCash;
                        }),

                    Hidden::make('calc_shift')->default(0),
                    Placeholder::make('different')->translateLabel()
                        ->content(function (Get $get) {
                            if ($get('shift') != null) {
                                $different =
                                    $get('shift') - $get('calc_shift');
                                return $different;
                            } else {
                                return 0;
                            }
                        }),
                    TextInput::make('shift')->translateLabel()
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->numeric()
                        ->required()
                        ->reactive()
                ])->columns(['md' => 2, 'xl' => 3]),
            ];
        }
        return [];
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistFeildSet::make('Shift')->translateLabel()
                    ->schema([
                        TextEntry::make('cash')->translateLabel()
                            ->badge(),
                        TextEntry::make('terminal')->translateLabel()
                            ->badge(),

                        TextEntry::make('sum')->translateLabel()
                            ->badge()
                            ->getStateUsing(fn (Shift $record): int => $record->cash + $record->terminal ?? ''),

                        TextEntry::make('calc_shift')->translateLabel()
                            ->badge(),

                        TextEntry::make('shift')->translateLabel()
                            ->badge(),

                        TextEntry::make('different')->translateLabel()
                            ->badge()
                            ->getStateUsing(fn (Shift $record): int => $record->calc_shift - $record->shift ?? ''),
                    ])->columns(['md' => 2, 'xl' => 3]),

                InfolistFeildSet::make('Workshops')->translateLabel()
                    ->schema([
                        TextEntry::make('hookahs')->translateLabel()
                            ->getStateUsing(fn (Shift $record): int => $record->bulbs + $record->hookah ?? ''),
                        TextEntry::make('bar')->translateLabel(),
                        TextEntry::make('kitchen')->translateLabel()
                    ])->columns(3),

                InfolistFeildSet::make('Expenses')->translateLabel()
                    ->schema([
                        TextEntry::make('wages')->translateLabel(),
                        TextEntry::make('expense')->translateLabel(),
                        TextEntry::make('tips')->translateLabel()
                    ])->columns(3)->columnSpanFull(),

                InfolistFeildSet::make('Reports')->translateLabel()
                    ->schema([
                        IconEntry::make('report.x-report-terminal')->label(__('X report terminal'))
                        ->boolean()
                        ->trueIcon('heroicon-o-check')
                        ->falseIcon('heroicon-o-x-mark'),

                        IconEntry::make('report.z-report-terminal')->label(__('Z report terminal'))
                        ->boolean()
                        ->trueIcon('heroicon-o-check')
                        ->falseIcon('heroicon-o-x-mark'),

                        IconEntry::make('report.x-report-poster')->label(__('X report poster'))
                        ->boolean()
                        ->trueIcon('heroicon-o-check')
                        ->falseIcon('heroicon-o-x-mark'),

                        IconEntry::make('report.z-report-poster')->label(__('Z report terminal'))
                        ->boolean()
                        ->trueIcon('heroicon-o-check')
                        ->falseIcon('heroicon-o-x-mark'),

                        IconEntry::make('report.incassation')->label(__('Incassation'))
                        ->boolean()
                        ->trueIcon('heroicon-o-check')
                        ->falseIcon('heroicon-o-x-mark')
                    ])->columns(3)
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'view' => Pages\ViewShift::route('/{record}/view'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
