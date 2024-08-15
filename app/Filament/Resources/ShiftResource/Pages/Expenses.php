<?php

namespace App\Filament\Resources\ShiftResource\Pages;

use App\Filament\Resources\ShiftResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Expenses extends ManageRelatedRecords
{
    protected static string $resource = ShiftResource::class;

    protected static string $relationship = 'expenses';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Expenses')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Expenses')
            ->modifyQueryUsing(fn (Builder $query) =>
            $query->where(
            'place_id', $this->getOwnerRecord()->place_id)
                    ->whereDate('added_at', $this->getOwnerRecord()->created_at ) )
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                ->size('sm')
                ->badge()
                ->description(fn ($record): string => $record->user->name ?? ''),
                TextColumn::make('category.name')->translateLabel()->badge()
                ->description(fn ($record): string => $record->supply->supplier->company ?? $record->comment ?? ''),
                TextColumn::make('sum')->translateLabel()->money('UAH', 100)->badge()
                ->summarize(Sum::make()->label(__('Total'))),
                IconColumn::make('bill.type')->label(__('Bill')),
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
