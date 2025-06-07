<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

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

        return view('site.home', compact('place'));
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

        return view('site.seet', compact('place', 'table_number'));

    }

    public function menu(?int $table_number = null)
    {
        $slug = 'od';
        $place = Place::where('slug', $slug)->first();

        $categories = Category::where('place_id', $place->id)
            ->where('parent_category', 0)->orderBy('sort_order', 'asc')
            ->with('childs', 'products')->orderBy('sort_order', 'asc')->VisibleMenu(true)->get();

        if (is_null($table_number)) {
            $table_number = Session::get('table', null);
        } else {
            Session::put('table', $table_number);
        }

        return view('site.menu', compact('place', 'categories', 'table_number'));
    }

    public function booking($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.booking', compact('place'));
    }

    public function pays($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.pays', compact('place'));
    }

    public function feedbacks($slug = 'od')
    {

        $place = Place::where('slug', $slug)->first();
        $feedbacks = Feedback::where('place_id', $place->id)->Approve(1)->get();

        return view('site.feedbacks', compact('place', 'feedbacks'));
    }

    public function vacancy($slug = 'od')
    {

        $place = Place::where('slug', $slug)->first();
        $vacancies = Feedback::where('place_id', $place->id)->Approve(1)->get();

        return view('site.vacancy', compact('place', 'vacancies'));
    }

    public function rules($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.rules', compact('place'));
    }

    public function contacts($slug = 'od')
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.contacts', compact('place'));
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
