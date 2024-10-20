<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $event;
    protected $startTime;
    protected $endTime;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->event = Event::factory()->create(['user_id' => $this->user->id]);
        $this->startTime = Carbon::now()->addDays(1)->format('Y-m-d H:i:s');
        $this->endTime = Carbon::now()->addDays(3)->addHours(1)->format('Y-m-d H:i:s');
    }

    /**
     * Test that an authenticated user can update an event.
     *
     * @return void
     */
    public function test_authenticated_user_can_update_event()
    {
        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'This is an updated event description',
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];

        // Act as the authenticated user and put the updated event data
        $response = $this->actingAs($this->user)->put(route('events.update', $this->event->id), $updatedData);

        // Assert that the response redirects to the events index
        $response->assertRedirect(route('events.index'));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Event updated successfully.');

        // Assert that the event is in the database with updated data
        $this->assertDatabaseHas('events', [
            'id' => $this->event->id,
            'title' => 'Updated Event Title',
            'description' => 'This is an updated event description',
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
    }

    /**
     * Test that an unauthenticated user cannot update an event.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_update_event()
    {
        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'This is an updated event description',
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];

        // Attempt to put the updated event data without authentication
        $response = $this->put(route('events.update', $this->event->id), $updatedData);

        // Assert that the response redirects to the login page
        $response->assertRedirect(route('login'));
        // Assert that the event is not in the database with the updated title
        $this->assertDatabaseMissing('events', ['title' => 'Updated Event Title']);
    }

    /**
     * Test that event update requires a title.
     *
     * @return void
     */
    public function test_event_update_requires_title()
    {
        $updatedData = [
            'description' => 'This is an updated event description',
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];

        // Act as the authenticated user and put the updated event data without a title
        $response = $this->actingAs($this->user)->put(route('events.update', $this->event->id), $updatedData);

        // Assert that the response has session errors for the title
        $response->assertSessionHasErrors('title');
        // Assert that the event is not in the database with the updated description
        $this->assertDatabaseMissing('events', ['description' => 'This is an updated event description']);
    }

    /**
     * Test that event update requires valid dates.
     *
     * @return void
     */
    public function test_event_update_requires_valid_dates()
    {
        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'This is an updated event description',
            'start_time' => 'invalid date',
            'end_time' => 'invalid date',
        ];

        // Act as the authenticated user and put the updated event data with invalid dates
        $response = $this->actingAs($this->user)->put(route('events.update', $this->event->id), $updatedData);

        // Assert that the response has session errors for start_time and end_time
        $response->assertSessionHasErrors(['start_time', 'end_time']);
        // Assert that the event is not in the database with the updated title
        $this->assertDatabaseMissing('events', ['title' => 'Updated Event Title']);
    }

    /**
     * Test that end time must be after start time on update.
     *
     * @return void
     */
    public function test_end_time_must_be_after_start_time_on_update()
    {
        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'This is an updated event description',
            'start_time' => $this->endTime,
            'end_time' => $this->startTime,
        ];

        // Act as the authenticated user and put the updated event data with end time before start time
        $response = $this->actingAs($this->user)->put(route('events.update', $this->event->id), $updatedData);

        // Assert that the response has session errors for end_time
        $response->assertSessionHasErrors('end_time');
        // Assert that the event is not in the database with the updated title
        $this->assertDatabaseMissing('events', ['title' => 'Updated Event Title']);
    }
}

