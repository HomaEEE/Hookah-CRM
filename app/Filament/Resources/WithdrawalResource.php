<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Bill;
use App\Models\Place;
use App\Models\Withdrawal;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Withdrawals');
    }

    public static function getNavigationLabel(): string
    {
        return __('Withdrawals');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('sum')->label(__('Sum'))->numeric()->prefix('₴')->default(3000),
                Select::make('bill_id')->label(__('Bill'))
                ->relationship('bills', 'name')
                ->options(Bill::get()->pluck('name', 'id'))->default(0),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('d/m/Y')->translateLabel()->badge(),
                TextColumn::make('sum')->translateLabel()
                ->summarize(Sum::make()->money('uah')->label(__('Total'))),
                TextColumn::make('bill.name')->badge()
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultGroup('bill.name')
            ->filters([
                Filter::make('created_at')
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
                            $indicators['created_from'] = 'Расходы с ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Расходы до ' . Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
                SelectFilter::make('bill_id')
                ->options(Bill::get()->pluck('name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWithdrawals::route('/'),
        ];
    }
}
