<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechcardResource\Pages;
use App\Filament\Resources\TechcardResource\RelationManagers;
use App\Models\{Ingredient, Techcard, Workshop};

use Auth;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
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

    protected static ?string $recordTitleAttribute = 'techcard';

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

                Textarea::make('process')->label(__('Process')),

                TableRepeater::make('ingredients')
                ->schema([
                    Select::make('ingredient_id')->label(__('Ingredient'))
                    ->hiddenLabel()
                    ->native(false)
                    ->searchable()
                        ->options(
                            Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                        ),
                    TextInput::make('heft')->suffix('гр.')->translateLabel()
                    ->hiddenLabel()
                ])->columnSpan('full')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workshop.name')->translateLabel()->searchable(),
                TextColumn::make('techcard')->translateLabel(),
            ])
            ->filters([

            ])
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
            TextEntry::make('ingredients[0]'),
            RepeatableEntry::make('ingredients')->hiddenLabel()
            ->schema([
                TextEntry::make('ingredient')->hiddenLabel(),
                TextEntry::make('heft')->hiddenLabel()->badge()
            ])
            ->columns(2)
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
