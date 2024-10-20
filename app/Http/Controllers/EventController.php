<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Requests\CsvImportRequest;
use App\Models\Event;
use App\Services\EventService;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $eventService;

    /**
     * EventController constructor.
     *
     * @param EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the events.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $upcomingEvents = $this->eventService->getUpcomingEvents();
            $completedEvents = $this->eventService->getCompletedEvents();
            return view('events.index', compact('upcomingEvents', 'completedEvents'));
        } catch (Exception $e) {
            Log::error('Error displaying events: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error displaying events.');
        }
    }

    /**
     * Store a newly created event in storage.
     *
     * @param EventStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EventStoreRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $this->eventService->storeEvent($validatedData);
            return redirect()->route('events.index')->with('success', 'Event created successfully.');
        } catch (Exception $e) {
            Log::error('Error storing event: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error storing event.');
        }
    }

    /**
     * Update the specified event in storage.
     *
     * @param EventUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EventUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $event = Event::findOrFail($id);

            $this->eventService->updateEvent($event, $validatedData);
            return redirect()->route('events.index')->with('success', 'Event updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error updating event.');
        }
    }

    /**
     * Remove the specified event from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $this->eventService->deleteEvent($event);
            return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error deleting event.');
        }
    }

    /**
     * Import events from a CSV file.
     *
     * @param CsvImportRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(CsvImportRequest $request)
    {
        try {
            $request->validated();

            $file = $request->file('csv_file');
            $events = array_map('str_getcsv', file($file->getPathname()));
            array_shift($events); // Remove header row
            $eventsToInsert = [];
            foreach ($events as $eventData) {
                $tempArray['event_id'] = $this->eventService->generateEventId();
                $tempArray['user_id'] = auth()->id();
                $tempArray['title'] = $eventData[0];
                $tempArray['description'] = $eventData[1];
                $tempArray['start_time'] = $eventData[2];
                $tempArray['end_time'] = $eventData[3];

                $eventsToInsert[] = $tempArray;
            }
            foreach ($eventsToInsert as $eventData) {
                $this->eventService->createEvent($eventData);
            }

            return redirect()->route('events.index')->with('success', 'Events imported successfully.');
        } catch (Exception $e) {
            Log::error('Error importing events: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error importing events.');
        }
    }

    /**
     * Mark the specified event as complete.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsComplete($id)
    {
        try {
            $event = Event::findOrFail($id);
            $this->authorize('update', $event);

            $this->eventService->markAsComplete($event);

            return redirect()->route('events.index')->with('success', 'Event marked as complete.');
        } catch (Exception $e) {
            Log::error('Error marking event as complete: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error marking event as complete.');
        }
    }

    /**
     * Download a CSV template for importing events.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadCsvTemplate()
    {
        try {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="event_import_template.csv"',
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['title', 'description', 'start_time', 'end_time']);
                fputcsv($file, ['Sample Event', 'This is a sample event description', '2023-01-01 10:00:00', '2023-01-01 12:00:00']);
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (Exception $e) {
            Log::error('Error downloading CSV template: ' . $e->getMessage(), ["Exception" => $e]);
            return redirect()->route('events.index')->with('error', 'Error downloading CSV template.');
        }
    }
}
