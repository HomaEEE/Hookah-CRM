<?php

namespace App\Filament\Clusters\Data\Resources;

use App\Filament\Clusters\Data;
use App\Filament\Clusters\Data\Resources\DocResource\Pages;
use App\Models\Doc;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocResource extends Resource
{
    protected static ?string $model = Doc::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Data::class;

    protected static ?int $navigationSort = 1;

    public static function getPluralModelLabel(): string
    {
        return __('Docs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Docs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('name')->label(__('Name'))->required(),
                Select::make('type')->label(__('Type'))
                    ->options([
                        '1' => __('Pass'),
                        '2' => __('License'),
                        '3' => __('EDR'),
                        '4' => __('Key'),
                        '5' => __('Contract'),
                    ])->required(),
                DatePicker::make('date')->label(__('Date Expired'))->native(false),
                SpatieMediaLibraryFileUpload::make('files')->translateLabel()
                    ->collection('docs')
                    ->multiple()
                    ->imageResizeTargetWidth('1600')
                    ->imageResizeMode('cover')
                    ->reorderable()
                    ->openable()
                    ->imagePreviewHeight('250')
                    ->loadingIndicatorPosition('left')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('type')->translateLabel()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            '1' => __('Pass'),
                            '2' => __('License'),
                            '3' => __('EDR'),
                            '4' => __('Key'),
                            '5' => __('Contract'),
                        }),
                TextColumn::make('date')->dateTime('d/m/Y')->label(__('Date Expired'))->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')->translateLabel(),
                TextEntry::make('type')->translateLabel(),
                TextEntry::make('date')->label(__('Date Expired'))->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocs::route('/'),
            // 'index' => Pages\ListDocs::route('/'),
            // 'create' => Pages\CreateDoc::route('/create'),
            // 'edit' => Pages\EditDoc::route('/{record}/edit'),
        ];
    }
}
