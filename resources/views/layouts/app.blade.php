<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Reminder App</title>
    <!-- Including Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <!-- Navigation link to events index -->
                        <a href="{{ route('events.index') }}" class="flex items-center py-4 px-2">
                            <span class="font-semibold text-gray-500 text-lg">Event Reminder</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <!-- Logout link for authenticated users -->
                        <a href="{{ route('logout') }}" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-gray-100 hover:text-gray-900 transition duration-300">Logout</a>
                    @else
                        <!-- Login and Sign Up links for guests -->
                        <a href="{{ route('login') }}" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-gray-100 hover:text-gray-900 transition duration-300">Login</a>
                        <a href="{{ route('register') }}" class="py-2 px-2 font-medium text-white bg-green-500 rounded hover:bg-green-400 transition duration-300">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-6 px-4">
        @if (session('success'))
            <!-- Success message alert -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <!-- Error message alert -->
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <!-- Validation error messages -->
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Validation Error!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Yield content from other views -->
        @yield('content')
    </div>
</body>
</html>
