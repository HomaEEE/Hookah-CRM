<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MistralOCRService
{
    public static function parseInvoice($state): array
    {
        $file = $state[0] ?? null;

        // dd($file);
        if ($file instanceof TemporaryUploadedFile) {
            $filePath = $file->getRealPath();
        } elseif (is_string($file)) {
            // Иногда $state приходит как строка (путь к файлу в медиатеке)
            $filePath = storage_path('app/'.$file);
        } elseif (is_array($file) && isset($file['path'])) {
            $filePath = storage_path('app/'.$file['path']);
        } else {
            throw new \RuntimeException('Не удалось определить путь к файлу');
        }

        if (! file_exists($filePath)) {
            throw new \RuntimeException("Файл не найден: {$filePath}");
        }

        $fileContent = base64_encode(file_get_contents($filePath));

        $prompt = <<<'PROMPT'
Распознай текст накладной и верни JSON в формате:
[
  {"ingredient": "название", "quantity": число, "unit": "единица", "price": число, "total": число}
]
PROMPT;

        $response = Http::withToken(config('services.mistral.api_key'))
            ->acceptJson()
            ->timeout(90)
            ->post('https://api.mistral.ai/v1/ocr', [
                'model' => 'mistral-ocr-latest',
                'document' => [
                    'type' => 'image_url',
                    'message' => $prompt,
                    'image_url' => 'data:image/jpeg;base64,'.$fileContent,
                ],
                'include_image_base64' => false,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Ошибка Mistral OCR API: '.$response->body());
        }

        $data = $response->json();
        dd($data);
        $content = $json['choices'][0]['message']['content'] ?? null;
    }
}
