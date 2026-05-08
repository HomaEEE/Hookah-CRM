<?php

namespace App\Filament\Clusters\Data\Resources\Docs;

use App\Filament\Clusters\Data\DataCluster;
use App\Filament\Clusters\Data\Resources\DocResource\Pages;
use App\Filament\Clusters\Data\Resources\Docs\Pages\ManageDocs;
use App\Filament\Clusters\Data\Resources\Docs\Tables\DocTable;
use App\Filament\Resources\Docs\Schemas\DocForm;
use App\Models\Doc;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use JibayMcs\Tabbed\Traits\HasTabbedActions;

class DocResource extends Resource
{
    use HasTabbedActions;

    protected static ?string $model = Doc::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = DataCluster::class;

    public static function getPluralModelLabel(): string
    {
        return __('Docs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Docs');
    }

    public static function form(Schema $schema): Schema
    {
        return DocForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDocs::route('/'),
            // 'index' => Pages\ListDocs::route('/'),
            // 'create' => Pages\CreateDoc::route('/create'),
            // 'edit' => Pages\EditDoc::route('/{record}/edit'),
        ];
    }
}
