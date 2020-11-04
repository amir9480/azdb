<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuisnessRequest;
use App\Models\Buisness;
use Illuminate\Support\Facades\Auth;

class BuisnessController extends Controller
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

    public function create()
    {
        if (Auth::user()->buisness && Auth::user()->buisness->approved) {
            return redirect()->route('buisnesses.show', ['buisness' => Auth::user()->buisness]);
        }

        return view('buisness.create');
    }

    public function store(BuisnessRequest $request)
    {
        Auth::user()->buisness()->create($request->validated());

        return redirect()
                ->back()
                ->with('success', __('Your buisness panel request submitted successfuly.'));
    }

    public function redirect(Buisness $buisness)
    {
        return redirect()->route('tickets.index', ['buisness' => $buisness]);
    }
}
