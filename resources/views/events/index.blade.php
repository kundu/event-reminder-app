@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Event Management Dashboard</h1>

        <div class="space-y-6">
            <!-- Create New Event Accordion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <button class="w-full text-left px-6 py-4 bg-indigo-500 hover:bg-indigo-600 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between" onclick="toggleAccordion('createEvent')">
                    <h2 class="text-xl font-semibold text-white">Create New Event</h2>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="createEvent" class="hidden px-6 py-4">
                    <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">Title</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="title" type="text" name="title" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="start_time">Start Time</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="start_time" type="datetime-local" name="start_time" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="end_time">End Time</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="end_time" type="datetime-local" name="end_time" required>
                            </div>
                        </div>
                        <button class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out" type="submit">
                            Create Event
                        </button>
                    </form>
                </div>
            </div>

            <!-- Upcoming Events Accordion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <button class="w-full text-left px-6 py-4 bg-green-500 hover:bg-green-600 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between" onclick="toggleAccordion('upcomingEvents')">
                    <h2 class="text-xl font-semibold text-white">Upcoming Events</h2>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="upcomingEvents" class="hidden px-6 py-4">
                    @foreach ($upcomingEvents as $event)
                        <div class="bg-white shadow-md rounded-lg px-6 py-4 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-xl font-bold text-gray-800">{{ $event->title }}</h3>
                                <div class="flex space-x-2">
                                    <button onclick="toggleEditForm('{{ $event->id }}')" class="text-blue-500 hover:text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <form action="{{ route('events.complete', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-500 hover:text-green-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-2">{{ $event->description }}</p>
                            <p class="text-gray-600 mb-2"><span class="font-semibold">Start:</span> {{ $event->start_time }}</p>
                            <p class="text-gray-600 mb-2"><span class="font-semibold">End:</span> {{ $event->end_time }}</p>

                            <!-- Edit form (hidden by default) -->
                            <div id="editForm{{ $event->id }}" class="hidden mt-4 bg-gray-100 p-4 rounded-lg">
                                <h4 class="text-lg font-semibold mb-2">Edit Event</h4>
                                <form action="{{ route('events.update', $event->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title{{ $event->id }}">Title</label>
                                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="title{{ $event->id }}" type="text" name="title" value="{{ $event->title }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description{{ $event->id }}">Description</label>
                                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description{{ $event->id }}" name="description">{{ $event->description }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2" for="start_time{{ $event->id }}">Start Time</label>
                                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="start_time{{ $event->id }}" type="datetime-local" name="start_time" value="{{ date('Y-m-d\TH:i', strtotime($event->start_time)) }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2" for="end_time{{ $event->id }}">End Time</label>
                                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="end_time{{ $event->id }}" type="datetime-local" name="end_time" value="{{ date('Y-m-d\TH:i', strtotime($event->end_time)) }}" required>
                                        </div>
                                    </div>
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out" type="submit">
                                        Update Event
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Completed Events Accordion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <button class="w-full text-left px-6 py-4 bg-blue-500 hover:bg-blue-600 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between" onclick="toggleAccordion('completedEvents')">
                    <h2 class="text-xl font-semibold text-white">Completed Events</h2>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="completedEvents" class="hidden px-6 py-4">
                    @foreach ($completedEvents as $event)
                        <div class="bg-white shadow-md rounded-lg px-6 py-4 mb-4">
                            <h3 class="text-xl font-bold mb-2 text-gray-800">{{ $event->title }}</h3>
                            <p class="text-gray-600 mb-2">{{ $event->description }}</p>
                            <p class="text-gray-600 mb-2"><span class="font-semibold">Start:</span> {{ $event->start_time }}</p>
                            <p class="text-gray-600 mb-2"><span class="font-semibold">End:</span> {{ $event->end_time }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Import Events from CSV Accordion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <button class="w-full text-left px-6 py-4 bg-yellow-500 hover:bg-yellow-600 focus:outline-none transition duration-150 ease-in-out flex items-center justify-between" onclick="toggleAccordion('importEvents')">
                    <h2 class="text-xl font-semibold text-white">Import Events from CSV</h2>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="importEvents" class="hidden px-6 py-4">
                    <div class="mb-4">
                        <a href="{{ route('events.csv.template') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                            Download CSV Template
                        </a>
                    </div>
                    <form action="{{ route('events.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="csv_file">CSV File</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="csv_file" type="file" name="csv_file" required>
                        </div>
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out" type="submit">
                            Import Events
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAccordion(id) {
        const content = document.getElementById(id);
        const button = content.previousElementSibling;
        const icon = button.querySelector('svg');

        content.classList.toggle('hidden');
        icon.classList.toggle('transform');
        icon.classList.toggle('rotate-180');
    }

    function toggleEditForm(eventId) {
        const editForm = document.getElementById(`editForm${eventId}`);
        editForm.classList.toggle('hidden');
    }
</script>
@endsection
