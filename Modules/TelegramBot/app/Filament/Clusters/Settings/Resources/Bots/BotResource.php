<?php

namespace Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots;

use App\Filament\Clusters\Settings\SettingsCluster;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\Pages\ManageBots;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\RelationManagers\ChannelRelationManager;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\RelationManagers\ChatRelationManager;
use Modules\TelegramBot\Models\Bot;

class BotResource extends Resource
{
    protected static ?string $model = Bot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'phosphor-robot';

    protected static ?string $cluster = SettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('place_id')->default(Filament::getTenant()->id),
                TextInput::make('name')->translateLabel(),
                TextInput::make('token')->translateLabel(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->recordActions([

                RelationManagerAction::make('chats-manager')
                    ->label('Chats')
                    ->relationManager(ChatRelationManager::make()),
                RelationManagerAction::make('channel-manager')
                    ->label('Channels')
                    ->relationManager(ChannelRelationManager::make()),

                Action::make('Webhook')->button()->color('success')
                    ->requiresConfirmation()
                    ->action(function (Bot $record) {
                        $url = route('telegraph.webhook', $record);
                        $response = $record->registerWebhook($url)->send();
                        Log::info('Telegram Webhook', ['response' => $response->json()]);
                        Notification::make()
                            ->title('Webhook установлен')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Bot $bot) => $bot->webhook == 0),
                Action::make('check')
                    ->url(fn (Bot $record): string => 'https://api.telegram.org/bot'.$record->token.'/getWebhookInfo')
                    ->openUrlInNewTab(),
                // Action::make('Webhook')->translateLabel()->button()
                //     ->action(function (Bot $record): void {
                //         (new TelegramBotController)->setWebhook($record->id);
                //     })->visible(fn (Bot $bot) => $bot->webhook == 0),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
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
            'index' => ManageBots::route('/'),
        ];
    }
}
