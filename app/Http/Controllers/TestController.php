<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Shift;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    private $urlApi = 'https://joinposter.com/api/';

    public function GetPlace()
    {
        return dd(Place::whereIn('id', Auth::user()->place->pluck('id'))->get());
    }

    public function GetUser($place_id)
    {
        $list = User::whereHas('places', function ($query) use ($place_id) {
            $query->where('place_id', $place_id);
        })->get();
        dd($list);
    }

    public function getShift($place_id)
    {
        $shift = Shift::where('place_id', $place_id)->select('shift')->latest()->first();

        return $shift->shift;
    }

    public function MakeTransaction($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api as api')->first();
        $response = Http::post($this->urlApi.'finance.createCashShiftTransaction?token='.$place_api, [
            'type' => 0,
            'category' => 3,
            'user_id' => 1,
            'amount_from' => 100, //сумма
            'account_from' => 2, //счет
            'date' => '2023-04-16 22:15:32',
            'comment' => 'My test finance transaction',
        ]);

        return $response->successful();
    }

    public function getSpot($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api as api')->first();
        $response = Http::posterApi()->get('access.getTablets?token='.$place_api);
        echo $place_api;
        echo $response;
    }

    public function getTodayShift($place_id)
    {
        $place_api = Place::whereId($place_id)->select('settings->Poster_api  as api')->first();
        $posterShift = Http::posterApi()->get('dash.getPaymentsReport?token='
        .$place_api['api']
        .'&dateFrom='.Carbon::today()->format('Ymd')
        .'&dateTo='.Carbon::today()->format('Ymd'))
            ->json('response');

        return $posterShift['total'];
    }
}
