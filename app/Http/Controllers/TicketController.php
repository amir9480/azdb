<?php

namespace App\Http\Controllers;

use App\Models\Buisness;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
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

    public function index(Buisness $buisness)
    {
        $tickets = $buisness->tickets()->where('tickets.user_id', Auth::id())->paginate();
        return view('ticket.index', get_defined_vars());
    }

    public function create(Buisness $buisness)
    {
        $categories = TicketCategory::query()->getCached();
        $priorities = TicketPriority::query()->getCached();

        return view('ticket.create', get_defined_vars());
    }

    public function show(Buisness $buisness, Ticket $ticket)
    {
        abort_if(Auth::id() != $ticket->user_id, 403);

        return view('ticket.show', get_defined_vars());
    }
}
