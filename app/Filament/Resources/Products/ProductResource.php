<?php

namespace App\Filament\Resources\Products;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Position');
    }

    public static function getNavigationLabel(): string
    {
        return __('Positions');
    }

    protected function getTableQuery(): Builder
    {
        return Product::query()->with('childs', 'parent')->fastPaginate();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make()
                    ->contained(false)
                    ->columns([
                        'default' => 3,
                        'md' => 3,
                        'xl' => 3,
                    ])
                    ->schema([
                        ToggleButtons::make('visible')->label(__('Visible'))->inline()->boolean()->grouped()->columns(1),
                        ToggleButtons::make('visible_menu')->label(__('Visible menu'))->inline()->boolean()->grouped()->columns(1),
                    ]),

                TextInput::make('product_name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255)
                    ->columns(1),

                Select::make('menu_category_id')
                    ->label(__('Category'))
                    ->options(Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id'))
                    ->searchable(),

                TranslatableTabs::make()
                    ->addEmptyBadgeWhenAllFieldsAreEmpty(emptyLabel: __('locale empty'))
                    ->schema([
                        TextInput::make('frontend_name')->label(__('Front product name')),
                        Textarea::make('frontend_desc')->label(__('Description')),
                    ]),
                TextInput::make('sup')->label(__('sup')),
                TextInput::make('barcode')->label('Barcode'),

                TextInput::make('out')->label(__('Out'))->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                TextInput::make('price')->label(__('Price'))->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                    ->required(),
                Textarea::make('ingredients')->label(__('Ingredients'))
                    ->visible(fn ($record) => ! empty($record['ingredients']) ? '1' : '0'),

                SpatieMediaLibraryFileUpload::make('media')
                    ->collection('products')
                    ->image()
                    ->imagePreviewHeight('400')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeMode('contain'),
            ])->columns(1);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Product $record) => match ($record->visibleMenu) {
                0 => 'border-1-4 |border-l-danger-500 bg-danger-50 dark:bg-gray-800',
                default => null,
            })
            ->recordTitleAttribute('product__name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->collection('products')
                    ->circular()->size(50),

                TextColumn::make('product_name')->searchable()->wrap()
                    ->description(fn (Product $record): string => $record->frontend_name ?? '', position: 'before')
                    ->description(fn (Product $record): string => $record->category['category_name'] ?? '', position: 'above'),

                TextColumn::make('cost')->money('uah')->translateLabel()
                    ->searchable()
                    ->badge('info')
                    ->visibleFrom('md')
                    ->sortable(),

                TextColumn::make('rrc')->money('uah')->badge('info')->translateLabel(),
                TextColumn::make('price')->money('uah')->badge('success')->translateLabel()
                    ->searchable()
                    ->sortable(),
                // IconColumn::make('visible')
                //    ->label(__('Visible'))
                //    ->size(IconColumn\IconColumnSize::Medium)
                //    ->boolean()
                //    ->trueIcon('heroicon-o-eye')
                //    ->falseIcon('heroicon-o-eye-slash')
                //     ->action(function ($record, $column) {
                //         $name = $column->getName();
                //         $record->update([
                //             $name => ! $record->$name,
                //         ]);
                //     }),

                IconColumn::make('visible_menu')
                    ->label(__('Visible menu'))
                    ->size(IconSize::Medium)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->extraAttributes(function (Product $product) {
                        if ($product->visible_menu === 0) {
                            return ['class' => 'bg-danger-300 dark:bg-danger-600'];
                        }

                        return [];
                    })
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
            ])
            ->defaultPaginationPageOption(25)
            ->striped()
            ->filters([
                SelectFilter::make('menu_category_id')
                    ->label(__('Category'))
                    ->native(false)
                    ->options(
                        Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id')
                    ),
                TernaryFilter::make('visible_menu')
                    ->label(__('Visible'))
                    ->nullable()
                    ->native(false)
                    ->placeholder(__('All'))
                    ->trueLabel(__('Visible'))
                    ->falseLabel(__('Not'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('visible_menu', 1),
                        false: fn (Builder $query) => $query->where('visible_menu', 0),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    ),
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ManageProducts::route('/'),
        ];
    }
}
