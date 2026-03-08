<?php

namespace App\Filament\Actions;

use App\Enums\IngredientUnit;
use App\Models\Ingredient;
use App\Models\IngredientAlias;
use App\Models\Reqvizite;
use App\Services\OCRService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                        ->title('Файл не найден')
                        ->body('Пожалуйста, загрузите файл перед распознаванием.')
                        ->send();

                    return;
                }

                try {
                    $imageUrl = $ocrService->getImageUrlFromState($state);

                    if (! $imageUrl) {
                        Notification::make()
                            ->danger()
                            ->title('Ошибка')
                            ->body('Не удалось получить доступ к файлу изображения.')
                            ->send();

                        return;
                    }

                    $parsedData = $ocrService->recognizeInvoice($imageUrl);

                    if ($parsedData) {
                        static::populateForm($set, $get, $parsedData);

                        Notification::make()
                            ->success()
                            ->title('Успех')
                            ->body('Накладная успешно распознана!')
                            ->send();
                    }
                } catch (\Exception $e) {
                    Log::error('OCR Error: '.$e->getMessage());
                    Notification::make()
                        ->danger()
                        ->title('Ошибка при распознавании')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    protected static function populateForm($set, $get, array $parsedData): void
    {
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

        $invoiceNumber = $parsedData['invoice_number'] ?? '';
        if (! empty($invoiceNumber)) {
            $set('invoice_number', $invoiceNumber);
        }

        $totalSum = $parsedData['total_sum'] ?? '';
        if (! empty($totalSum)) {
            $set('sum', $totalSum);
        }

        // Populate Items
        $items = $parsedData['items'] ?? [];
        $repeaterItems = [];

        foreach ($items as $item) {
            $name = $item['name'] ?? '';
            if (empty($name)) {
                continue;
            }

            $alias = IngredientAlias::where('alias', $name)->first();
            $ingredientId = $alias ? $alias->ingredient_id : null;

            if (! $ingredientId) {
                $ingredient = Ingredient::where('place_id', Filament::getTenant()?->id)
                    ->where('ingredient', $name)
                    ->first();

                if (! $ingredient) {
                    $ingredient = Ingredient::create([
                        'ingredient' => $name,
                        'unit' => IngredientUnit::kg,
                        'place_id' => Filament::getTenant()?->id,
                        'price' => $item['price'] ?? 0,
                    ]);
                }

                $ingredientId = $ingredient->id;

                IngredientAlias::firstOrCreate([
                    'alias' => $name,
                    'ingredient_id' => $ingredientId,
                ]);
            }

            $repeaterItems[(string) Str::uuid()] = [
                'name' => $name,
                'ingredient_id' => $ingredientId,
                'qty' => $item['qty'] ?? 1,
                'price' => $item['price'] ?? 0,
                'sum' => number_format(($item['price'] ?? 0) * ($item['qty'] ?? 0), 2, '.', ''),
            ];
        }

        if (! empty($repeaterItems)) {
            $set('items', $repeaterItems);
        }
    }
}
