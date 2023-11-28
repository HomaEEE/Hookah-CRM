<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\{User, Place, Wage};
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';


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
                    TextInput::make('creditcard')
                        ->translateLabel()
                        ->mask(RawJs::make(<<<'JS'
                        $input.length >= 16 ? '9999-9999-9999-9999' : '9999-9999-9999-9999'
                     JS)),
                    DatePicker::make('birth')->translateLabel(),
                    Select::make('wage_id')->translateLabel()->options(
                        Wage::where('place_id', Filament::getTenant()->id)->pluck('name', 'id')
                    )->columns(2),
                    Section::make([
                        CheckboxList::make('place_user')->label(__('Places'))
                            ->relationship('place', 'name')
                            ->columns(2),

                        Select::make('role_id')
                            ->relationship('roles', 'name')
                            ->preload()
                             ->searchable()
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
                Tables\Actions\Action::make('Unbind')
                ->label(__('Unbind user'))
                ->color('danger')
                ->modalHeading(__('Unbind user'))
                ->requiresConfirmation()
                ->action( fn (User $user) => $user->place()->detach(Filament::getTenant()->id))
                ->after(function ($record) {
                    Notification::make()
                    ->title($record->name . ' '. __('deteled from Place'))
                    ->icon('heroicon-o-user')
                    ->iconColor('danger')
                    ->send();
                })
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
           // 'create' => Pages\CreateUser::route('/create'),
           // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
