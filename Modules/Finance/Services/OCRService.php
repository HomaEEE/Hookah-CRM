<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OCRService
{
    public function recognizeInvoice(string $imageUrl): ?array
    {
        $apiKey = config('services.mistral.api_key');

        if (! $apiKey) {
            throw new \Exception('В конфигурации отсутствует MISTRAL_API_KEY.');
        }

        $prompt = 'Extract data from invoice image.

                Text language: Ukrainian or Russian.
                Do NOT translate text. Keep original spelling.

                Return JSON only:

                {
                    "supplier_iban":string|null,
                    "invoice_number":string|null,
                    "total_sum":number|null,
                    "items":[
                      {"name":string,"qty":number,"price":number,"sum":number}
                    ]
                }

                Rules:
                missing→null
                decimal="."
                items = table rows
                qty/price/sum → numbers';

        $response = Http::withToken($apiKey)
            ->timeout(1500)
            ->post('https://api.mistral.ai/v1/chat/completions', [
                'model' => 'pixtral-large-latest',
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
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
