<?php

namespace Modules\Cms\Filament\Cms\Resources\Pages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Cms\Filament\Cms\Resources\Pages\Pages\CreatePage;
use Modules\Cms\Filament\Cms\Resources\Pages\Pages\EditPage;
use Modules\Cms\Filament\Cms\Resources\Pages\Pages\ListPages;
use Modules\Cms\Filament\Cms\Resources\Pages\Pages\ViewPage;
use Modules\Cms\Filament\Cms\Resources\Pages\Schemas\PageForm;
use Modules\Cms\Filament\Cms\Resources\Pages\Schemas\PageInfolist;
use Modules\Cms\Filament\Cms\Resources\Pages\Tables\PagesTable;
use Modules\Cms\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Page';

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record}'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
