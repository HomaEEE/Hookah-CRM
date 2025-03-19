<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?int $navigationSort = 6;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('visible')->label(__('Visible'))->inline(),
                Toggle::make('visible_menu')->label(__('Visible menu'))->inline(),
                TextInput::make('product_name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),

                Select::make('menu_category_id')
                    ->label(__('Category'))
                    ->options(Category::where('place_id', Filament::getTenant()->id)->pluck('category_name', 'category_id'))
                    ->searchable(),
                Translate::make()
                    ->schema([
                        TextInput::make('frontend_name')->label(__('Front product name'))->translatable(),
                        Textarea::make('frontend_desc')->label(__('Description'))->translatable(),
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
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('price')->money('uah')->translateLabel()
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
                    ->size(IconColumn\IconColumnSize::Medium)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
