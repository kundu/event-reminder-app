<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    /**
     * Register a new user and send OTP email.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data)
    {
        // Create a new user with hashed password
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Generate OTP and save it to the user
        $otp = $this->generateOtp();
        $user->otp = $otp;
        $user->save();

        // Send OTP email to the user
        $this->sendOtpEmail($user->email, $otp);

        return $user;
    }

    /**
     * Attempt to log in a user with given credentials.
     *
     * @param array $credentials
     * @return \App\Models\User|null
     */
    public function login(array $credentials)
    {
        if (auth()->attempt($credentials)) {
            return auth()->user();
        }
        return null;
    }

    /**
     * Verify the OTP for a given email.
     *
     * @param string $email
     * @param string $otp
     * @return bool
     */
    public function verifyOtp($email, $otp)
    {
        $user = User::where('email', $email)->first();

        if ($user && $user->otp === $otp) {
            // Mark email as verified and clear the OTP
            $user->email_verified_at = now();
            $user->otp = null;
            $user->save();
            return true;
        }

        return false;
    }

    /**
     * Generate a one-time password (OTP).
     *
     * @return int
     */
    private function generateOtp()
    {
        if (app()->environment('local')) {
            return 123456;
        }
        return rand(100000, 999999);
    }

    /**
     * Send an OTP email to the given email address.
     *
     * @param string $email
     * @param int $otp
     * @return void
     */
    private function sendOtpEmail($email, $otp)
    {
        Mail::to($email)->send(new OtpMail($otp));
    }
}
