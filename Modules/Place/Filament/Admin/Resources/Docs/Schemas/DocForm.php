<?php

namespace Modules\Place\Filament\Admin\Resources\Docs\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->automaticallyResizeImagesToWidth('1600')
                    ->automaticallyResizeImagesMode('cover')
                    ->reorderable()
                    ->openable()
                    ->imagePreviewHeight('250')
                    ->loadingIndicatorPosition('left')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left'),
            ])->columns(1);
    }
}
