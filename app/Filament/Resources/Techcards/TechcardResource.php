<?php

namespace App\Filament\Resources\Techcards;

use App\Filament\Resources\Techcards\Pages\ManageTechcards;
use App\Filament\Resources\Techcards\RelationManagers\IngredientRelationManager;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Techcard;
use App\Models\Workshop;
use Awcodes\TableRepeater\Components\TableRepeater;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Select::make('workshop_id')->label(__('Workshop'))
                    ->options(Workshop::where('place_id', Filament::getTenant()->id)->get()->pluck('name', 'id'))
                    ->live()
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

                // TableRepeater::make('ingredients')
                // ->schema([
                //     Select::make('ingredient_id')->label(__('Ingredient'))
                //             ->hiddenLabel()
                //             ->native(false)
                //             ->searchable()
                //             ->options(
                //                 Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                //             ),
                //     TextInputSelectAffix::make('ingredient_heft')
                //     ->hiddenLabel()
                //             ->numeric()
                //             ->select(
                //                 fn () => Select::make('ingredient_unit')
                //                     ->extraAttributes([
                //                         'class' => 'w-[72px]' // if you want to constrain the selects size, depending on your usecase
                //                     ])
                //                     ->options([
                //                         'ml' => __('ml'),
                //                         'gr' => __('gr'),
                //                         'p' => __('unit')

                //                     ])
                //             )
                // ])
                // ->reorderable()
                // ->cloneable()
                // ->collapsible()
                // ->defaultItems(3)
                // ->columnSpan('full'),

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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('process')->translateLabel(),
                RepeatableEntry::make('ingredients')->hiddenLabel()
                    ->schema([
                        TextEntry::make('ingredient.ingredient')->hiddenLabel(),
                        TextEntry::make('heft')->hiddenLabel()->badge(),
                        TextEntry::make('weight_unit')->hiddenLabel(),
                    ])
                    ->grid(3)
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            IngredientRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTechcards::route('/'),
        ];
    }
}
