<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EventService;
use Exception;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eventService = new EventService();

        $events = $eventService->getUpcomingEventsForReminders();
        foreach ($events as $event) {
            try {
                $eventService->sendReminder($event);
            } catch (Exception $e) {
                Log::error('Error sending reminder email: ' . $e->getMessage());
                $this->error('Error sending reminder email: ' . $e->getMessage());
            }

        }

        $this->info('Event reminders sent successfully.');
    }
}
