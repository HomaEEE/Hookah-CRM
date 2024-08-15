<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\ShiftResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('users')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('shift_id', $this->getOwnerRecord()->id)
                      ->whereDate('created_at', $this->getOwnerRecord()->created_at ) )
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
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}
