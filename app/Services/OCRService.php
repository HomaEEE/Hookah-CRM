<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OCRService
{
    public function recognizeInvoice(string $imageUrl): ?array
    {
        $apiKey = config('ai.mistral.key');

        if (! $apiKey) {
            throw new \Exception('В конфигурации отсутствует MISTRAL_API_KEY.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(120) // Increase timeout for image processing
            ->post('https://api.mistral.ai/v1/chat/completions', [
                'model' => 'pixtral-large-latest',
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Извлеките данные из этой накладной (invoice). Верните валидный JSON объект со следующей структурой: { "supplier_iban": "string, номер IBAN поставщика, если есть, иначе null или пустая строка", "invoice_number": "string, номер накладной, если есть, иначе null или пустая строка", "total_sum": "float, сумма накладной, если есть, иначе null или пустая строка", "items": [ { "name": "string, точное наименование товара", "qty": float, количество, также может быть указано в названии количество в упаковке, например "10шт", "price": float, цена за единицу или штуку если в названии указано количество в упаковке, "sum": float, сумма } ] }',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => $imageUrl,
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            $result = $response->json();
            $content = $result['choices'][0]['message']['content'] ?? null;

            if ($content) {
                $parsedData = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $parsedData;
                } else {
                    throw new \Exception('Не удалось разобрать JSON: '.json_last_error_msg());
                }
            } else {
                throw new \Exception('Ответ от Mistral не содержит ожидаемого содержимого.');
            }
        } else {
            throw new \Exception('Ошибка API: '.$response->status().' - '.$response->body());
        }
    }

    public function getImageUrlFromState($state): ?string
    {
        $firstFileState = is_array($state) ? reset($state) : $state;

        $filePath = null;
        $imageUrl = null;

        if ($firstFileState instanceof TemporaryUploadedFile) {
            $filePath = $firstFileState->getRealPath();
            $imageUrl = $firstFileState->temporaryUrl();
        } elseif (is_string($firstFileState) && Str::isUuid($firstFileState)) {
            $media = Media::findByUuid($firstFileState);
            if ($media) {
                $filePath = $media->getPath();
                $imageUrl = $media->getUrl();
            }
        } else {
            $media = Media::find($firstFileState);
            if ($media) {
                $filePath = $media->getPath();
                $imageUrl = $media->getUrl();
            }
        }

        if ($imageUrl && str_starts_with($imageUrl, '/')) {
            $imageUrl = url($imageUrl);
        }

        if (! $filePath || ! file_exists($filePath)) {
            return null;
        }

        return $imageUrl;
    }
}
