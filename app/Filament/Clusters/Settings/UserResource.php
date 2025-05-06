<?php

namespace App\Filament\Clusters\Settings;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\UserResource\Pages;
use App\Models\User;
use App\Models\Wage;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Settings::class;

    use Translatable;

    public static function getPluralModelLabel(): string
    {
        return __('Personal');
    }

    public static function getNavigationLabel(): string
    {
        return __('Personal');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->compact()
                    ->schema([
                        TextInput::make('name')->translateLabel(),
                        TextInput::make('email')->email(),
                        TextInput::make('password')
                            ->translateLabel()
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
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
                        DatePicker::make('birth')->translateLabel()
                            ->native(false),

                        Select::make('wage_id')->label('Post')
                            ->options(
                                Wage::where('place_id', Filament::getTenant()->id)
                                    ->get()->pluck('name', 'id')
                            )
                            ->native(false),

                    ])->columns(2),

                Select::make('place_id')
                    ->relationship('place', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', App::getLocale()))
                    ->label(__('Places'))
                    ->native(false)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->hintIcon('heroicon-o-question-mark-circle')
                    ->hintColor('primary')
                    ->hintIconTooltip(__('')),

                // Select::make('roles')->translateLabel()
                //     ->preload()
                //     ->relationship('roles', 'name'),

                Select::make('roles')->translateLabel()
                    ->relationship('roles', 'name')
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
                    })
                    ->multiple()
                    ->preload()
                    ->searchable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel()
                    ->description(fn (User $record): string => $record->wage->name ?? '', position: 'below'),
                TextColumn::make('phone')->translateLabel()->copyable(),
                //   TextColumn::make('places.name')->badge()->translateLabel(),
                TextColumn::make('birth')->label(__('Birth'))->date('d M Y')->badge(),
                TextColumn::make('shift_day')->label(__('Shift')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Impersonate::make(),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
