<?php

namespace App\Filament\Resources;

use App\Enums\Bill as BillEnum;
use App\Enums\Subscription as SubscriptionEnum;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\{Bill, Expense, ExpenseCategory, Subscription};
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Grouping\Group;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Finance';

    public static function getPluralModelLabel(): string
    {
        return __('Subscriptions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
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
                    ->options(SubscriptionEnum::class),
                DatePicker::make('next_date')->label(__('next date'))->default(now())->native(false),
                TextInput::make('notes')->label(__('Notes')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('xl')
                    ->toggleable(),
                TextColumn::make('name')
                    ->label(__('Destination'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('xl')
                    ->toggleable()
                    ->description(fn (Subscription $record): string => $record->place->city, position: 'above'),

                TextColumn::make('price')
                    ->translateLabel()
                    ->money('uah', true)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->description(fn (Subscription $record): string => $record->category->name, position: 'above')
                    ->summarize(Sum::make()->money('uah')->label(__('Total'))),

                TextColumn::make('period')
                    ->label(__('Payment period'))
                    ->toggleable(),
                TextColumn::make('next_date')
                    ->label(__('next date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])

            ->actions([
                Action::make('paid')->button()->translateLabel()
                    ->icon('heroicon-s-banknotes')
                    ->color('success')
                    ->modalIcon('heroicon-o-banknotes')
                    ->modalIconColor('success')
                    ->modalHeading('Оплата платежа')
                    ->requiresConfirmation()
                    ->modalWidth(MaxWidth::Small)
                    ->form([
                        Hidden::make('user_id')->default(Auth::user()->id),
                        TextInput::make('sum')->translateLabel()
                        ->numeric()
                        ->required()
                        ->prefix('₴')
                        ->columnSpan(6)
                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                        ->default(fn (Subscription $record): int => $record->price),
                        Select::make('type_pay')->translateLabel()
                            ->columnSpan(6)
                            ->options(
                              Bill::get()->pluck('name', 'id')
                            )
                            ->default('0'),
                    ])
                    ->action(function (Subscription $record, array $data): void {

                        $record->update(
                            [
                                'next_date' => Carbon::parse($record->next_date)->addMonth(),
                            ]
                        );
                        $record->save();
                        Expense::create(
                            [
                                'place_id' => $record->place_id,
                                'user_id' => Auth::user()->id,
                                'category_id' => $record->category_id,
                                'sum' => $record->price,
                                'type_pay' => $data['type_pay'],
                                'comment' => $record->name,
                            ]
                        );
                    }),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubscriptions::route('/'),
        ];
    }
}
