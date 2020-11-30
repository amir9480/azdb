<?php

namespace App\Http\Controllers;

use App\Models\Buisness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $buisnesses = Buisness::whereHas('tickets', function ($query) {
            $query->where('tickets.user_id', Auth::id());
        })->get();
        $yourBuisnesses = Buisness::where('user_id', Auth::id())->orWhereHas('users', function ($query) {
            $query->where('buisness_user.user_id', Auth::id());
        })->get();

        return view('home', get_defined_vars());
    }
}
