<?php

namespace Modules\Manage\Filament\Admin\Resources\Excises;

use App\Enums\NavigationGroup;
use Auth;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\Manage\Filament\Admin\Resources\Excises\Pages\ManageExcises;
use Modules\Manage\Models\Excise;
use UnitEnum;

class ExciseResource extends Resource
{
    protected static ?string $model = Excise::class;

    protected static ?string $recordTitleAttribute = 'excise';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

    public static function getPluralModelLabel(): string
    {
        return __('Excises');
    }

    public static function getNavigationLabel(): string
    {
        return __('Excises');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Hidden::make('user_id')->default(Auth::user()->id),
                                Hidden::make('place_id')->default(Filament::getTenant()->id),
                                DateTimePicker::make('added_at')->label(__('Date Added'))->default(now())->native(false),
                                TextInput::make('title')->translateLabel(),
                                TextInput::make('excise')->translateLabel(),
                                Select::make('volume')->translateLabel()
                                    ->native(false)
                                    ->options([
                                        '0.5' => '0.5L',
                                        '0.7' => '0.7L',
                                        '0.75' => '0.75L',
                                        '1' => '1L',
                                    ]),
                            ]),
                    ]),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('excises')
                                    ->collection('excises')
                                    ->label(__('Photo'))
                                    ->imagePreviewHeight('350')
                                    ->automaticallyResizeImagesMode('cover')
                                    ->imageEditor()
                                    //                 ->optimize('webp')                                    ->resize(70)
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->reorderable()
                                    ->openable(),
                            ])
                            ->columnSpan(['lg' => 2, 'md' => 2, 'xs' => 1]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('added_at')->dateTime('d/m/Y')->badge()->translateLabel(),
                TextColumn::make('excise')->translateLabel()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::upper($state))
                    ->description(fn (Excise $record): string => $record->title ? $record->title : '', position: 'below')->copyable(),

                TextColumn::make('volume')->translateLabel(),
                TextColumn::make('portions')->translateLabel(),
                TextColumn::make('left')->translateLabel()
                    ->getStateUsing(function (Excise $record) {
                        // return whatever you need to show
                        return $record->volume * 1000 / 40 - $record->portions;
                    }),
            ])->defaultSort('added_at', 'desc')
            ->filters([

            ])
            ->recordActions([
                Action::make('approve')->iconButton()
                    ->icon('heroicon-o-arrow-uturn-up')
                    ->color('success')
                    ->action(function (Excise $record): void {
                        $record->update(
                            [
                                'open' => 1,
                            ]
                        );
                        $record->save();
                    })->visible(fn (Excise $record) => $record->open == '0'),
                Action::make('add_portion')->hiddenLabel()
                    ->icon('heroicon-o-plus')
                    ->button()
                    ->modalHeading(__('Portions'))
                    ->color('info')
                    ->modalWidth('xs')
                    ->schema([
                        TextInput::make('portions')
                            ->hiddenLabel()->numeric()
                            ->default(1)
                            ->required()
                            ->suffixAction(
                                Action::make('plus')
                                    ->size(Size::Large)
                                    ->color('success')
                                    ->icon('heroicon-o-plus')
                                    ->iconButton()
                                    ->disabled(fn (Get $get): bool => empty($get('portions')) || $get('portions') >= 25)
                                    ->action(function (Get $get, Set $set, $state): void {
                                        $set('portions', $get('portions') + 1);
                                    })
                            )
                            ->prefixAction(
                                Action::make('minus')
                                    ->size(Size::Large)
                                    ->icon('heroicon-o-minus')
                                    ->color('danger')
                                    ->iconButton()
                                    ->disabled(fn (Get $get): bool => empty($get('portions')) || $get('portions') <= 1)
                                    ->action(function (Get $get, Set $set, $state): void {
                                        $set('portions', $get('portions') - 1);
                                    })
                            ),

                    ])
                    ->action(function (Excise $record, array $data): void {
                        $record->increment('portions', $data['portions']);
                    })
                    ->visible(fn (Excise $record) => $record->volume * 1000 / 40 - $record->portions != 0),

                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {

        //   dd($this->record);

        return $schema
            ->components([
                TextEntry::make('added_at')->translateLabel(),
                TextEntry::make('title')->translateLabel(),
                TextEntry::make('excise')->translateLabel(),
                TextEntry::make('volume')->translateLabel(),
                TextEntry::make('portions')->translateLabel(),
                TextEntry::make('left')->translateLabel()
                    ->getStateUsing(function (Excise $record) {
                        return $record->volume * 1000 / 40 - $record->portions;
                    })
                    ->html(),

                SpatieMediaLibraryImageEntry::make('excises')
                    ->collection('excises')
                    ->lightbox(),
                ImageEntry::make('excises'),
                //  ->simpleLightbox(fn ($record) => $record->getMedia()->first()->getUrl() ?? $record->getMedia('excises')->first()->getUrl()),

                // LightboxSpatieMediaLibraryImageEntry::make('excise')->translateLabel()
                //     ->collection('excise')
                //     ->conversion('thumb'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExcises::route('/'),
        ];
    }
}
