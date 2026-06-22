<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Pages;

use App\Models\User;
use Auth;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Http;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Filament\Admin\Resources\Transactions\TransactionResource;
use Modules\Finance\Models\Transaction;

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

            Action::make('Loan_funds')
                ->label(__('Loan'))
                ->icon(Phosphor::ArrowElbowRightDown)
                ->color('warning')
                ->form([
                    ToggleButtons::make('bill_id')->label(__('Bill'))
                        ->options(BillType::class)->default(1)
                        ->required()
                        ->inline()
                        ->grouped()
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->colors([
                            0 => 'info',
                            1 => 'success',
                        ]),
                    TextInput::make('sum')
                        ->label(__('Sum'))
                        ->numeric()
                        ->required(),
                    TextInput::make('comment')
                        ->label(__('Comment'))
                        ->default('Взнос личных средств '.auth()->user()->name),
                ])
                ->action(function (array $data) {
                    Transaction::create([
                        'type' => TransactionType::Loan->value,
                        'sum' => $data['sum'],
                        'user_id' => Auth::id(),
                        'bill_id' => $data['bill_id'],
                        'comment' => $data['comment'],
                        'added_at' => now(),
                    ]);
                }),

            Action::make('repayment_funds')
                ->label(__('Repayment'))
                ->icon(Phosphor::ArrowElbowRightUp)
                ->color('success')
                ->form([
                    // 1. Выбираем, кому именно возвращаем долг
                    Select::make('user_id')
                        ->label('Кому возвращаем')
                        ->options(User::pluck('name', 'id')) // Выбираем из пользователей/соучредителей
                        ->searchable()
                        ->required()
                        ->live() // Чтобы динамически пересчитывать helperText ниже
                        ->helperText(function (Get $get) {
                            $userId = $get('user_id');
                            if (! $userId) {
                                return null;
                            }

                            // Считаем текущий баланс долга перед этим пользователем в копейках
                            $debtKopecks = Transaction::where('user_id', $userId)
                                ->whereIn('type', [TransactionType::Loan->value, TransactionType::Repayment->value])
                                ->selectRaw('SUM(CASE WHEN type = ? THEN sum ELSE -sum END) as balance', [TransactionType::Loan->value])
                                ->value('balance') ?? 0;

                            $debtGrid = number_format($debtKopecks / 100, 2, '.', ' ');

                            return "Текущий долг: {$debtGrid} ₴";
                        }),

                    ToggleButtons::make('bill_id')->label(__('Bill'))
                        ->options(BillType::class)
                        ->default(0)
                        ->required()
                        ->inline()
                        ->grouped()
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->colors([
                            0 => 'info',
                            1 => 'success',
                        ]),
                    // 3. Указываем сумму выплаты
                    TextInput::make('sum')
                        ->label('Сумма выплаты')
                        ->numeric()
                        ->required()
                        ->rules([
                            // Кастомная валидация, чтобы нельзя было выплатить больше, чем существует долг
                            fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                $userId = $get('user_id');
                                if (! $userId) {
                                    return;
                                }

                                $debtKopecks = Transaction::where('user_id', $userId)
                                    ->whereIn('type', [TransactionType::Loan->value, TransactionType::Repayment->value])
                                    ->selectRaw('SUM(CASE WHEN type = ? THEN sum ELSE -sum END) as balance', [TransactionType::Loan->value])
                                    ->value('balance') ?? 0;

                                $enteredKopecks = $value * 100;

                                if ($enteredKopecks > $debtKopecks) {
                                    $maxAllowed = number_format($debtKopecks / 100, 2, '.', ' ');
                                    $fail("Нельзя вернуть больше, чем заведение должно. Максимальная сумма: {$maxAllowed} ₴");
                                }
                            },
                        ]),

                    TextInput::make('comment')
                        ->label('Комментарий')
                        ->default('Частичный/полный возврат личных средств'),
                ])
                ->action(function (array $data) {
                    Transaction::create([
                        'type' => TransactionType::Repayment->value, // Указываем тип возврата долга ('7')
                        'sum' => $data['sum'],
                        'user_id' => $data['user_id'], // Тот, кому вернули долг
                        'bill_id' => $data['bill_id'], // Счет списания
                        'comment' => $data['comment'],
                        'added_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Выплата долга зафиксирована')
                        ->success()
                        ->send();
                })
                ->modalWidth('sm'),

            CreateAction::make()
                ->modalWidth('xl'),
        ];
    }
}
