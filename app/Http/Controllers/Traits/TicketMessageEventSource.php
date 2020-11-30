<?php

namespace App\Http\Controllers\Traits;

use App\Models\Ticket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use SanjabTicket\Resources\TicketMessageResource;
use Session;

trait TicketMessageEventSource
{
    public function ticketEventSource(Request $request, Ticket $ticket)
    {
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

    public function ticketCreateMessage(Request $request, Ticket $ticket)
    {
        $request->validate(['text' => 'required|string']);
        if ($ticket->closed_at) {
            $ticket->closed_at = null;
            $ticket->save();
        }
        $ticket->messages()->create(['text' => $request->input('text')]);
        return true;
    }
}
