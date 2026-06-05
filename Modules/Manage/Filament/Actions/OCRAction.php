<?php

namespace Modules\Manage\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\IngredientAlias;
use Modules\Manage\Services\OCRService;
use Modules\Place\Models\Reqvizite;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class OCRAction
{
    public static function attach(FileUpload $component): FileUpload
    {
        return $component->hintAction(static::make());
    }

    public static function make(): Action
    {
        return Action::make('recognize')
            ->label(__('OCR Photo'))
            ->icon('heroicon-o-sparkles')
            ->color('primary')
            ->action(function ($set, $get, $state, OCRService $ocrService) {
                if (empty($state)) {
                    Notification::make()
                        ->warning()
                        ->icon(Phosphor::FileX)
                        ->title('Файл не найден')
                        ->body('Пожалуйста, загрузите файл перед распознаванием.')
                        ->send();

                    return;
                }

                try {
                    // ИСПРАВЛЕНО: Заменили ">" на "->"
                    $imageBase64 = $ocrService->getBase64ImageFromState($state);

                    if (! $imageBase64) {
                        Notification::make()
                            ->danger()
                            ->icon(Phosphor::ImageBroken)
                            ->title('Ошибка')
                            ->body('Не удалось получить доступ к файлу изображения.')
                            ->send();

                        return;
                    }

                    $parsedData = $ocrService->recognizeInvoice($imageBase64);

                    if ($parsedData) {
                        static::populateForm($set, $get, $parsedData);

                        Notification::make()
                            ->success()
                            ->icon(Phosphor::ListChecks)
                            ->title(__('Done'))
                            ->body(__('Invoice successfully recognized!'))
                            ->send();
                    }
                } catch (\Exception $e) {
                    Log::error('OCR Error: '.$e->getMessage());
                    Notification::make()
                        ->danger()
                        ->title(__('Error'))
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    protected static function populateForm($set, $get, array $parsedData): void
    {
        // 1. Заполнение IBAN / Поставщика
        $rawIban = $parsedData['supplier_iban'] ?? '';
        $iban = strtoupper(preg_replace('/[^A-Z0-9]/', '', $rawIban));

        if (! empty($iban)) {
            $ibanWithoutUA = preg_replace('/^UA/', '', $iban);

            $reqvizite = Reqvizite::where('IBAN', $ibanWithoutUA)
                ->whereNotNull('supplier_id')
                ->first();

            if ($reqvizite) {
                $set('supplier_id', $reqvizite->supplier_id);
                $set('reqvizite_id', $reqvizite->id);
            }
        }

        // 2. Номер накладной
        $invoiceNumber = $parsedData['invoice_number'] ?? '';
        if (! empty($invoiceNumber)) {
            $set('invoice_number', $invoiceNumber);
        }

        // 3. Общая сумма
        $totalSum = $parsedData['total_sum'] ?? '';
        if (! empty($totalSum)) {
            $set('sum', $totalSum);
        }

        // 4. Товары (Repeater)
        $items = $parsedData['items'] ?? [];
        $repeaterItems = [];

        foreach ($items as $item) {
            $name = $item['name'] ?? '';
            if (empty($name)) {
                continue;
            }

            // Пытаемся найти алиас
            $alias = IngredientAlias::where('alias', $name)->first();
            $ingredientId = $alias ? $alias->ingredient_id : null;

            // ИСПРАВЛЕННАЯ ЛОГИКА АЛИАСОВ
            if (! $ingredientId) {
                // Если алиас не найден, ищем точное совпадение в базовой таблице
                $ingredient = Ingredient::where('ingredient', $name)->first();

                if ($ingredient) {
                    $ingredientId = $ingredient->id;

                    // Автоматически создаем алиас, чтобы в следующий раз было быстрее
                    IngredientAlias::firstOrCreate([
                        'alias' => $name,
                        'ingredient_id' => $ingredientId,
                    ]);
                }
                // Если $ingredient не найден, $ingredientId остается null.
                // В форме это поле будет пустым, и менеджер должен будет выбрать товар вручную.
            }

            $repeaterItems[(string) Str::uuid()] = [
                'name' => $name,
                'ingredient_id' => $ingredientId, // Будет ID если нашли, и null если не нашли
                'qty' => $item['qty'] ?? 1,
                'price' => $item['price'] ?? 0,
            ];
        }

        if (! empty($repeaterItems)) {
            $set('items', $repeaterItems);
        }
    }
}
