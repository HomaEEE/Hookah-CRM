<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExciseResource\Pages;
use App\Models\Excise;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class ExciseResource extends Resource
{
    protected static ?string $model = Excise::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    public static function getNavigationGroup(): ?string
    {
        return __('Menu');
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
                                DateTimePicker::make('added_at')->label(__('Date Added'))->maxDate(now())->default(now()),
                                TextInput::make('title')->translateLabel()->required(),
                                Select::make('volume')->translateLabel()->required()
                                    ->options(
                                        [
                                            '0.5' => '0.5',
                                            '0.7' => '0.7',
                                            '0.75' => '0.75',
                                            '1' => '1',
                                        ]
                                    ),
                            ]),
                    ]),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('excise')
                                    ->collection('excise')
                                    ->image()
                                    ->imageResizeMode('contain')
                                    ->openable()
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
                TextColumn::make('title')->translateLabel(),
                TextColumn::make('volume')->translateLabel(),
                TextColumn::make('portions')->translateLabel(),
                TextColumn::make('left')->translateLabel()
                    ->getStateUsing(function (Excise $record) {
                        // return whatever you need to show
                        return $record->volume * 1000 / 40 - $record->portions;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('add_portion')->label(__('Add portions'))
                    ->icon('heroicon-o-shield-check')
                    ->button()
                    ->modalHeading(__('Add portions'))
                    ->color('success')
                    ->modalWidth('sm')
                    ->form([
                        Forms\Components\TextInput::make('portion')
                            ->label(__('Add portions'))->numeric()
                            ->required(),
                    ])
                    ->action(function (Excise $record, array $data): void {
                        $record->update(
                            [$record->portions += $data['portion']]
                        );
                    }),
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
        return $infolist
        ->schema([
            TextEntry::make('added_at')->translateLabel(),
            TextEntry::make('title')->translateLabel(),
            TextEntry::make('volume')->translateLabel(),
            TextEntry::make('portions')->translateLabel(),
            TextEntry::make('left')->translateLabel()
            ->getStateUsing(function (Excise $record) {
                // return whatever you need to show
                return $record->volume * 1000 / 40 - $record->portions;
            })
            ->html(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageExcises::route('/'),
        ];
    }
}
