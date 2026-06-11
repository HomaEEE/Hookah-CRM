<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Menu\Models\Category;
use Modules\Menu\Models\Product;
use Modules\Place\Models\Booking;
use Modules\Place\Models\Feedback;

class SiteController extends Controller
{
    public function switchLang($lang)
    {
        if (array_key_exists($lang, Config::get('app.languages'))) {
            Session::put('applocale', $lang);
        }

        return Redirect::back();
    }

    public function index($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();
        $feedbacks = Feedback::where('place_id', $place->id)->Approve(1)->get();

        return view('cms::site.home', compact('place', 'feedbacks'));
    }

    public function seet(?int $table_number = null)
    {
        $slug = 'od';
        $place = Place::where('slug', $slug)->first();

        if (is_null($table_number)) {
            $table_number = Session::get('table', null);
        } else {
            Session::put('table', $table_number);
        }

        return view('cms::site.seet', compact('place', 'table_number'));

    }

    public function menu(?int $table_number = null)
    {
        $slug = 'od';
        $place = Place::where('slug', $slug)->first();

        $categories = Category::where('place_id', $place->id)
            ->where('parent_category', 0)
            ->VisibleMenu(true)
            ->orderBy('sort_order', 'asc')
            ->with('childs', 'products')
            ->get();

        if (is_null($table_number)) {
            $table_number = Session::get('table', null);
        } else {
            Session::put('table', $table_number);
        }

        $productsJson = Cache::remember('products', 60, function () {
            return Product::VisibleMenu(true)->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'frontend_name' => $item->frontend_name,
                    'frontend_desc' => $item->frontend_desc,
                    'price' => number_format($item->price, 0),
                    'image' => $item->getFirstMediaUrl('images') ?: null,
                    'sup' => $item->sup,
                    'out' => $item->out ? number_format($item->out, 0) : null,
                    'ingredients' => $item->ingredients,
                ];
            })->toJson();
        });

        return view('cms::site.menu', compact('place', 'categories', 'table_number', 'productsJson'));
    }

    public function getProduct($id)
    {
        $locale = session('locale', 'uk');
        $product = Product::findOrFail($id);

        return response()->json([
            'product_name' => $product->product_name,
            'frontend_name' => $product->frontend_name,
            'frontend_desc' => $product->frontend_desc,
            'price' => number_format($product->price, 0),
            'image' => $product->getFirstMediaUrl('products') ?: 'assets/imgs/menu/no-photo.jpg',
            'sup' => $product->sup,
            'out' => $product->out ? number_format($product->out, 0) : null,
            'ingredients' => $product->ingredients,
        ]);
    }

    public function booking($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('cms::site.booking', compact('place'));
    }

    public function feedbacks($slug = 'od')
    {

        $place = Place::where('slug', $slug)->first();
        $feedbacks = Feedback::where('place_id', $place->id)->Approve(1)->get();

        return view('cms::site.feedbacks', compact('place', 'feedbacks'));
    }

    public function vacancy($slug = 'od')
    {

        $place = Place::where('slug', $slug)->first();
        $vacancies = Feedback::where('place_id', $place->id)->Approve(1)->get();

        return view('cms::site.vacancy', compact('place', 'vacancies'));
    }

    public function rules($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('cms::site.rules', compact('place'));
    }

    public function contacts($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('cms::site.contacts', compact('place'));
    }

    public function sendFeedback(Request $request)
    {

        Feedback::create([
            'place_id' => $request->place_id,
            'name' => $request->name,
            'contact' => $request->contact,
            'text' => $request->text,
        ]);

        return response()->json(['success' => 'Send Feedback']);
    }

    public function sendReserve(Request $request)
    {

        echo $request;

        Booking::create([
            'user_id' => 1,
            'place_id' => $request->place_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'time' => Carbon::createFromFormat('d-m-Y H:i', $request->date.' '.$request->time),
            'person' => $request->person,
        ]);

        return response()->json(['success' => 'Send Booking']);
    }
}
