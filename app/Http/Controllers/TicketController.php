<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketRequest;
use App\Models\Buisness;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use SanjabTicket\Resources\TicketMessageResource;

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

    public function store(CreateTicketRequest $request, Buisness $buisness)
    {
        $ticket = $buisness->tickets()->create($request->validated());
        $file = null;
        if ($request->file('file') instanceof UploadedFile) {
            $file = $request->file('file')->store('tickets/'.$ticket->id, ['disk' => 'public']);
        }
        $ticket->messages()->create(array_merge(['text' => $request->input('description')], ['file' => $file]));

        return redirect()->route('tickets.show', ['buisness' => $buisness, 'ticket' => $ticket]);
    }

    public function show(Request $request, Buisness $buisness, Ticket $ticket)
    {
        abort_if(Auth::id() != $ticket->user_id, 403);
        $ticket->markSeen();
        if ($request->has('last_created_at') && is_numeric($request->input('last_created_at'))) {
            if ($request->wantsJson()) {
                $messages = $ticket->messages()->where('created_at', '>', Carbon::createFromTimestamp($request->input('last_created_at')))->get();
                return TicketMessageResource::collection($messages)->toArray($request);
            } else {
                try {
                    set_time_limit(600);
                    ini_set('max_execution_time', 600);
                } catch (Exception $e) {
                }
                Session::save();
                $response = response()->stream(function () use ($request, $ticket) {
                    echo "data: []\n\n";
                    ob_flush();
                    flush();
                    $lastCreatedAt = $request->input('last_created_at');
                    $unseenMessagesQuery = $ticket->messages()->select('id')->where('user_id', '!=', $ticket->user_id)->whereNull('seen_id');
                    $unseenMessages = $unseenMessagesQuery->get()->pluck('id')->toArray();
                    $lastMessageTime = time();
                    while (true) {
                        $ticket->markSeen();
                        $messages = $ticket->messages()->where('created_at', '>', Carbon::createFromTimestamp($lastCreatedAt))->get();

                        // Mark previous messages as seen.
                        if ($ticket->messages()->whereNotNull('seen_id')->whereIn('id', $unseenMessages)->exists()) {
                            $unseenMessages = [];
                            echo "data: seen\n\n";
                            ob_flush();
                            flush();
                        }

                        // Show new messages.
                        if ($messages->count() > 0) {
                            $unseenMessages = $unseenMessagesQuery->get()->pluck('id')->toArray();
                            $lastCreatedAt = $messages->max('created_at')->timestamp;
                            echo "data: ".json_encode(TicketMessageResource::collection($messages)->toArray($request))."\n\n";
                            ob_flush();
                            flush();
                        }

                        // Prevent Maximum execution time of N seconds exceeded error.
                        if ((microtime(true) - LARAVEL_START) + 3 >= intval(ini_get('max_execution_time'))) {
                            echo "data: close\n\n";
                            ob_flush();
                            flush();
                            return;
                        }

                        // Prevent keep alive timeout
                        if (time() - $lastMessageTime >= 10) {
                            echo "data: []\n\n";
                            ob_flush();
                            flush();
                            $lastMessageTime = time();
                        }

                        usleep(800000);
                    }
                });
                $response->headers->set('Content-Type', 'text/event-stream');
                $response->headers->set('X-Accel-Buffering', 'no');
                $response->headers->set('Cach-Control', 'no-cache');
                $response->headers->set('Connection', 'keep-alive');
                return $response;
            }
        }
        if ($request->wantsJson()) {
            $item = $this->itemResponse($ticket);
            return $item;
        }

        return view('ticket.show', get_defined_vars());
    }

    public function update(Request $request, Buisness $buisness, Ticket $ticket)
    {
        abort_unless($ticket->user_id == Auth::id(), 404);
        $request->validate(['text' => 'required|string', 'file' => 'nullable|array']);
        if ($ticket->closed_at) {
            $ticket->closed_at = null;
            $ticket->save();
        }
        $ticket->messages()->create(['text' => $request->input('text')]);
        return ['success' => true];
    }

    public function destroy(Buisness $buisness, Ticket $ticket)
    {
        abort_unless($ticket->user_id == Auth::id(), 404);
        $ticket->close();
        return ['success' => true];
    }
}
