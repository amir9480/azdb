<?php

namespace App\Models;

class Ticket extends \SanjabTicket\Models\Ticket
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buisness()
    {
        return $this->belongsTo(Buisness::class);
    }
}
