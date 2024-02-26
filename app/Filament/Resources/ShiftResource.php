<?php

namespace App\Filament\Resources;

use Auth;
use Carbon\Carbon;

use Filament\Forms\Get;
use Filament\Forms\Set;

use App\Filament\Resources\ShiftResource\Pages;
use App\Filament\Resources\ShiftResource\RelationManagers;

use App\Http\Controllers\PosterApiController as PosterApi;
use App\Http\Controllers\LocalApiController as LocalApi;
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
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Fieldset as InfolistFeildSet;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Grouping\Group;

use Filament\Tables\Columns\Summarizers\Sum;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 1;

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
        return Shift::query()->with('place');
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

                TextColumn::make('sum')->translateLabel()->badge()
                    ->state(function (Shift $record): int {
                        return $record->terminal * 0.987 + $record->cash;
                    })->money('UAH', true),

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
