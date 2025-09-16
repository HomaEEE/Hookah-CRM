<?php

namespace App\Filament\Resources\Arrears;

use App\Filament\Resources\Arrears\Pages\ManageArrears;
use App\Models\Arrear;
use App\Models\User;
use Auth;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ->recordActions([
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

                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),

            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageArrears::route('/'),
        ];
    }
}
