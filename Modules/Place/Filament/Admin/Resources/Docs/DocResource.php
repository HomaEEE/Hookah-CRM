<?php

namespace Modules\Place\Filament\Admin\Resources\Docs;

use App\Enums\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Place\Filament\Admin\Resources\Docs\Pages\ManageDocs;
use Modules\Place\Filament\Admin\Resources\Docs\Schemas\DocForm;
use Modules\Place\Filament\Admin\Resources\Docs\Schemas\DocInfolist;
use Modules\Place\Filament\Admin\Resources\Docs\Tables\DocTable;
use Modules\Place\Models\Doc;
use UnitEnum;

class DocResource extends Resource
{
    // TODO move to Place modules all data resources
    protected static ?string $model = Doc::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Data;

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
