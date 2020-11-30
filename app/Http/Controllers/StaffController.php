<?php

namespace App\Http\Controllers;

use App\Models\Buisness;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function show(Buisness $buisness)
    {
        abort_unless($buisness->user_id == Auth::id(), 403);
        $users = $buisness->users;

        return view('staff.show', get_defined_vars());
    }

    public function store(Request $request, Buisness $buisness)
    {
        abort_unless($buisness->user_id == Auth::id(), 403);
        $request->validate(['email' => 'required|email|exists:users,email']);
        $newUser = User::where('email', $request->input('email'))->first();
        $buisness->users()->syncWithoutDetaching([$newUser->id]);

        return redirect()
                ->back()
                ->with('success', __('New staff added successfully.'));
    }

    public function destroy(Request $request, Buisness $buisness)
    {
        abort_unless($buisness->user_id == Auth::id(), 403);
        $buisness->users()->detach([$request->input('user')]);

        return redirect()
                ->back()
                ->with('success', __('Staff deleted successfully.'));
    }
}
