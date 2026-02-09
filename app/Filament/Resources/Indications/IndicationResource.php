<?php

namespace App\Filament\Resources\Indications;

use App\Filament\Resources\Indications\Pages\ManageIndications;
use App\Models\Indication;
use App\Models\Meter;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class IndicationResource extends Resource
{
    protected static ?string $model = Indication::class;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Indications');
    }

    public static function getNavigationLabel(): string
    {
        return __('Indications');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TableRepeater::make('data')
                    ->headers(['Meter', 'Indication'])
                    ->emptyLabel('Add ingredients to techcard')
                    ->schema([
                        Select::make('meter_id')->label(__('Meter'))
                            ->options(
                                Meter::where('place_id', Filament::getTenant()->id)->pluck('type', 'id')
                            ),
                        TextInput::make('indication'),
                    ])->columnSpan('full'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIndications::route('/'),
        ];
    }
}
