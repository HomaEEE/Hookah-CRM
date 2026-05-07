<?php

namespace Modules\Indicators\Filament\Admin\Indicators\Resources;

use App\Enums\NavigationGroup;
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
use Modules\Indicators\Filament\Admin\Indicators\Resources\Pages\ManageIndications;
use Modules\Indicators\Models\Indication;
use Modules\Indicators\Models\Meter;
use UnitEnum;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indication::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Place;

    protected static ?int $navigationSort = 6;

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
                Select::make('meter_id')
                    ->label(__('Meter'))
                    ->options(
                        Meter::where('place_id', Filament::getTenant()->id)->pluck('type', 'id')
                    ),
                TextInput::make('data')
                    ->label(__('Indication')),
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
