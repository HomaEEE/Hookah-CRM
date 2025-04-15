<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExciseResource\Pages;
use App\Models\Excise;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Njxqlus\Filament\Components\Infolists\LightboxSpatieMediaLibraryImageEntry;

class ExciseResource extends Resource
{
    protected static ?string $model = Excise::class;

    protected static ?string $recordTitleAttribute = 'excise';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Excises');
    }

    public static function getNavigationLabel(): string
    {
        return __('Excises');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                                    ->imageResizeMode('cover')
                                    ->imageEditor()
                                    ->optimize('webp')
                                    ->resize(70)
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
                //
            ])
            ->actions([
                Action::make('approve')->button()->label('')
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
                    ->form([
                        TextInput::make('portions')
                            ->hiddenLabel()->numeric()
                            ->default(1)
                            ->required()
                            ->suffixAction(
                                FormAction::make('plus')
                                    ->size(ActionSize::Large)
                                    ->color('success')
                                    ->icon('heroicon-o-plus')
                                    ->iconButton()
                                    ->disabled(fn (Get $get): bool => empty($get('portions')) || $get('portions') >= 25)
                                    ->action(function (Get $get, Set $set, $state): void {
                                        $set('portions', $get('portions') + 1);
                                    })
                            )
                            ->prefixAction(
                                FormAction::make('minus')
                                    ->size(ActionSize::Large)
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {

        //   dd($this->record);

        return $infolist
            ->schema([
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
                    ->simpleLightbox(),
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
            'index' => Pages\ManageExcises::route('/'),
        ];
    }
}
