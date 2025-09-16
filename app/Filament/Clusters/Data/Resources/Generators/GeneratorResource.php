<?php

namespace App\Filament\Clusters\Data\Resources\Generators;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Clusters\Data\Resources\Generators\Pages\ManageGenerators;
use App\Filament\Clusters\Data\DataCluster;
use App\Filament\Clusters\Data\Resources\GeneratorResource\Pages;
use App\Models\Generator;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    protected static ?string $cluster = DataCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 999;

    public static function getPluralModelLabel(): string
    {
        return __('Generator');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('Data');
    // }

    public static function getNavigationLabel(): string
    {
        return __('Generator');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('tech')->translateLabel()->required()->columns(1),
                Select::make('box.num')->label(__('Switch Box'))->options([
                    '1' => __('Bar'),
                    '2' => __('Hookah'),
                    '3' => __('Kitchen'),
                ])->native(false),
                TextInput::make('box.switch')->label(__('Switcher'))->numeric(),
                TextInput::make('load')->translateLabel()->numeric(),
                TextInput::make('phase')->translateLabel()->numeric()->minValue(1)->maxValue(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tech')->translateLabel(),
                TextColumn::make('box.num')->label(__('Switch Box'))->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => __('Bar'),
                        '2' => __('Hookah'),
                        '3' => __('Kitchen'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => __('Bar'),
                        '2' => __('Hookah'),
                        '3' => __('Kitchen'),
                    }),
                TextColumn::make('box.switch')->label(__('Switcher')),

                TextColumn::make('load')->translateLabel()
                    ->formatStateUsing(fn (string $state): string => $state / 100),
                TextColumn::make('phase')->translateLabel()->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'warning',
                        '2' => 'success',
                        '3' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['load'] = $data['load'] * 100;

                        return $data;
                    })
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['load'] = $data['load'] / 100;

                        return $data;
                    }),
                DeleteAction::make(),
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
            'index' => ManageGenerators::route('/'),
        ];
    }
}
