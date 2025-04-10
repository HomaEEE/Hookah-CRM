<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;

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
                    ->reactive()
                    ->required(),
                // Select::make('buyer')->translateLabel()->default(1), // Покупатель если не Noire
                TableRepeater::make('ingredients')
                    ->hiddenLabel()
                    ->reactive()
                    ->hidden(fn (Get $get): bool => ! $get('supplier_id'))
                    ->minItems(1)
                    ->schema([
                        Select::make('ingredient_id')
                            ->preload()
                            ->label(__('Ingredient'))
                            ->columnSpan(8)
                            ->native(false)
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                return Ingredient::query()
                                    ->where(function (Builder $builder) use ($search) {
                                        $searchString = "%$search%";
                                        $builder->where('ingredient', 'like', $searchString);
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function (Ingredient $ingredient) {
                                        return [$ingredient->id => $ingredient->ingredient];
                                    })
                                    ->toArray();
                            }),
                        TextInput::make('qty')->translateLabel()
                            ->numeric()
                            ->label(__('Quantity'))
                            ->default(1)
                            ->columnSpan(2),
                        Select::make('qty_unit')
                            ->native(false)
                            ->columnSpan(2)
                            ->label(__('Unit'))
                            ->options([
                                'p' => __('Unit'),
                                'l' => __('Litr'),
                                'kg' => __('Kg'),
                            ])
                            ->default('p'),
                    ])
                    ->colStyles([
                        'ingredient_id' => 'width: 45%;',
                        'qty' => 'width: 25%;',
                        'qty_unit' => 'width: 20%;',
                    ]),
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
                ]),

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
                                Components\TextEntry::make('qty_unit')->hiddenLabel(),
                            ])
                            ->grid(3)
                            ->contained(false),
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
