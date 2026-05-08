<?php

namespace App\Filament\Clusters\Settings\Resources\Users;

use App\Filament\Clusters\Settings\Resources\Users\Pages\ManageUsers;
use App\Filament\Clusters\Settings\SettingsCluster;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Modules\Finance\Models\Wage;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = SettingsCluster::class;

    use Translatable;

    public static function getPluralModelLabel(): string
    {
        return __('Personal');
    }

    public static function getNavigationLabel(): string
    {
        return __('Personal');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

                    ])->columns(3),

                Section::make()
                    ->compact()
                    ->schema([
                        Select::make('place_id')
                            ->relationship('place', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', App::getLocale()))
                            ->label(__('Places'))
                            ->native(false)
                            ->multiple()
                            ->preload(),

                        Select::make('wage_id')->label(__('Post'))
                            ->options(
                                Wage::where('place_id', Filament::getTenant()->id)
                                    ->get()->pluck('name', 'id')
                            )
                            ->native(false),

                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->saveRelationshipsUsing(function (Model $record, $state) {
                                $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
                            })
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])->columns(3),
            ])->columns(1);
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
                IconColumn::make('enabled')->label(__('Visible'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
