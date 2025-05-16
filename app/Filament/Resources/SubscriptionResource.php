<?php

namespace App\Filament\Resources;

use App\Enums\Subscription as SubscriptionEnum;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Subscription;
use Auth;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('Subscriptions');
    }

    public static function getNavigationLabel(): string
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
                TextColumn::make('name')
                    ->label(__('Destination'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('xl')
                    ->description(fn (Subscription $record): string => $record->category->name ?? '', position: 'above')
                    ->toggleable(),

                TextColumn::make('next_date')
                    ->label(__('next date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('price')
                    ->translateLabel()
                    ->money('uah')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

            ])
            ->defaultSort('next_date', 'asc')
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
                        ToggleButtons::make('bill_id')->label(__('Type pay'))
                            ->options(Bill::get()->pluck('name', 'id'))
                            ->default(0)
                            ->required()
                            ->inline()
                            ->grouped()
                            ->live(),
                    ])
                    ->action(function (Subscription $record, array $data): void {

                        $record->update(
                            [
                                'next_date' => Carbon::today()->addMonth(),
                                'price' => $data['sum'],
                            ]
                        );
                        $record->save();
                        Expense::create(
                            [
                                'place_id' => $record->place_id,
                                'user_id' => Auth::user()->id,
                                'category_id' => $record->category_id,
                                'sum' => $data['sum'],
                                'bill_id' => $data['bill_id'],
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
