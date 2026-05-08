<?php

namespace Modules\Finance\Filament\Admin\Resources\Subscriptions\Tables;

use Auth;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Modules\Finance\Models\Bill;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\Subscription;

class SubscriptionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('next_date')
                    ->label(__('Date'))
                    ->getTitleFromRecordUsing(fn ($record) => Carbon::parse($record->created_at)->translatedFormat('F Y'))
                    ->orderQueryUsing(fn ($query, $direction) => $query->orderByRaw("YEAR(next_date) $direction, MONTH(next_date) $direction")
                    ),
            ])
            ->defaultGroup('next_date')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Destination'))
                    ->searchable()
                    ->sortable()
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

            ->recordActions([
                Action::make('paid')->button()->translateLabel()
                    ->icon('heroicon-s-banknotes')
                    ->color('success')
                    ->modalIcon('heroicon-o-banknotes')
                    ->modalIconColor('success')
                    ->modalHeading('Оплата платежа')
                    ->requiresConfirmation()
                    ->modalWidth(Width::Small)
                    ->schema([
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
                            ->extraAttributes(['style' => 'width: 100%'])

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
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
