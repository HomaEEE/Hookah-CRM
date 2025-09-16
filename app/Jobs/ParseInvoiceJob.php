<?php

namespace App\Jobs;

use App\Models\Ingredient;
use App\Models\Supply;
use App\Services\MistralOCRService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $supplyId;       // nullable для create

    public string $tmpFilePath;  // livewire tmp file path

    public function __construct(?int $supplyId, string $tmpFilePath)
    {
        $this->supplyId = $supplyId;
        $this->tmpFilePath = $tmpFilePath;
    }

    public function handle()
    {
        $items = MistralOCRService::parseInvoice($this->tmpFilePath);

        $repeaterData = collect($items)->map(fn ($i) => [
            'ingredient_id' => Ingredient::where('ingredient', 'like', "%{$i['ingredient']}%")->value('id'),
            'qty' => $i['qty'],
            'price' => $i['price'],
        ])->toArray();

        // Если запись уже есть
        if ($this->supplyId) {
            $supply = Supply::find($this->supplyId);
            if ($supply) {
                $supply->update(['items' => $repeaterData]);
            }
        } else {
            // Иначе можно временно сохранить результат в файл/кэш, пока запись создаётся
            cache()->put('ocr_parsed_items_'.session()->getId(), $repeaterData, 3600);
        }

        Notification::make()
            ->title('OCR завершён')
            ->body('Позиции накладной успешно распознаны.')
            ->success()
            ->send();
    }
}
