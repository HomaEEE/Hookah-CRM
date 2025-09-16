<?php

namespace App\Filament\Clusters\Settings\Resources\Places\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Enums\Bill as BillEnum;
use App\Enums\Currency;
use App\Http\Controllers\PosterApiController;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BillsRelationManager extends RelationManager
{
    protected static string $relationship = 'bills';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getPluralModelLabel(): string
    {
        return __('Bills');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bills');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('Bill name')),
                Select::make('currency')->label(__('Currency'))
                    ->options(Currency::class),
                Select::make('type')->label(__('Type bill'))
                    ->options(BillEnum::class),
                TextInput::make('balance')->label(__('Bill balance')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('currency')->badge(),
                TextColumn::make('type')->badge(),
                TextColumn::make('balance'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('getBills')
                    ->label(__('Get Bills'))
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->slideOver()
                    ->modalHeading(__('Get Bills'))
                    ->requiresConfirmation()
                    ->action(function (): void {
                        (new PosterApiController)->getBills($this->ownerRecord->id);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }
}
