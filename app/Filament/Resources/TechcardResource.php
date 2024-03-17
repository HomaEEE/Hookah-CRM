<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechcardResource\Pages;
use App\Filament\Resources\TechcardResource\RelationManagers;
use App\Models\{Ingredient, Product, Techcard, Workshop};

use Auth;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
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
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;

class TechcardResource extends Resource
{
    protected static ?string $model = Techcard::class;

    protected static ?string $recordTitleAttribute = 'techcard';

    protected static ?int $navigationSort = 7;

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
                    ->options(Workshop::where('place_id', Filament::getTenant()->id)->get()->pluck('name', 'id'))
                    ->reactive()
                    ->required(),
                Select::make('techcard')
                    ->searchable()
                    ->native(false)
                    ->options(Product::where('place_id', Filament::getTenant()->id)->get()->pluck('product_name', 'id')),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->collection('techcard')
                    ->multiple()
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeMode('cover')
                    ->imagePreviewHeight(350)
                    ->openable(),

                Textarea::make('process')->label(__('Process')),

                TableRepeater::make('ingredients')
                ->relationship('ingredients')
                ->schema([
                    Select::make('ingredient_id')->label(__('Ingredient'))
                            ->hiddenLabel()
                            ->native(false)
                            ->searchable()
                            ->options(
                                Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                            ),
                    TextInputSelectAffix::make('heft')
                    ->hiddenLabel()
                            ->numeric()
                            ->select(
                                fn () => Select::make('weight_unit')
                                    ->extraAttributes([
                                        'class' => 'w-[72px]' // if you want to constrain the selects size, depending on your usecase
                                    ])
                                    ->options([
                                        'ml' => __('ml'),
                                        'gr' => __('gr')
                                    ])
                            )
                ])
                ->reorderable()
                ->cloneable()
                ->collapsible()
                ->defaultItems(3)
                ->columnSpan('full'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workshop.name')->translateLabel()->searchable(),
                TextColumn::make('techcard')->translateLabel(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('process')->translateLabel(),
                RepeatableEntry::make('ingredients')->hiddenLabel()
                    ->schema([
                        TextEntry::make('ingredient.ingredient')->hiddenLabel(),
                        TextEntry::make('heft')->hiddenLabel()->badge(),
                        TextEntry::make('weight_unit')->hiddenLabel()
                    ])
                    ->grid(3)
                    ->columns(3)
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
