<?php

namespace App\Filament\Components\Forms;

use App\Models\Bill;
use App\Models\Ingredient;
use App\Models\Reqvizite;
use App\Models\Supplier;
use Auth;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Http;

class SupplyForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('user_id')->default(Auth::user()->id),
            //  Hidden::make('place_id')->default(Filament::getTenant()->id),
            DateTimePicker::make('added_at')
                ->label(__('Date Added'))
                ->native(false)
                ->seconds(false)
                ->maxDate(now())->default(now()),

            ToggleButtons::make('bill_id')->label(__('Type pay'))
                ->options(Bill::get()->pluck('name', 'id'))
                ->default(0)
                ->required()
                ->inline()
                ->grouped()
                ->extraAttributes(['style' => 'width: 100%'])
                ->colors([
                    0 => 'info',
                    1 => 'success',
                ])
                ->live(),

            ToggleButtons::make('pay')->label(__('Pay'))
                ->options(
                    [
                        0 => __('No pay'),
                        1 => __('Paid'),
                    ]
                )
                ->colors([
                    0 => 'danger',
                    1 => 'success',
                ])
                ->default(0)
                ->inline()
                ->live()
                ->grouped()
                ->extraAttributes(['style' => 'width: 100%'])
                ->live(),

            Select::make('supplier_id')->label(__('Supplier'))
                ->relationship('supplier', 'company')
                ->searchable()
                ->searchingMessage('Searching supplier...')
                ->searchDebounce(500)
                ->native(false)
                ->required()
                ->options(Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id'))
                ->createOptionForm([
                    Section::make()
                        ->schema([
                            TextInput::make('name')->label(__('Name')),
                            TextInput::make('phone')->label(__('Phone')),
                            TextInput::make('company')->label(__('Company')),
                            TextInput::make('mail')->label(__('Email'))->email(),
                        ])->columns(2),
                ]),

            Select::make('reqvizite_id')
                ->label('Реквизиты')
                ->preload()
                ->live()
                ->options(function (callable $get) {
                    if (null == ($get('supplier_id'))) {
                        return null;
                    } else {
                        return Reqvizite::where('supplier_id', $get('supplier_id'))->pluck('own', 'id');
                    }
                })

                ->createOptionForm(fn ($get) => [
                    Section::make()
                        ->schema([
                            Hidden::make('supplier_id')
                                ->default(fn () => $get('supplier_id')),
                            TextInput::make('IBAN')->label(__('IBAN'))->prefix('UA')->numeric(),
                            TextInput::make('INN')->label(__('INN'))->numeric(),
                            TextInput::make('own')->label(__('FOP')),
                        ])->columns(1),
                ])
                ->createOptionUsing(function (array $data) {
                    return Reqvizite::create([
                        'supplier_id' => $data['supplier_id'],
                        'IBAN' => $data['IBAN'],
                        'INN' => $data['INN'],
                        'own' => $data['own'],
                    ])->id;
                })
                ->searchable()
                ->live()
                ->disabled(fn (Get $get) => $get('supplier_id') === null)
                ->required(fn (Get $get) => (int) $get('bill_id') === 1 && (int) $get('pay') === 0)
                ->visible(fn (Get $get) => (int) $get('pay') === 0 && (int) $get('bill_id') === 1),

            TextInput::make('invoice_number')->label(__('Invoice'))
                ->visible(fn (Get $get) => (int) $get('bill_id') === 1 && (int) $get('pay') === 0)
                ->required(fn (Get $get) => (int) $get('bill_id') === 1 && (int) $get('pay') === 0),

            TextInput::make('sum')->label(__('Sum'))
                ->numeric()
                ->prefix('₴')
                ->inputMode('decimal')
                ->required(),

            TextInput::make('delay')->label(__('Delay'))
                ->inputMode('none')
                ->suffixAction(
                    Action::make('DelayPlus')
                        ->size(Size::Large)
                        ->color('success')
                        ->icon('heroicon-o-plus')
                        ->iconButton()
                        ->disabled(fn (Get $get): bool => empty($get('delay')) || $get('delay') >= 28)
                        ->action(function (Get $get, Set $set, $state): void {
                            $set('delay', $get('delay') + 1);
                        })
                )
                ->prefixAction(
                    Action::make('DelayMinus')
                        ->size(Size::Large)
                        ->icon('heroicon-o-minus')
                        ->color('danger')
                        ->iconButton()
                        ->disabled(fn (Get $get): bool => empty($get('delay')) || $get('delay') <= 1)
                        ->action(function (Get $get, Set $set, $state): void {
                            $set('delay', $get('delay') - 1);
                        })
                )
                ->default(
                    function (callable $get, $set) {
                        $toggle = $get('pay');

                        return $toggle === 0 ? $set('delay', 7) : $set('delay', 0); // Or '' for an empty string
                    })
                ->live()
                ->numeric() // If you want to enforce numeric input
                ->required(fn (callable $get) => $get('pay') === 1)
                ->visible(fn (Get $get) => $get('pay') == '0'),

            Tabs::make('Tabs')
                ->contained(false)
                ->tabs([

                    Tab::make(__('Photo'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('invoice')
                                ->label(__('Photo'))
                                ->collection('supplies')
                                ->multiple()
                                ->imageResizeMode('cover')
                                ->imagePreviewHeight(350)
                                ->customProperties(['format' => 'webp', 'width' => 1800, 'quality' => 50])
                                ->openable()
                                ->preserveFilenames()
                                ->live()
                                ->hintAction(
                                    Action::make('recognize')
                                        ->label(__('OCR Photo'))
                                        ->icon('heroicon-o-sparkles')
                                        ->color('primary')

                                        ->action(function (Get $get, Set $set, $state) {
                                            if (empty($state)) {
                                                Notification::make()
                                                    ->warning()
                                                    ->title('Файл не найден')
                                                    ->body('Пожалуйста, загрузите файл перед распознаванием.')
                                                    ->send();

                                                return;
                                            }

                                            // The file is stored in Livewire's temporary directory before being saved
                                            $firstFileState = is_array($state) ? reset($state) : $state;

                                            // Handle cases where the image is a newly uploaded TemporaryUploadedFile
                                            // or an already saved Spatie Media ID.
                                            $filePath = null;
                                            $imageUrl = null;
                                            if ($firstFileState instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                $filePath = $firstFileState->getRealPath();
                                                $imageUrl = $firstFileState->temporaryUrl();
                                            } elseif (is_string($firstFileState) && \Str::isUuid($firstFileState)) {
                                                // It's a media UUID from Spatie Media Library
                                                $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findByUuid($firstFileState);
                                                if ($media) {
                                                    $filePath = $media->getPath();
                                                    $imageUrl = $media->getUrl();
                                                }
                                            } else {
                                                // Try to see if $firstFileState is an ID for Spatie
                                                $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($firstFileState);
                                                if ($media) {
                                                    $filePath = $media->getPath();
                                                    $imageUrl = $media->getUrl();
                                                }
                                            }

                                            if ($imageUrl && str_starts_with($imageUrl, '/')) {
                                                // If relative path, try to make it absolute
                                                $imageUrl = url($imageUrl);
                                            }

                                            if (! $filePath || ! file_exists($filePath)) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Ошибка')
                                                    ->body('Не удалось получить доступ к файлу изображения.')
                                                    ->send();

                                                return;
                                            }

                                            // Read and encode the image
                                            // Upload file to Mistral
                                            $apiKey = env('MISTRAL_API_KEY');
                                            if (! $apiKey) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Ошибка API')
                                                    ->body('В конфигурации отсутствует MISTRAL_API_KEY.')
                                                    ->send();

                                                return;
                                            }

                                            try {
                                                // Make API call to Mistral
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
                                                                        'text' => 'Извлеките данные из этой накладной (invoice). Верните валидный JSON объект со следующей структурой: { "supplier_iban": "string, номер IBAN поставщика, если есть, иначе null или пустая строка",
                                                                        "invoice_number": "string, номер накладной, если есть, иначе null или пустая строка",
                                                                        "total_sum": "float, сумма накладной, если есть, иначе null или пустая строка",
                                                                        "items": [ { "name": "string, точное наименование товара", "qty": float, количество, "price": float, цена за единицу, "sum": float, сумма } ] }',
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

                                                            $rawIban = $parsedData['supplier_iban'] ?? '';

                                                            $iban = strtoupper(preg_replace('/[^A-Z0-9]/', '', $rawIban));

                                                            if (! empty($iban)) {
                                                                $ibanWithoutUA = preg_replace('/^UA/', '', $iban);

                                                                $reqvizite = \App\Models\Reqvizite::where('IBAN', $ibanWithoutUA)
                                                                    ->whereNotNull('supplier_id')
                                                                    ->first();
                                                                if ($reqvizite) {
                                                                    $set('supplier_id', $reqvizite->supplier_id);
                                                                    $set('reqvizite_id', $reqvizite->id);
                                                                }

                                                                $invoiceNumber = $parsedData['invoice_number'] ?? '';
                                                                if (! empty($invoiceNumber)) {
                                                                    $set('invoice_number', $invoiceNumber);
                                                                }

                                                                $totalSum = $parsedData['total_sum'] ?? '';
                                                                if (! empty($totalSum)) {
                                                                    $set('sum', $totalSum);
                                                                }
                                                            }
                                                            // 2. Populate Items
                                                            $items = $parsedData['items'] ?? [];
                                                            $repeaterItems = [];

                                                            foreach ($items as $item) {
                                                                $name = $item['name'] ?? '';
                                                                if (empty($name)) {
                                                                    continue;
                                                                }

                                                                $alias = \App\Models\IngredientAlias::where('alias', $name)->first();

                                                                $ingredientId = $alias ? $alias->ingredient_id : null;

                                                                $repeaterItems[(string) \Illuminate\Support\Str::uuid()] = [
                                                                    'name' => $name,
                                                                    'ingredient_id' => $ingredientId,
                                                                    'qty' => $item['qty'] ?? 1,
                                                                    'price' => $item['price'] ?? 0,
                                                                    'sum' => number_format(($item['price'] ?? 0) * ($item['qty'] ?? 0), 2, '.', '')];
                                                            }

                                                            if (! empty($repeaterItems)) {
                                                                $set('items', $repeaterItems);
                                                            }

                                                            Notification::make()
                                                                ->success()
                                                                ->title('Успех')
                                                                ->body('Накладная успешно распознана!')
                                                                ->send();

                                                        } else {
                                                            throw new \Exception('Не удалось разобрать JSON: '.json_last_error_msg());
                                                        }
                                                    } else {
                                                        throw new \Exception('Ответ от Mistral не содержит ожидаемого содержимого.');
                                                    }
                                                } else {
                                                    throw new \Exception('Ошибка API: '.$response->status().' - '.$response->body());
                                                }
                                            } catch (\Exception $e) {
                                                \Illuminate\Support\Facades\Log::error('OCR Error: '.$e->getMessage());
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Ошибка при распознавании')
                                                    ->body($e->getMessage())
                                                    ->send();
                                            }
                                        })
                                ),

                            Textarea::make('comment')->label(__('Comment'))
                                ->rows(3),
                        ]),

                    Tab::make(__('Receipt'))
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('receipt')
                                ->collection('receipts')
                                ->label(__('Receipt'))
                                ->multiple()
                                ->imagePreviewHeight(450)
                                ->imageResizeMode('cover')
                                ->imagePreviewHeight(350)
                                ->imageResizeTargetWidth('1800')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    null,
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->openable(),

                        ])->columns(1),
                    Tabs\Tab::make(__('List'))
                        ->schema([
                            Repeater::make('items')
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('Invoice name'))
                                        ->disabled()
                                        ->dehydrated()
                                        ->visible(fn (Get $get) => empty($get('ingredient_id'))),

                                    Select::make('ingredient_id')
                                        ->label(__('Ingredient'))
                                        ->searchable()
                                        ->options(Ingredient::get()->pluck('ingredient', 'id'))
                                        ->required(fn (Get $get) => empty($get('ingredient_id')))
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $get) {
                                            $name = $get('name');
                                            if (! empty($name) && ! empty($state)) {
                                                // Create alias if it doesn't exist
                                                \App\Models\IngredientAlias::firstOrCreate(
                                                    ['alias' => $name],
                                                    ['ingredient_id' => $state]
                                                );
                                            }
                                        }),
                                    TextInput::make('qty')->translateLabel()
                                        ->reactive(),
                                    TextInput::make('price')->translateLabel()->suffix('')
                                        ->numeric()
                                        ->autocomplete(false)
                                        ->inputMode('decimal')
                                        ->stripCharacters(',')
                                        ->required()
                                        ->prefix('₴'),
                                    TextInput::make('sum')->translateLabel()->suffix('')
                                        ->numeric()
                                        ->autocomplete(false)
                                        ->inputMode('decimal')
                                        ->stripCharacters(',')
                                        ->required()
                                        ->prefix('₴'),
                                ]),
                            TextEntry::make('invoice_sum')->label(__('Invoice sum'))->prefix('₴')->disabled()
                                ->state(function (Set $set, Get $get) {
                                    $totalInvoiceSum = collect($get('items'))
                                        ->pluck('sum')
                                        ->sum();

                                    $set('invoice_sum', $totalInvoiceSum);

                                    return $totalInvoiceSum;
                                })
                                ->live(),
                        ]),

                ]),

        ];
    }
}
