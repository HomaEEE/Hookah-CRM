<?php

namespace Modules\Finance\app\Services;

use Illuminate\Support\Facades\Http;

class MonobankWebhook
{
    public function __construct(protected ?string $token = null)
    {
    }

    /**
     * Устанавливает URL для получения вебхуков от Monobank
     */
    public function setWebhook(string $url): bool
    {
        $response = Http::withHeaders([
            'X-Token' => $this->token,
        ])
        ->timeout(10)
        ->post('https://api.monobank.ua/personal/webhook', [
            'webHookUrl' => $url,
        ]);

        return $response->successful();
    }
}