<?php
/**
     * Устанавливает URL для получения вебхуков от Monobank
     */
    public function setWebhook(string $url): bool
    {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-Token' => $this->token,
        ])
        ->timeout(10)
        ->post('https://api.monobank.ua/personal/webhook', [
            'webHookUrl' => $url,
        ]);

        // Возвращает true, если ответ 200 OK
        return $response->successful();
    }