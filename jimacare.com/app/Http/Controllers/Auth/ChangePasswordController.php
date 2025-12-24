<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->middleware('auth');
        $this->twilioService = $twilioService;
    }

    /**
     * Show the password change form
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        
        // Only allow access if user must change password
        if (!$user->must_change_password) {
            return redirect()->route('home');
        }

        return view('app.pages.change-password');
    }

    /**
     * Handle password change
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Only allow access if user must change password
        if (!$user->must_change_password) {
            return redirect()->route('home');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Update password
        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        // Check if phone needs verification
        if (!$user->phone_verified_at) {
            // Send OTP for phone verification
            if ($user->phone) {
                $result = $this->twilioService->sendVerificationCode($user->phone);
                
                if ($result['success']) {
                    return redirect()->route('verification.phone')->with([
                        'type' => 'success',
                        'notice' => 'Password changed successfully! Please verify your phone number to continue.'
                    ]);
                } else {
                    return redirect()->route('verification.phone')->with([
                        'type' => 'warning',
                        'notice' => 'Password changed successfully! However, we could not send the verification code. ' . ($result['message'] ?? 'Please try again later.')
                    ]);
                }
            } else {
                return redirect()->route('profile')->with([
                    'type' => 'warning',
                    'notice' => 'Password changed successfully! Please add and verify your phone number in your profile.'
                ]);
            }
        }

        return redirect()->route('home')->with([
            'type' => 'success',
            'notice' => 'Password changed successfully!'
        ]);
    }
}

