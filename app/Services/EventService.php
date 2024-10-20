<?php

namespace App\Services;

use App\Mail\EventReminderMail;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Class EventService
 * @package App\Services
 */
class EventService
{
    /**
     * Create a new event.
     *
     * @param array $data
     * @return bool
     */
    public function createEvent(array $data)
    {
        return Event::insert($data);
    }

    /**
     * Store a new event with additional data.
     *
     * @param array $data
     * @return Event
     */
    public function storeEvent(array $data)
    {
        $data['event_id'] = $this->generateEventId();
        $data['user_id'] = auth()->id();
        return Event::create($data);
    }

    /**
     * Update an existing event.
     *
     * @param Event $event
     * @param array $data
     * @return Event
     */
    public function updateEvent(Event $event, array $data)
    {
        $event->update($data);
        return $event;
    }

    /**
     * Delete an event.
     *
     * @param Event $event
     * @return bool|null
     * @throws \Exception
     */
    public function deleteEvent(Event $event)
    {
        return $event->delete();
    }

    /**
     * Get upcoming events for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingEvents()
    {
        return Event::where('user_id', auth()->id())
            ->where('start_time', '>', now())
            ->where('is_completed', false)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get completed events for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompletedEvents()
    {
        return Event::where('user_id', auth()->id())
            ->where('is_completed', true)
            ->orderBy('end_time', 'desc')
            ->get();
    }

    /**
     * Generate a unique event ID.
     *
     * @return string
     */
    public function generateEventId()
    {
        $prefix = 'EVT-';
        $uniqueId = Str::random(8);
        return $prefix . $uniqueId;
    }

    /**
     * Mark an event as complete.
     *
     * @param Event $event
     * @return Event
     */
    public function markAsComplete(Event $event)
    {
        $event->update(['is_completed' => true]);
        return $event;
    }

    /**
     * Get upcoming events for sending reminders.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingEventsForReminders()
    {
        return Event::where('start_time', '>', now())
            ->where('is_completed', false)
            ->where(function ($query) {
                $query->whereNull('last_reminder_sent_at')->orWhere('last_reminder_sent_at', '<=', now()->subMinutes(15));
            })
            ->get();
    }

    /**
     * Send a reminder for an event.
     *
     * @param Event $event
     * @return void
     */
    public function sendReminder(Event $event)
    {
        Mail::to($event->user->email)->send(new EventReminderMail($event));
        $event->update(['last_reminder_sent_at' => now()]);
    }
}
