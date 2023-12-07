<?php

namespace App\Http\Controllers;

use App\Models\{Category, Place, Feedback, Booking};
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use Carbon\Carbon;

class SiteController extends Controller
{
    public function switchLang($lang)
    {
        if (array_key_exists($lang, Config::get('app.languages'))) {
            Session::put('applocale', $lang);
        }

        return Redirect::back();
    }

    public function home()
    {
        $places = Place::all();

        return view('site.index', compact('places'));
    }

    public function index($slug)
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.index', compact('place'));
    }

    public function menu($slug = "od")
    {
        $place = Place::where('slug', $slug)->first();

        $categories = Category::where('place_id', $place->id)
            ->where('parent_category', 0)
            ->with('childs', 'products')->orderBy('sort_order', 'asc')->VisibleMenu(true)->get();

        return view('site.menu', compact('place', 'categories'));
    }

    public function booking($slug = "od")
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.booking', compact('place'));
    }

     public function pays($slug = "od")
     {
         $place = Place::where('slug', $slug)->first();

         return view('site.pays', compact('place'));
     }

    public function feedbacks($slug = "od")
    {

        $place = Place::where('slug', $slug)->first();
        $feedbacks = Feedback::where('place_id', $place->id)->Approve(1)->get();

        return view('site.feedbacks', compact('place', 'feedbacks'));
    }

    public function contacts($slug = "od")
    {
        $place = Place::where('slug', $slug)->first();

        return view('site.contacts', compact('place'));
    }

    public function sendFeedback( Request $request) {

        Feedback::create([
            'place_id' => $request->place_id,
            'name' => $request->name,
            'contact' => $request->contact,
            'text' => $request->text,
        ]);
        return response()->json(['success'=>'Send Feedback']);
    }

    public function sendReserve( Request $request) {

        Booking::create([
            'user_id' => 1,
            'place_id' => $request->place_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'time' =>  Carbon::createFromFormat('d-m-Y H:i', $request->date . ' ' . $request->time),
            'person' => $request->person,
        ]);
        return response()->json(['success'=>'Send Booking']);
    }
}
