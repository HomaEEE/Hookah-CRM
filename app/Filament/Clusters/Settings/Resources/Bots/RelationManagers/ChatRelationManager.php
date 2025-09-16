<?php

namespace App\Filament\Clusters\Settings\Resources\Bots\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Http\Controllers\TelegramBotController;
use App\Models\BotChat;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChatRelationManager extends RelationManager
{
    protected static string $relationship = 'chats';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('Sendtest')->label(__('Send Message'))
                    ->schema([
                        Textarea::make('message')->translateLabel(),
                    ])
                    ->action(function (BotChat $record, array $data): void {
                        (new TelegramBotController)->sendTestMessage($record->chat_id, $data);
                    }),
                ActionGroup::make([
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
}
