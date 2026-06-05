<?php

namespace Modules\Manage\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OCRService
{
    public function recognizeInvoice(string $imageDataUrl): ?array
    {
        $apiKey = config('services.mistral.api_key');

        if (! $apiKey) {
            throw new \Exception('В конфигурации отсутствует MISTRAL_API_KEY.');
        }

        $prompt = 'Extract data from invoice/receipt image.
Text language: Ukrainian, English, Russian.
Do NOT translate text. Keep original spelling.
Return JSON only:
{
    "supplier_name": string|null,
    "supplier_iban": string|null,
    "invoice_number": string|null,
    "total_sum": number|null,
    "items": [
        {"name": string, "qty": number, "unit": string|null, "price": number, "sum": number}
    ]
}
Rules:
- missing → null
- decimal = "."
- items = table rows
- qty/price/sum → numbers
- VAT included in total_sum and sum.';

        // Отправляем запрос к Mistral (URL строго прописан)
        $response = Http::withToken($apiKey)
            ->timeout(60)
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
                                // ВАЖНО: Для Mistral это должен быть объект с ключом 'url'
                                'image_url' => [
                                    'url' => $imageDataUrl,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');

            if ($content) {
                // Очищаем от случайных markdown-тегов
                $content = trim(str_replace(['```json', '```'], '', $content));

                $parsedData = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $parsedData;
                }

                throw new \Exception('Не удалось разобрать JSON от ИИ: '.json_last_error_msg());
            }

            throw new \Exception('Ответ от Mistral пуст.');
        }

        throw new \Exception('Ошибка API Mistral: '.$response->status().' - '.$response->body());
    }

    /**
     * Безопасное получение Base64 без обращения к HTTP-клиентам.
     * Работает одинаково надежно и с локальным диском, и с S3.
     */
    public function getBase64ImageFromState($state): ?string
    {
        $firstFileState = is_array($state) ? reset($state) : $state;

        $content = null;
        $mimeType = 'image/jpeg';

        if ($firstFileState instanceof TemporaryUploadedFile) {
            // Livewire: безопасно читаем сырые данные файла (работает даже если диск S3)
            $content = $firstFileState->get();
            $mimeType = $firstFileState->getMimeType();
        } else {
            // Spatie Media Library
            $media = null;
            if (is_string($firstFileState) && Str::isUuid($firstFileState)) {
                $media = Media::findByUuid($firstFileState);
            } else {
                $media = Media::find($firstFileState);
            }

            if ($media) {
                // Spatie: безопасно читаем через потоки (абсолютно надежно для S3/FTP/Local)
                $content = stream_get_contents($media->stream());
                $mimeType = $media->mime_type;
            }
        }

        if (! $content) {
            return null;
        }

        $base64Data = base64_encode($content);

        return "data:{$mimeType};base64,{$base64Data}";
    }
}
