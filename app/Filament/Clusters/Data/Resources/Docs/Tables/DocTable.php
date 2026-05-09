<?php

namespace App\Filament\Clusters\Data\Resources\Docs\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->translateLabel(),
            TextColumn::make('type')->translateLabel()
                ->formatStateUsing(
                    fn (string $state): string => match ($state) {
                        '1' => __('Pass'),
                        '2' => __('License'),
                        '3' => __('EDR'),
                        '4' => __('Key'),
                        '5' => __('Contract'),
                    }),
            TextColumn::make('date')->dateTime('d/m/Y')->label(__('Date Expired'))->badge(),
        ])->filters([
            //
        ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
