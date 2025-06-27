<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\BotResource\Pages;
use App\Filament\Clusters\Settings\Resources\BotResource\RelationManagers\ChatRelationManager;
use App\Http\Controllers\TelegramBotController;
use App\Models\Bot;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Illuminate\Support\Facades\Log;

class BotResource extends Resource
{
    protected static ?string $model = Bot::class;

    protected static ?string $navigationIcon = 'phosphor-robot';

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([

                RelationManagerAction::make('lesson-relation-manager')

                    ->label('Chat list')
                    ->relationManager(ChatRelationManager::make()),
                Action::make('Webhook')->button()
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
                // Action::make('Webhook')->translateLabel()->button()
                //     ->action(function (Bot $record): void {
                //         (new TelegramBotController)->setWebhook($record->id);
                //     })->visible(fn (Bot $bot) => $bot->webhook == 0),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBots::route('/'),
        ];
    }
}
