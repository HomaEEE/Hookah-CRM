<?php

namespace App\Livewire\Dashboard;

use App\Models\Supply;
use Filament\Facades\Filament;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class Paying extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable
    {
        return __('Supplies to pay');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultGroup('supplier.company')
            ->groups([
                Group::make('bill.name')->label(__('Bill'))
                    ->titlePrefixedWithLabel(false),
                Group::make('supplier.company')->label(__('Supplier'))
                    ->titlePrefixedWithLabel(false),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->query(Supply::query()->where('place_id', Filament::getTenant()->id)->with('supplier')
                ->where('pay', 0))
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->description(fn (Supply $record): string => $record->user->name ?? ''),
                TextColumn::make('supplier.company')->label(__('Supplier'))->badge(),

                TextColumn::make('delay_day')->label(__('Delay'))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        default => 'info',
                        $state < 1 => 'danger',
                        $state < 3 => 'warning',
                        $state < 7 => 'success',
                    }),
                TextColumn::make('sum')->label(__('Sum'))->money('uah')->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),

                IconColumn::make('bill.type')->label(__('Bill'))
                    ->action(function ($record) {
                        $record->update([
                            'bill_id' => ! $record->bill_id,
                        ]);
                    }),

                SpatieMediaLibraryImageColumn::make('invoice')->label(__('Invoice'))
                    ->collection('supplies')
                    ->simpleLightbox()
                    ->circular()
                    ->stacked()
                    ->limitedRemainingText()
                    ->height(30)
                    ->toggleable()
                    ->defaultImageUrl(url('/assets/imgs/app/no-photo.png'))
                    ->placeholder('No description.'),

                IconColumn::make('pay')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
            ])->defaultSort('created_at', 'desc')
            ->actions([
                Actions\ViewAction::make()->label(''),
                Actions\EditAction::make()->label(''),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('added_at')->label(__('Date Added'))->dateTime('d F Y')
                    ->badge(),
                TextEntry::make('user.name')->translateLabel(),
                TextEntry::make('supplier.company')->label(__('Supplier'))->badge(),
                TextEntry::make('sum')->label(__('Sum'))->badge()->money('UAH')->copyable(),
                TextEntry::make('bill.name')->translateLabel()
                    ->badge(),
                TextEntry::make('invoice_number')->translateLabel()->badge()->copyable(),

                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make(__('Rekvizites'))
                            ->schema([
                                TextEntry::make('reqvizite.own')->label(__('FOP'))->badge()->copyable(),
                                TextEntry::make('reqvizite.IBAN')->label(__('IBAN'))->badge()->copyable(),
                                TextEntry::make('reqvizite.INN')->label(__('INN'))->badge()->copyable(),
                            ])
                            ->columns(3),
                        Tabs\Tab::make(__('Comment'))
                            ->schema([
                                TextEntry::make('comment')->label(__('Comment'))->copyable()->hiddenLabel(),
                            ]),
                        Tabs\Tab::make(__('Photo'))
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('invoice')
                                    ->collection('supplies')
                                    ->simpleLightbox()
                                    ->hiddenLabel(),
                            ]),
                        Tabs\Tab::make(__('Supply'))
                            ->schema([
                                // SpatieMediaLibraryImageEntry::make('invoice')
                                // ->collection('supplies')
                                // ->simpleLightbox()
                                // ->hiddenLabel(),
                            ]),
                    ])->columnSpanFull()
                    ->contained(false),

                // ComponentsSection::make(__('List'))->schema([
                //     RepeatableEntry::make('supplyItems')->schema([
                //         TextEntry::make('ingredient.ingredient'),
                //         TextEntry::make('qty')->translateLabel(),
                //         TextEntry::make('sum')->translateLabel()->badge(),
                //     ])->columns(3)
                //     ->contained(false)

                // ])->columns(1)->collapsed(),

            ])->columns(3);
    }
}
