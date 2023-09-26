<?php

namespace App\Http\Controllers;

use App\Models\{Withdrawal, Tip, Supply, Product, Place, Expense, Category, Ingredient, Shift};
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PosterApiController extends Controller
{
    private $url = 'https://joinposter.com';

    private $urlApi = 'https://joinposter.com/api/';

    public function getCategories($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseCategories = Http::get($this->urlApi . 'menu.getCategories?token=' . $place_api['api'])
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
    }

    public function updateCategories($place_id, $category)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseCategories = Http::get($this->urlApi . 'menu.getProducts?token=' . $place_api['api'] . '&category_id=' . $category . '&type=products')
            ->throw()->json();

        $products = $responseCategories['response'];

        foreach ($products as $product) {

            $ingredient_array = [];

            if (isset($product['ingredients'])) {
                foreach ($product['ingredients'] as $ingredient) {
                    array_push($ingredient_array, $ingredient['ingredient_name']);
                }
            }
            $product = Product::updateOrCreate(
                [
                    'place_id' => $place_id,
                    'product_name' => $product['product_name'],
                ],
                [
                    'menu_category_id' => $product['menu_category_id'],
                    'product_id' => $product['product_id'],
                    'cost' => $product['cost'],
                    'price' => $product['spots'][0]['price'] / 100,
                    'out' => $product['out'],
                    'ingredients' => empty($ingredient_array) ? '' : implode(', ', $ingredient_array),
                ]
            );
            $product = $product->getOriginal('out');
        }
    }

    public function getProducts($place_id)
    {

        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseMenu = Http::get($this->urlApi . 'menu.getProducts?token=' . $place_api['api'])
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
                    'place_id' => $place_id
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


    public function getIngredients($place_id)
    {

        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $responseMenu = Http::get($this->urlApi . 'menu.getIngredients?token=' . $place_api['api'])
            ->throw()->json();
        $ingredients = $responseMenu['response'];

        dd($ingredients);

        foreach ($ingredients as $ingredient) {

            Ingredient::updateOrCreate(
                [
                    'product_name' => $ingredient['product_name'],
                    'place_id' => $place_id
                ],
                [
                    'ingredient' => $ingredient['ingredient_name'],
                    'unit' => $ingredient['ingredient_unit'],
                ]

                // $table->string('product_name')->index();
                // $table->integer('barcode')->nullable();
                // $table->integer('cost')->nullable();
                // $table->integer('workshop_id')->nullable();
                // $table->integer('product_id');
                // $table->integer('cost')->nullable();
                // $table->integer('type');
                // $table->string('unit');
                // $table->string('price');
            );
        }
    }


    //Получение Кассы и терминала за день
    public function getTodayShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterShift = Http::get($this->urlApi . 'dash.getPaymentsReport?token='
            . $place_api['api']
            . '&dateFrom=' . Carbon::today()->format('Ymd')
            . '&dateTo=' . Carbon::today()->format('Ymd'))
            ->json('response');
        return $posterShift['total'];
    }


    //Получение прошлой смены
    public function getShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterShift = Http::get($this->urlApi . 'finance.getCashShifts?token='
            . $place_api['api']
            . '&dateFrom=' . Carbon::yesterday()->format('Ymd')
            . '&dateTo=' . Carbon::yesterday()->format('Ymd'))
            ->json('response');

        return $posterShift[0]['amount_end'] / 100;
    }

    //Получение данных по цехам
    public function getTodayWorkshops($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterWorkshops = Http::get($this->urlApi . 'dash.getAnalytics?format=json&token='
            . $place_api['api']
            . '&interpolate=day&type=workshops'
            . '&dateFrom=' . Carbon::today()->format('Ymd')
            . '&dateTo=' . Carbon::today()->format('Ymd'))
            ->json('response');

        return $posterWorkshops;
    }

    //Получить список категорий счетов
    public function getTransatcionCategories($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterCategories = Http::get($this->urlApi . 'finance.getCategories?token=' . $place_api['api'])
            ->json('response');
        dd($posterCategories);
    }

    public function getLocalExpenses($place_id)
    {
        $cash_expense = Expense::select('sum')->where(
            [
                ['type_pay', 0],
                ['place_id', $place_id],
            ]
        )
            ->whereDate('added_at', Carbon::today());

        return $cash_expense->sum('sum');
    }

    public function getLocalWithdrawal($place_id)
    {
        $withdrawal = Withdrawal::select('sum')
            ->where('place_id', $place_id)
            ->whereDate('created_at', Carbon::today());
        $TOTAL = $withdrawal->sum('sum');

        return $TOTAL;
    }

    public function getLocalTips($place_id)
    {
        $tips = Tip::select('tips')
            ->where('place_id', $place_id)
            ->whereDate('created_at', Carbon::today());
        $TOTAL = $tips->sum('tips');

        return $TOTAL;
    }


    //Получить список сотрудников
    public function getEmployees($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterCategories = Http::get($this->urlApi . 'access.getEmployees?token=' . $place_api['api'])
            ->json('response');
        dd($posterCategories);
    }

    public function MakeTransaction($place_id, $data)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api as api')->first();
        Http::post($this->urlApi . 'finance.createTransactions?token=' . $place_api, [
            'type' => 0,
            'category' => 1,
            'user_id' => 1,
            'amount_from' => 100, //сумма
            'account_from' => 1, //счет
            'date' => $data->added_at,
            'comment' => $data->comment,
        ]);
    }

    public function getCurrentShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $getCurrentShift = Http::get($this->urlApi . 'finance.getCashShifts', [
            'token' => $place_api['api'],
            'dateFrom' => Carbon::today()->format('Ymd')
        ])->json('response', true);

        return $getCurrentShift;
    }


    public function openShift($data)
    {
        $place_api = Place::whereId($data['place_id'])->select('settings->Poster_api  as api')->first();

        $localShift = Shift::where('place_id', $data['place_id'])->select('shift')->latest()->first();

        Http::post($this->urlApi . 'finance.openCashShift?token=' . $place_api['api'], [
            'spot_id'   => 1,
            'user_id'   => 4,
            'amount'    => $localShift != null ? $localShift->shift : 0,
            'time'      => Carbon::now()->format('Y-m-d H:i'),
            'is_fiscal' => 0
        ]);
    }

    public function closeShift($data)
    {
        $place_api = Place::whereId($data['place_id'])->select('settings->Poster_api  as api')->first();

        $getShift = Http::get($this->urlApi . 'finance.getCashShifts', [
            'token' => $place_api['api'],
            'dateFrom' => Carbon::today()->format('Ymd')
        ])->json('response');

        Http::post($this->urlApi . 'finance.closeCashShift?token=' . $place_api['api'], [
            'cash_shift_id' => $getShift[0]['cash_shift_id'],
            'user_id'       => $getShift[0]['user_id_start'],
            'amount'        => $data['shift'],
            'time'          => Carbon::now()->subMinute(2)->format('Y-m-d H:i'),
            'is_fiscal'     => 0,
            'comment'       => $data['comment']
        ]);

    }
}
