<?php

namespace App\Filament\Resources\Expenses;

use App\Filament\Components\Forms\ExpenseForm;
use App\Filament\Resources\Expenses\Pages\ManageExpenses;
use App\Filament\Resources\Expenses\Tables\ExpenseTable;
use App\Models\Expense;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $tentantOwnershipRelationName = 'owner';

    // protected static ?string $tentantRelationName = 'placeLabel';

    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category', 'user', 'supply');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                ExpenseForm::schema()
            );
    }

    public static function table(Table $table): Table
    {
        return ExpenseTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('added_at')->translateLabel()->badge()->dateTime('d F Y'),
                TextEntry::make('user.name')->translateLabel()->badge(),
                TextEntry::make('bill.name')->translateLabel()->badge(),
                TextEntry::make('category.name')->translateLabel()->badge(),
                TextEntry::make('sum')->translateLabel()->badge(),
                TextEntry::make('comment')->translateLabel()->badge(),
                SpatieMediaLibraryImageEntry::make('invoice')
                    ->collection('supplies')
                    ->lightbox()
                    ->hiddenLabel(),
                // ImageEntry::make('invoice')
                // ->simpleLightbox(fn ($record) =>  $record?->image ?? "Your Image Url address", defaultDisplayUrl: true)
                // ->translateLabel(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExpenses::route('/'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // App\Filament\Widgets\ExpenseChart::class
        ];
    }
}
