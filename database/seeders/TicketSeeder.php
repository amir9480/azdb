<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;
use SanjabTicket\Models\TicketPriority;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketPriority::create(['name' => 'پایین', 'color' => '#000000']);
        TicketPriority::create(['name' => 'متوسط', 'color' => '#00ff00']);
        TicketPriority::create(['name' => 'بالا', 'color' => '#ff9800']);
        TicketPriority::create(['name' => 'اورژانسی', 'color' => '#ff0000']);

        TicketCategory::create(['name' => 'پشتیبانی', 'color' => '#00ff00']);
        TicketCategory::create(['name' => 'فنی', 'color' => '#0000ff']);
        TicketCategory::create(['name' => 'پیشنهاد', 'color' => '#000000']);
        TicketCategory::create(['name' => 'انتقاد', 'color' => '#ff9800']);
        TicketCategory::create(['name' => 'شکایت', 'color' => '#ff0000']);

        Ticket::factory(50)->create();
    }
}
