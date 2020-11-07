<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use SanjabTicket\Resources\TicketMessageResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'subject'         => $this->subject,
            'user'            => ['id' => $this->user->id, 'name' => $this->user->name],
            'created_at'      => $this->created_at->timestamp,
            'created_at_diff' => $this->created_at_diff,
            'status'          => $this->status,
            'category'        => ['name' => $this->category->name],
            'priority'        => ['name' => $this->priority->name],
            'messages'        => TicketMessageResource::collection($this->messages),
        ];
    }
}
