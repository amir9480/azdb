<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TicketMessageEventSource;
use App\Http\Requests\BuisnessRequest;
use App\Models\Buisness;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuisnessController extends Controller
{
    use TicketMessageEventSource;

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

    public function show(Buisness $buisness)
    {
        abort_unless(Auth::user()->can('view', $buisness), 403);
        $tickets = $buisness->tickets()->paginate();

        return view('buisness.show', get_defined_vars());
    }

    public function showTicket(Request $request, Buisness $buisness, Ticket $ticket)
    {
        abort_unless(Auth::user()->can('view', $buisness) && $ticket->buisness_id == $buisness->id, 403);
        $ticket->markSeen();
        if ($request->has('last_created_at') && is_numeric($request->input('last_created_at'))) {
            return $this->ticketEventSource($request, $ticket);
        }

        return view('buisness.show-ticket', get_defined_vars());
    }

    public function store(BuisnessRequest $request)
    {
        Auth::user()->buisness()->create($request->validated());

        return redirect()
                ->back()
                ->with('success', __('Your buisness panel request submitted successfuly.'));
    }

    public function sendTicketMessage(Request $request, Buisness $buisness, Ticket $ticket)
    {
        abort_unless(Auth::user()->can('view', $buisness) && $ticket->buisness_id == $buisness->id, 403);
        return ['success' => $this->ticketCreateMessage($request, $ticket)];
    }

    public function redirect(Buisness $buisness)
    {
        return redirect()->route('tickets.index', ['buisness' => $buisness]);
    }
}
