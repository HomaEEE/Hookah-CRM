<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use Auth;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationGroup = 'Stocks';

    protected static ?int $navigationSort = 4;


    public static function getPluralModelLabel(): string
    {
        return __('Purchase');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getNavigationLabel(): string
    {
        return __('Purchases');
    }

    public function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-view-grid-add';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Hidden::make('user_id')->default(Auth::user()->id),
                Select::make('supplier_id')->label(__('Supplier'))
                    ->columnSpan(12)
                    ->native(false)
                    ->searchable()
                    ->options(
                         Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id')
                        )
                    ->required(),
                //Select::make('buyer')->translateLabel(), Покупатель если не Noire
                TableRepeater::make('ingredients')->hiddenLabel()
                ->columnSpanFull()
                ->headers([
                    Header::make('Ingredient'),
                    Header::make('Count'),
                    Header::make('Unit'),
                ])
                    ->schema([
                        Select::make('ingredient_id')
                            ->preload()
                            ->columnSpan(8)
                            ->native(false)
                            ->searchable()
                            ->options(
                                Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                               ),

                        TextInput::make('qty')->translateLabel()
                            ->numeric()
                            ->default(1),
                        Select::make('qty_unit')
                            ->native(false)
                            ->options([
                                'p' => __('Unit'),
                                'l' => __('Litr'),
                                'kg' => __('Kg')
                            ])
                            ->default('p')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.company')->label(__('Supplier')),
                TextColumn::make('status')->translateLabel(),
                TextColumn::make('created_at')->translateLabel()->dateTime('d/m/Y')->badge(),
            ])
            ->filters([
                //
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
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(3)
                                ->schema([
                                    Components\TextEntry::make('supplier.company')->translateLabel(),
                                    Components\TextEntry::make('status')->translateLabel(),
                                    Components\TextEntry::make('created_at')
                                        ->translateLabel()
                                        ->badge()
                                        ->date()
                                        ->color('success'),

                                ]),
                        ]),
                    ]),
                    Components\Section::make('Content')->hiddenLabel()
                    ->schema([
                    Components\RepeatableEntry::make('ingredients')->hiddenLabel()
                    ->schema([
                        Components\TextEntry::make('ingredient')->hiddenLabel(),
                        Components\TextEntry::make('qty')->hiddenLabel(),
                        Components\TextEntry::make('qty_unit')->hiddenLabel()
                    ])
                    ->grid(3)
                    ->contained(false)
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'view' => Pages\ViewPurchase::route('/{record}/view'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
