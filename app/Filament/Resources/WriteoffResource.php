<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WriteoffResource\Pages;
use App\Models\Ingredient;
use App\Models\Workshop;
use App\Models\Writeoff;
use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WriteoffResource extends Resource
{
    protected static ?string $model = Writeoff::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 6;

    public static function getPluralModelLabel(): string
    {
        return __('Writeoffs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Manage');
    }

    public static function getNavigationLabel(): string
    {
        return __('Writeoffs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),

                Select::make('workshop_id')->label(__('Workshop'))
                    ->reactive()
                    ->native(false)
                    ->options(function (callable $get) {
                        if (null == ($get('place_id'))) {
                            return null;
                        } else {
                            return Workshop::where('place_id', $get('place_id'))->pluck('name', 'id');
                        }
                    }),
                Repeater::make('whiteoff')->label(__('White_off'))
                    ->schema([
                        Select::make('ingredient_id')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->reactive()
                            ->options(
                                Ingredient::where('place_id', Filament::getTenant()->id)->pluck('ingredient', 'id')
                            ),
                        TextInput::make('quantity')->label(__('Quantity'))
                            ->reactive()
                            ->integer()
                            ->suffix(static function (Get $get) {
                                if ($get('ingredient_id') != null) {
                                    return Ingredient::where('id', $get('ingredient_id'))['unit'] ?? '';
                                }
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
                TextColumn::make('workshop.name')->translateLabel()
                    ->description(fn (Writeoff $record): string => $record->whiteoff ?? '', position: 'before'),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
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

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWriteoffs::route('/'),
        ];
    }
}
