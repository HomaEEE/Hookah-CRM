<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';

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

    public static function getNavigationBadge(): ?string
    {
        return Feedback::approve(0)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('contact'),
                TextInput::make('text'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel(),
                TextColumn::make('contact')->label(__('Phone')),
                TextColumn::make('text')->wrap(),

                IconColumn::make('approve')
                ->translateLabel()
                ->size(IconColumn\IconColumnSize::Large)
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFeedback::route('/'),
        ];
    }
}
