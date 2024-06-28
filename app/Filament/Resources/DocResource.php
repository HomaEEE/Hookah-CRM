<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocResource\Pages;
use App\Models\Doc;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use Filament\Tables;
use Filament\Tables\Table;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;

class DocResource extends Resource
{
    protected static ?string $model = Doc::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getPluralModelLabel(): string
    {
        return __('Docs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Data');
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
                        ->uploadProgressIndicatorPosition('left')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocs::route('/'),
        ];
    }
}
