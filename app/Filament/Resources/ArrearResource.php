<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArrearResource\Pages;
use App\Models\{Arrear, Ingredient, User};

use Auth;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;

use function Laravel\Prompts\select;

class ArrearResource extends Resource
{
    protected static ?string $model = Arrear::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Arrears');
    }

    public static function getNavigationLabel(): string
    {
        return __('Arrears');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::user()->id),
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Select::make('from_user_id')->label(__('From User'))
                    ->hidden(fn (Get $get) => $get('place_id') == null)
                    ->options(function (callable $get) {
                        return User::whereHas('place', function ($query) use ($get) {
                            $query->where('place_id', $get('place_id'));
                        })->get()
                            ->pluck('name', 'id');
                    })
                    ->searchable(),
                TextInput::make('arrear')->label(__('Arrear')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label(__('Date Added'))->dateTime('d/m/Y')
                    ->size('sm')
                    ->description(fn (Arrear $record): string => $record->user->name),
                TextColumn::make('fromUser.name')->label(__('User')),
                TextColumn::make('arrear')->translateLabel(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')->button()->label(__('Refund'))
                ->icon('heroicon-s-check')
                ->color('success')
                ->action(function (Arrear $record): void {
                    $record->update(
                        [
                            'refund' => 1,
                        ]
                    );
                    $record->save();
                })->visible(fn (Arrear $record) => $record->refund == '0'),

                Tables\Actions\ActionGroup::make([
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
            'index' => Pages\ManageArrears::route('/'),
        ];
    }
}
