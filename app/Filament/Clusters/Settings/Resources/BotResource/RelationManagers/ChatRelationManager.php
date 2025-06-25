<?php

namespace App\Filament\Clusters\Settings\Resources\BotResource\RelationManagers;

use App\Http\Controllers\TelegramBotController;
use App\Models\BotChat;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChatRelationManager extends RelationManager
{
    protected static string $relationship = 'chat';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                Hidden::make('telegraph_bot_id')->default($this->ownerRecord->id),
                TextInput::make('chat_id')
                    ->numeric()
                    ->required()
                    ->maxLength(30),
                TextInput::make('name')->translateLabel()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Chat')
            ->columns([
                TextColumn::make('chat_id')->translateLabel(),
                TextColumn::make('name')->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('Sendtest')->label('Send Message')
                    ->form([
                        Textarea::make('message')->translateLabel(),
                    ])
                    ->action(function (BotChat $record, array $data): void {
                        // (new TelegramBotController)->sendMessage($record->chat_id, $data);
                        (new TelegramBotController)->sendTestMessage($record->chat_id, $data);
                    }),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
