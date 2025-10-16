<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\Ingredient;
use App\Models\Place;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Workshop;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class PosterApiController extends Controller
{
    private $url = 'https://joinposter.com';

    private $urlApi = 'https://joinposter.com/api/';

    // Получение категорий из Постера
    public function getCategories($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseCategories = Http::get($this->urlApi.'menu.getCategories?token='.$place_api['api'])
            ->throw()->json();

        $categories = $responseCategories['response'];

        foreach ($categories as $c) {
            Category::updateOrCreate(
                [
                    'place_id' => $place_id,
                    'category_id' => $c['category_id'],
                ],
                [
                    'category_name' => $c['category_name'],
                    'parent_category' => $c['parent_category'],
                    'visible' => 1,
                ]
            );
        }

        // $categories = collect($categories)->map(function ($c) use ($place_id) {
        //     return [
        //         'place_id' => $place_id,
        //         'category_id' => $c['category_id'],
        //         'category_name' => $c['category_name'],
        //         'parent_category' => $c['parent_category'],
        //         'visible' => 1, // добавим только при вставке
        //     ];
        // })->toArray();

        // Category::upsert(
        //     $categories,
        //     uniqueBy: ['place_id', 'category_id'],
        //     update: ['category_name', 'parent_category'] // ← visible НЕ обновляем
        // );
    }
    // Получение товаров и техкарт из Постера

    public function getProducts($place_id)
    {

        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseMenu = Http::get($this->urlApi.'menu.getProducts?token='.$place_api['api'])
            ->throw()->json();
        $products = $responseMenu['response'];

        foreach ($products as $product) {

            $ingredient_array = [];

            if (isset($product['ingredients'])) {
                foreach ($product['ingredients'] as $ingredient) {
                    array_push($ingredient_array, $ingredient['ingredient_name']);
                }
            }

            Product::updateOrCreate(
                [
                    'product_id' => $product['product_id'],
                    'place_id' => $place_id,
                ],
                [
                    'product_name' => $product['product_name'],
                    'menu_category_id' => $product['menu_category_id'],
                    'cost' => $product['cost'] / 100,
                    'price' => $product['spots'][0]['price'] / 100,
                    'out' => $product['out'],
                    'ingredients' => empty($ingredient_array) ? '' : implode(', ', $ingredient_array),
                ]
            );
        }
    }
    // Получение ингридиентов из Постера

    public function getIngredients($place_id)
    {

        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseMenu = Http::get($this->urlApi.'menu.getIngredients?token='.$place_api['api'])
            ->throw()->json();
        $ingredients = $responseMenu['response'];

        foreach ($ingredients as $ingredient) {

            Ingredient::updateOrCreate(
                [
                    'ingredient' => $ingredient['ingredient_name'],
                    'place_id' => $place_id,
                ],
                [
                    'unit' => $ingredient['ingredient_unit'],
                    'barcode' => $ingredient['ingredient_barcode'],

                ]);

        }
        $responseMenu = Http::get($this->urlApi.'menu.getProducts?type=products&token='.$place_api['api'])
            ->throw()->json();
        $products = $responseMenu['response'];

        foreach ($products as $product) {

            Ingredient::updateOrCreate(
                [
                    'ingredient' => $product['product_id'],
                    'place_id' => $place_id,
                ],
                [
                    'ingredient' => $product['product_name'],
                    'unit' => 'p',
                    'barcode' => $ingredient['ingredient_barcode'],
                    'price' => $product['cost'],
                ]
            );
        }
    }

    // Изменение товара в Poster'e

    public function updateProduct($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();

    }

    // Получение техкарт из Постера

    public function getTechcards($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $techcards = Http::get($this->urlApi.'menu.getProducts?token='.$place_api['api'].'&type=batchtickets')
            ->throw()->json();

        dd($techcards);
    }
    // Получение поставщиков из Постера

    public function getSuppliers($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $suppliers = Http::get($this->urlApi.'storage.getSuppliers?token='.$place_api['api'].'&type=batchtickets')
            ->throw()->json();
        dd($suppliers);
    }

    // Получение Кассы и терминала за день
    public function getTodayShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterShift = Http::get($this->urlApi.'dash.getPaymentsReport?token='
            .$place_api['api']
            .'&dateFrom='.Carbon::today()->format('Ymd')
            .'&dateTo='.Carbon::today()->format('Ymd'))
            ->json('response');

        return $posterShift['total'];
    }

    // Получить все чеки
    public function getAllBills($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $getAllBills = Http::get($this->urlApi.'dash.getTransactions?token='
            .$place_api['api']
            .'&dateFrom='.Carbon::today()->format('Ymd')
            .'&dateTo='.Carbon::today()->format('Ymd'));

        //   -->>> EDIT RETURN
        return (array) $getAllBills->json('response', []);
    }

    // Получение прошлой смены
    public function getShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterShift = Http::get($this->urlApi.'finance.getCashShifts?token='
            .$place_api['api']
            .'&dateFrom='.Carbon::yesterday()->format('Ymd')
            .'&dateTo='.Carbon::yesterday()->format('Ymd'))
            ->json('response');

        return $posterShift[0]['amount_end'] / 100;
    }

    // Получение цехов из Постера

    public function getWorkshops($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $workshops = Http::get($this->urlApi.'menu.getWorkshops?token='
        .$place_api['api'])
            ->json('response');

        foreach ($workshops as $workshop) {

            Workshop::updateOrCreate(
                [
                    'place_id' => $place_id,
                    'name' => $workshop['workshop_name'],
                ],
                [
                    'place_id' => $place_id,
                    'name' => $workshop['workshop_name'],
                    'workshop_id' => $workshop['workshop_id'],
                ]
            );
        }
    }

    // Получение данных по цехам
    public function getTodayWorkshops($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterWorkshops = Http::get($this->urlApi.'dash.getAnalytics?format=json&token='
            .$place_api['api']
            .'&interpolate=day&type=workshops'
            .'&dateFrom='.Carbon::today()->format('Ymd')
            .'&dateTo='.Carbon::today()->format('Ymd')
        )->json('response');

        // dd($posterWorkshops);
        return $posterWorkshops;
    }

    public function getBills($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $bills = Http::get($this->urlApi.'finance.getAccounts?token='.$place_api['api'])
            ->json('response');

        dd($bills);

        foreach ($bills as $bill) {

            Bill::updateOrCreate(
                [
                    'place_id' => $place_id,
                    'name' => $bill['name'],
                ],
                [
                    'place_id' => $place_id,
                    'name' => $bill['name'],
                    'type' => $bill['type'],
                ]
            );
        }
    }

    // Получить список категорий
    public function getExpenseCategories($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterCategories = Http::get($this->urlApi.'finance.getCategories?token='.$place_api['api'])
            ->json('response');

        // dd($posterCategories);

        foreach ($posterCategories as $category) {

            ExpenseCategory::updateOrCreate(
                [
                    'place_id' => $place_id,
                    'name' => $category['name'],
                ],
                [
                    'place_id' => $place_id,
                    'name' => $category['name'],
                    'poster_category_id' => $category['category_id'],
                ]
            );
        }
    }

    // Получить список сотрудников
    public function getEmployees($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterEmplyoeers = Http::get($this->urlApi.'access.getEmployees?token='.$place_api['api'])
            ->json('response');
        dd($posterEmplyoeers);
    }

    public function MakeTransaction($place_id, $data)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api as api')->first();
        Http::post($this->urlApi.'finance.createTransactions?token='.$place_api, [
            'type' => 0,
            'category' => 1,
            'user_id' => 1,
            'amount_from' => 100, // сумма
            'account_from' => 1, // счет
            'date' => $data->added_at,
            'comment' => $data->comment,
        ]);
    }

    public function getCurrentShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $getCurrentShift = Http::get($this->urlApi.'finance.getCashShifts', [
            'token' => $place_api['api'],
            'dateFrom' => Carbon::today()->format('Ymd'),
        ])->json('response', true);

        return $getCurrentShift;
    }

    public function openShift($data)
    {
        $place_api = Place::whereId($data['place_id'])->select('settings->Poster_api  as api')->first();

        $localShift = Shift::where('place_id', $data['place_id'])->select('shift')->latest()->first();

        Http::post($this->urlApi.'finance.openCashShift?token='.$place_api['api'], [
            'spot_id' => 1,
            'user_id' => 4,
            'amount' => $localShift != null ? $localShift->shift : 0,
            'time' => Carbon::now()->format('Y-m-d H:i'),
            'is_fiscal' => 0,
        ]);
    }

    public function closeShift($data)
    {
        $place_api = Place::whereId($data['place_id'])->select('settings->Poster_api  as api')->first();

        $getShift = Http::get($this->urlApi.'finance.getCashShifts', [
            'token' => $place_api['api'],
            'dateFrom' => Carbon::today()->format('Ymd'),
        ])->json('response');

        Http::post($this->urlApi.'finance.closeCashShift?token='.$place_api['api'], [
            'cash_shift_id' => $getShift[0]['cash_shift_id'],
            'user_id' => $getShift[0]['user_id_start'],
            'amount' => $data['shift'],
            'time' => Carbon::now()->subMinute(1)->format('Y-m-d H:i'),
            'is_fiscal' => 0,
            'comment' => 'CRM',
        ]);
    }

    // Удаление чека из Постера

    public function deleteReceipt($data)
    {
        $place_api = Place::whereId($data['place_id'])->select('settings->Poster_api  as api')->first();

        $response = Http::post($this->urlApi.'transactions.removeTransaction?token='.$place_api['api'], [
            'spot_tablet_id' => 1,
            'transaction_id' => $data['receipt_id'],
        ]);

        if (isset($response['response']['err_code']) && $response['response']['err_code'] === 0) {
            Notification::make()
                ->title('#'.$data['receipt_id'].__('Receipt deleted'))
                ->icon('heroicon-o-backspace')
                ->color('success')
                ->send();
        } else {
            // Получаем сообщение об ошибке, если есть
            $errorMessage = $response['message'] ?? __('Unknown error');

            Notification::make()
                ->title(__('Error'))
                ->body($errorMessage)
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->send();
        }

    }
}
