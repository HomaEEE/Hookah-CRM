<?php

namespace App\Filament\Resources\Feedback;

use App\Filament\Resources\Feedback\Pages\ManageFeedback;
use App\Models\Feedback;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?int $navigationSort = 4;

    public static function getPluralModelLabel(): string
    {
        return __('Feedbacks');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Place');
    }

    public static function getNavigationLabel(): string
    {
        return __('Feedbacks');
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return Feedback::count();
    // }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('contact'),
                TextInput::make('text'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel()
                    ->description(fn (Feedback $record): string => $record->contact ?: ''),
                TextColumn::make('created_at')->translateLabel()->dateTime('d-m-Y')
                    ->size('md')
                    ->badge(),
                TextColumn::make('text')->wrap(),

                IconColumn::make('approve')
                    ->translateLabel()
                    ->size(IconSize::Large)
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
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
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFeedback::route('/'),
        ];
    }
}
