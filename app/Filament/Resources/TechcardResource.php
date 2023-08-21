<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechcardResource\Pages;
use App\Filament\Resources\TechcardResource\RelationManagers;
use App\Models\{Ingredient, Techcard, Workshop};

use Auth;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Get;
use Filament\Forms\Set;

use Forms\Components\Actions\Action;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TechcardResource extends Resource
{
    protected static ?string $model = Techcard::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getPluralModelLabel(): string
    {
        return __('Techcards');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Menu');
    }

    public static function getNavigationLabel(): string
    {
        return __('Techcards');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('place_id')->label(__('Place'))
                    ->options(
                        Auth::user()->place->pluck('nameCity', 'id')
                    )
                    ->reactive()
                    ->required(),

                Select::make('workshop_id')->label(__('Workshop'))
                    ->options(function (Get $get) {
                        return Workshop::where('place_id', $get('place_id'))->get()->pluck('name', 'id');
                    })
                    ->reactive()
                    ->required(),
                TextInput::make('techcard')->translateLabel(),
                TimePicker::make('time')->translateLabel(),

                Textarea::make('process')->label(__('Process')),
                Repeater::make('ingredients')
                    ->schema([
                        Select::make('ingredient_id')->label(__('ingredient'))
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required()
                        //    ->hidden(fn (Get $get) => $get('workshop_id') == null)
                            ->options(function (callable $get) {
                                if (null == ($get('workshop_id'))) {
                                    return null;
                                } else {
                                    return Ingredient::get()->pluck('ingredient', 'id');
                                }
                            })
                            ->createOptionForm([
                                Select::make('place_id')->label(__('Place'))
                                    ->options(
                                        Auth::user()->place->pluck('nameCity', 'id')
                                    )
                                    ->reactive()
                                    ->required(),

                                Select::make('workshop_id')->label(__('Workshop'))
                                    ->options(function (Get $get) {
                                        return Workshop::where('place_id', $get('place_id'))->get()->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->required(),

                                TextInput::make('ingredient')->label(__('ingredient'))
                                    ->required(),

                                Select::make('unit')
                                    ->options([
                                        'kg' => 'Kg',
                                        'gr' => 'Gr',
                                        'l' => 'L',
                                        'ml' => 'Ml',
                                        'unit' => 'Unit',
                                    ])
                                    ->required()
                            ]),

                        TextInput::make('heft')->label(__('Heft'))
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('place.city')->translateLabel()
                    ->badge(),
                TextColumn::make('workshop.name')->translateLabel(),
                TextColumn::make('product_name')->translateLabel(),
                TextColumn::make('time')->translateLabel(),
            ])
            ->filters([
                SelectFilter::make('Place')
                    ->label(__('Place'))
                    // build the filter form
                    ->form([
                        Select::make('place_id')->label(__('Place'))
                            ->searchable()
                            ->options(
                                Auth::user()->place->pluck('nameCity', 'id')
                            )
                            ->afterStateUpdated(
                                function (callable $set) {
                                    $set('supplier_id', null);
                                }
                            ),
                        Select::make('workshop_id')->label(__('Workshop'))
                            ->searchable()
                            ->hidden(fn (Get $get) => $get('place_id') == null)
                            ->preload()
                            ->options(function (callable $get) {
                                if (null == ($get('place_id'))) {
                                    return null;
                                } else {
                                    return Workshop::where('place_id', $get('place_id'))->pluck('name', 'id');
                                }
                            }),
                    ])
                    ->query(
                        function (Builder $query, array $data) {
                            $place_id = (int) $data['place_id'];
                            $workshop_id = (int) $data['workshop_id'];

                            if (!empty($workshop_id)) {
                                $query->where('workshop_id', '=', $workshop_id);
                            }

                            if (!empty($place_id)) {
                                $query->where('place_id', '=', $place_id);
                            }
                        }
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\IngredientRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTechcards::route('/'),
        ];
    }
}
