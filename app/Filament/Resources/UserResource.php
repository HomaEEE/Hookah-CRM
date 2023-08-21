<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\{User, Place};
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make([
                Grid::make(3)
                    ->schema([
                        Grid::make(2)->schema([
                            Section::make()->schema([
                                TextInput::make('email')->email(),
                                TextInput::make('password')
                                    ->translateLabel()
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create'),
                                TextInput::make('name')->translateLabel(),
                                TextInput::make('phone')
                                    ->tel()
                                    ->translateLabel()
                                    ->mask(RawJs::make(<<<'JS'
                                        $input.length >= 13 ? '(999)999-99-99' : '(999)999-99-99'
                                    JS)),

                                DatePicker::make('birth')->translateLabel(),
                    ]),
            ]),
            Section::make([
                Select::make('place_user')->label(__('Place'))
                    //->options(Place::all()->pluck('city', 'id'))
                    ->relationship('place', 'city')
                    ->native(false)
                    ->preload()
                    ->multiple(),

                CheckboxList::make('roles')->label(__('Access'))
                    ->relationship('roles', 'name')
                    ->columns(2)
                    ->helperText('Только 1 пункт!')
                    ->required(),
            ]),
        ]),
        ])
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->translateLabel(),
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('places')->translateLabel()
                ->badge()
                ->separator(','),
                TextColumn::make('birth')->date('d M Y')
                    ->translateLabel()
                    ->icon('heroicon-s-calendar')
                    ->iconPosition('before'),
                TextColumn::make('phone')
                    ->icon('heroicon-s-phone')
                    ->translateLabel()
                    ->iconPosition('before')
                    ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
