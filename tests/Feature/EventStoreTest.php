<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test that an authenticated user can create an event.
     *
     * @return void
     */
    public function test_authenticated_user_can_create_event()
    {
        $startTime = Carbon::now()->addDays(1)->format('Y-m-d H:i:s');
        $endTime = Carbon::now()->addDays(3)->addHours(1)->format('Y-m-d H:i:s');
        $eventData = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];

        // Act as the authenticated user and post the event data
        $response = $this->actingAs($this->user)->post(route('events.store'), $eventData);

        // Assert that the response redirects to the events index
        $response->assertRedirect(route('events.index'));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Event created successfully.');

        // Assert that the event is in the database
        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'user_id' => $this->user->id,
        ]);

        // Retrieve the event and assert its start and end times
        $event = Event::where('title', 'Test Event')->first();
        $this->assertNotNull($event);
        $this->assertEquals($startTime, $event->start_time);
        $this->assertEquals($endTime, $event->end_time);
    }

    /**
     * Test that an unauthenticated user cannot create an event.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_create_event()
    {
        $eventData = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'start_time' => '2023-06-01 10:00:00',
            'end_time' => '2023-06-01 11:00:00',
        ];

        // Post the event data without authentication
        $response = $this->post(route('events.store'), $eventData);

        // Assert that the response redirects to the login page
        $response->assertRedirect(route('login'));
        // Assert that the event is not in the database
        $this->assertDatabaseMissing('events', ['title' => 'Test Event']);
    }

    /**
     * Test that an event requires a title.
     *
     * @return void
     */
    public function test_event_requires_title()
    {
        $eventData = [
            'description' => 'This is a test event',
            'start_time' => '2023-06-01 10:00:00',
            'end_time' => '2023-06-01 11:00:00',
        ];

        // Act as the authenticated user and post the event data without a title
        $response = $this->actingAs($this->user)->post(route('events.store'), $eventData);

        // Assert that the session has errors for the title
        $response->assertSessionHasErrors('title');
        // Assert that the event is not in the database
        $this->assertDatabaseMissing('events', ['description' => 'This is a test event']);
    }

    /**
     * Test that an event requires valid dates.
     *
     * @return void
     */
    public function test_event_requires_valid_dates()
    {
        $eventData = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'start_time' => 'invalid date',
            'end_time' => 'invalid date',
        ];

        // Act as the authenticated user and post the event data with invalid dates
        $response = $this->actingAs($this->user)->post(route('events.store'), $eventData);

        // Assert that the session has errors for the start and end times
        $response->assertSessionHasErrors(['start_time', 'end_time']);
        // Assert that the event is not in the database
        $this->assertDatabaseMissing('events', ['title' => 'Test Event']);
    }

    /**
     * Test that the end time must be after the start time.
     *
     * @return void
     */
    public function test_end_time_must_be_after_start_time()
    {
        $eventData = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'start_time' => '2023-06-01 11:00:00',
            'end_time' => '2023-06-01 10:00:00',
        ];

        // Act as the authenticated user and post the event data with an end time before the start time
        $response = $this->actingAs($this->user)->post(route('events.store'), $eventData);

        // Assert that the session has errors for the end time
        $response->assertSessionHasErrors('end_time');
        // Assert that the event is not in the database
        $this->assertDatabaseMissing('events', ['title' => 'Test Event']);
    }
}
