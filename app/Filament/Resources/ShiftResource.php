<?php

namespace App\Filament\Resources;

use App\Filament\Components\Filters\LastMonthRecords;
use Auth;
use Carbon\Carbon;

use App\Filament\Resources\ShiftResource\Pages;
use App\Filament\Resources\ShiftResource\RelationManagers;

use App\Http\Controllers\PosterApiController as PosterApi;
use App\Http\Controllers\LocalApiController as LocalApi;
use App\Models\{Bill, Expense, ExpenseCategory, Place, Workshop, Shift, Supplier, User, Wage, Withdrawal};
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Fieldset as InfolistFeildSet;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\DB;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

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
        return Shift::query()->fastPaginate();
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('l')->translateLabel()
                ->badge()
                ->size('xs')
                ->description(fn (Shift $record): string => $record->created_at->format('d-m-Y') ?? ''),

                TextColumn::make('terminal')->translateLabel()->badge()
                    ->money('UAH')
                    ->state(function (Shift $record): int {
                        return $record->terminal * 0.987;
                    })
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

                TextColumn::make('cash')->translateLabel()->badge()->color('warning')
                    ->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

                TextColumn::make('sum')->translateLabel()->badge()->color('gray')
                    ->state(function (Shift $record): int {
                        return $record->terminal * 0.987 + $record->cash;
                    })->money('UAH'),

                TextColumn::make('shift')->color('info')
                    ->badge()
                    ->money('UAH')->translateLabel(),

                //   TextColumn::make('shift_users.user_id')->badge(),

            ])->defaultSort('id', 'desc')
            ->filters(
                LastMonthRecords::schema()
            )

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistFeildSet::make('Shift')->translateLabel()
                    ->schema([
                        TextEntry::make('cash')->translateLabel()
                            ->badge()->color('warning'),
                        TextEntry::make('terminal')->translateLabel()
                            ->badge(),

                        TextEntry::make('sum')->translateLabel()
                            ->badge()
                            ->color('gray')
                            ->getStateUsing(fn (Shift $record): int => $record->cash + $record->terminal ?? ''),

                        TextEntry::make('calc_shift')->translateLabel()
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('shift')->translateLabel()
                            ->badge()
                            ->color('info'),

                        TextEntry::make('different')->translateLabel()
                            ->badge()
                            ->color('danger')
                            ->getStateUsing(fn (Shift $record): int => $record->shift - $record->calc_shift ?? ''),
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
                        IconEntry::make('report.x-report')->label(__('X reports'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check')
                            ->falseIcon('heroicon-o-x-mark'),

                        IconEntry::make('report.z-report')->label(__('Z reports'))
                            ->trueIcon('heroicon-o-check')
                            ->falseIcon('heroicon-o-x-mark'),


                    ])->columns(3)
            ]);
    }




    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewShift::class,
            Pages\Users::class,
            Pages\Expenses::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'view' => Pages\ViewShift::route('/{record}/view'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
            'expenses' => Pages\Expenses::route('/{record}/expenses'),
            'users' => Pages\Users::route('/{record}/users'),
        ];
    }
}
