<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MistralOCRService
{
    public static function parseInvoice(string $filePath): array
    {
        $fileContent = file_get_contents($filePath);
        $base64File = base64_encode($fileContent);

        $response = Http::withToken(config('services.mistral.api_key'))
            ->timeout(120) // увеличиваем до 2 минут

            ->post('https://api.mistral.ai/v1/chat/completions', [
                'model' => 'mistral-large-latest',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты OCR помощник. Распознай накладную и верни JSON вида: [{"ingredient":"Название","qty":"число","price":"число"}]',
                    ],
                    [
                        'role' => 'user',
                        'content' => $base64File,
                    ],
                ],
                'temperature' => 0.0,
            ]);

        return json_decode($response->json('choices.0.message.content'), true) ?? [];
    }
}
