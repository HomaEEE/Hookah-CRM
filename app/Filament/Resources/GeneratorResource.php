<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneratorResource\Pages;
use App\Filament\Resources\GeneratorResource\RelationManagers;
use App\Models\Generator;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    public static function getPluralModelLabel(): string
    {
        return __('Generator');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Data');
    }

    public static function getNavigationLabel(): string
    {
        return __('Generator');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('tech')->translateLabel()->required(),
                TextInput::make('load')->translateLabel()->numeric(),
                TextInput::make('phase')->translateLabel()->numeric()->minValue(1)->maxValue(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tech')->translateLabel(),
                TextColumn::make('load')->translateLabel()
                ->formatStateUsing(fn (string $state): string => $state/100),
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
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['load'] =  $data['load']*100;
                    return $data;
                })
                ->mutateRecordDataUsing(function (array $data): array {
                    $data['load'] =  $data['load']/100;
                    return $data;
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGenerators::route('/'),
        ];
    }
}
