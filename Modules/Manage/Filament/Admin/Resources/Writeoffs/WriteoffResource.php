<?php

namespace Modules\Manage\Filament\Admin\Resources\Writeoffs;

use App\Enums\IngredientUnit;
use App\Enums\NavigationGroup;
use App\Models\Workshop;
use Auth;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Manage\Filament\Admin\Resources\Writeoffs\Pages\ManageWriteoffs;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\Writeoff;
use UnitEnum;

class WriteoffResource extends Resource
{
    protected static ?string $model = Writeoff::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

    public static function getPluralModelLabel(): string
    {
        return __('Writeoffs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Writeoffs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),

                Select::make('workshop_id')->label(__('Workshop'))
                    ->live()
                    ->required()
                    ->native(false)
                    ->options(function (callable $get) {
                        if (null == ($get('place_id'))) {
                            return null;
                        } else {
                            return Workshop::where('place_id', $get('place_id'))->pluck('name', 'id');
                        }
                    }),
                Repeater::make('whiteoff')->label(__('White_off'))
                    ->columns(12)
                    ->addActionLabel(__('Add'))
                    ->schema([
                        Select::make('ingredient_id')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->columnSpan(6)
                            ->live()
                            ->options(
                                Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                            ),
                        TextInput::make('quantity')->label(__('Quantity'))
                            ->live()
                            ->columnSpan(6)
                            ->suffix(static function (Get $get) {

                                if ($get('ingredient_id') != null) {
                                    $ingredient = Ingredient::find($get('ingredient_id'));
                                    if ($ingredient && $ingredient->unit instanceof IngredientUnit) {
                                        return $ingredient->unit->getLabel();
                                    }
                                }

                                return null;

                            }),
                    ]),
                Hidden::make('approve')->default(0),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('d/m/Y')->translateLabel()->badge(),
                TextColumn::make('user.name')->translateLabel(),
                TextColumn::make('workshop.name')->translateLabel(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('approve')->button()->label(__('Approve'))
                    ->icon('heroicon-s-check')
                    ->color('success')
                    ->action(function (Writeoff $record): void {
                        $record->update(
                            [
                                'approve' => 1,
                            ]
                        );
                        $record->save();
                    })->visible(fn (Writeoff $record) => $record->approve == 0),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWriteoffs::route('/'),
        ];
    }
}
