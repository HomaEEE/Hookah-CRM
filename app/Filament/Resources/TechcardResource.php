<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechcardResource\Pages;
use App\Filament\Resources\TechcardResource\RelationManagers;
use App\Models\{Ingredient, Techcard, Workshop};

use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

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
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Select::make('workshop_id')->label(__('Workshop'))
                    ->options(function (Get $get) {
                        return Workshop::where('place_id', $get('place_id'))->get()->pluck('name', 'id');
                    })
                    ->reactive()
                    ->required(),
                TextInput::make('techcard')->translateLabel(),

                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('photo')
                                    ->collection('techcard')
                                    ->multiple()
                                    ->imageResizeTargetWidth('1200')
                                    ->imageResizeMode('cover')
                                    ->imagePreviewHeight(350)
                                    ->openable()
                            ])
                            ->columnSpan(['lg' => 2, 'md' => 2, 'xs' => 1]),
                    ])->columns(2),
                Textarea::make('process')->label(__('Process')),
                Repeater::make('ingredients')
                    ->relationship()
                    ->schema([
                        Select::make('ingredient_id')->label(__('ingredient'))
                        ->relationship('ingredient', 'ingredient')
                            ->native(false)
                            ->required()
                            ->options(function (callable $get) {
                                    return Ingredient::where('place_id', $get('place_id'))->get()->pluck('ingredient', 'id');
                            })
                            ->createOptionForm([
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                TextInput::make('ingredient')->label(__('ingredient'))->required(),
                                Select::make('unit')
                                    ->options([
                                        'kg' => __('Kg'),
                                        'gr' => __('Gr'),
                                        'l' => __('L'),
                                        'ml' => __('Ml'),
                                        'unit' => __('Unit'),
                                    ])
                                    ->required()
                            ]),

                        TextInput::make('heft')->label(__('Heft'))->suffix('.com')
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workshop.name')->translateLabel(),
                TextColumn::make('techcard')->translateLabel(),
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


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('techcard')->translateLabel(),
            TextEntry::make('time')->translateLabel(),
            TextEntry::make('process')->translateLabel(),

            RepeatableEntry::make('ingredients')
            ->schema([
                TextEntry::make('ingredient')
            ])
            ->grid(2)
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
