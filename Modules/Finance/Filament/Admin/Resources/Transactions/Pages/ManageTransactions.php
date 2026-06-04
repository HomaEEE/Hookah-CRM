<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Http;
use Modules\Finance\Filament\Admin\Resources\Transactions\TransactionResource;

class ManageTransactions extends ManageRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Кнопка для установки вебхука
            Action::make('setup_webhook')
                ->label('Подключить Monobank')
                ->icon('heroicon-m-signal')
                ->color('info')
                ->form([
                    TextInput::make('webhook_url')
                        ->label('URL Вебхука')
                        // Если роут называется 'monobank.webhook', то: route('monobank.webhook')
                        ->default(url('/webhook/monobank'))
                        ->required()
                        ->url()
                        ->helperText('Этот адрес должен быть доступен из интернета (https).'),
                ])
                ->action(function (array $data) {
                    // Получаем токен из .env (или конфига)
                    $token = env('MONOBANK_TOKEN');

                    if (empty($token)) {
                        Notification::make()
                            ->title('Ошибка')
                            ->body('Токен Monobank не найден в файле .env')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        // Отправляем прямой запрос в Монобанк
                        $response = Http::withHeaders([
                            'X-Token' => $token,
                        ])
                            ->timeout(10)
                            ->post('https://api.monobank.ua/personal/webhook', [
                                'webHookUrl' => $data['webhook_url'],
                            ]);

                        if ($response->successful()) {
                            Notification::make()
                                ->title('Вебхук успешно установлен!')
                                ->body('Теперь новые транзакции будут автоматически падать в базу.')
                                ->success()
                                ->send();
                        } else {
                            // Если банк вернул ошибку, выводим её текст (например, неверный URL)
                            Notification::make()
                                ->title('Ошибка банка')
                                ->body($response->json('errorDescription') ?? 'Неизвестная ошибка')
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Сбой сети')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->modalWidth('sm'),

            // Твоя стандартная кнопка создания
            CreateAction::make()
                ->modalWidth('xl'),
        ];
    }
}
