@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-bold mb-6">Verify OTP</h1>
        <!-- Form to verify OTP -->
        <form action="{{ route('verify.otp') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <!-- Email input field, pre-filled with session email and readonly -->
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="{{ session('email') }}" required readonly>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="otp">
                    OTP
                </label>
                <!-- OTP input field -->
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="otp" type="text" name="otp" required>
            </div>
            <div class="flex items-center justify-between">
                <!-- Submit button to verify OTP -->
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Verify OTP
                </button>
            </div>
        </form>
    </div>
@endsection
