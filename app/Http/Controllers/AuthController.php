<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        try {
            return view('auth.register');
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }

    /**
     * Handle the registration request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Register the user
            $user = $this->authService->register($validatedData);

            // Redirect to OTP verification page with the user's email
            return redirect()->route('verify.otp')->with('email', $user->email);
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        try {
            return view('auth.login');
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }

    /**
     * Handle the login request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Attempt to log in the user
            if ($this->authService->login($credentials)) {
                // Redirect to the intended page
                return redirect()->intended('/events');
            }

            // Redirect back with an error message
            return back()->withErrors(['email' => 'Invalid credentials']);
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }

    /**
     * Show the OTP verification form.
     *
     * @return \Illuminate\View\View
     */
    public function showOtpForm()
    {
        try {
            return view('auth.verify-otp');
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }

    /**
     * Handle the OTP verification request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'email' => 'required|string|email',
                'otp' => 'required|string|size:6',
            ]);

            // Verify the OTP
            if ($this->authService->verifyOtp($validatedData['email'], $validatedData['otp'])) {
                // Redirect to the login page with a success message
                return redirect()->route('login')->with('success', 'Email verified successfully. Please login.');
            }

            // Redirect back with an error message
            return back()->withErrors(['otp' => 'Invalid OTP']);
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }

    /**
     * Log out the user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        try {
            auth()->logout();
            return redirect()->route('login');
        } catch (Exception $e) {
            Log::error('Error sending reminder email: ' . $e->getMessage(), ["Exception" => $e]);
        }
    }
}
