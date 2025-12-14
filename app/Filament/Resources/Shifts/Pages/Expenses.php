<?php

namespace App\Filament\Resources\Shifts\Pages;

use App\Filament\Resources\Shifts\ShiftResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Expenses extends ManageRelatedRecords
{
    protected static string $resource = ShiftResource::class;

    protected static string $relationship = 'expenses';

    protected static ?string $recordTitleAttribute = 'date'; // или 'shift_date'

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public function getTitle(): string
    {

        return __('Expenses').' '.$this->record->created_at->translatedFormat('d F Y');
    }

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Expenses')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultGroup('bill.name')
            ->recordTitleAttribute('Expenses')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('added_at', $this->getOwnerRecord()->created_at))
            ->columns([
                TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->badge()
                    ->description(fn ($record): string => $record->user->name ?? ''),
                TextColumn::make('category.name')->translateLabel()->badge()
                    ->description(fn ($record): string => $record->supply->supplier->company ?? $record->comment ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),
                IconColumn::make('bill.type')->label(__('Bill')),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->recordActions([
            ])
            ->toolbarActions([

            ]);
    }
}
