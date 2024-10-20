<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Reminder</title>
    <!-- Including Tailwind CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans leading-normal text-gray-800 max-w-lg mx-auto p-5">
    <div class="bg-gray-100 rounded-lg p-5 shadow-md">
        <h1 class="text-blue-500 text-center mb-5 text-2xl">Event Reminder</h1>
        <p class="text-center mb-8 text-lg">This is a reminder for your upcoming event:</p>
        <div class="bg-white rounded-lg p-5 mb-5">
            <h2 class="text-gray-800 text-center mb-5 text-xl">{{ $event->title }}</h2>
            <p class="mb-3"><strong class="text-blue-500">Start Time:</strong> {{ $event->start_time }}</p>
            <p class="mb-3"><strong class="text-blue-500">End Time:</strong> {{ $event->end_time }}</p>
            <p class="mb-3"><strong class="text-blue-500">Description:</strong> {{ $event->description }}</p>
        </div>
        <div class="text-center mt-8">
            <a href="{{ url('/events') }}" class="bg-blue-500 text-white no-underline py-2 px-4 rounded-lg font-bold">View Event Details</a>
        </div>
    </div>
    <p class="text-center mt-5 text-xs text-gray-500">This is an automated reminder. Please do not reply to this email.</p>
</body>
</html>
