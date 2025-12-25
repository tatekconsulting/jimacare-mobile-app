# Laravel Mobile Authentication Setup

## Problem
Your Flutter app is trying to call `/api/v1/mobile/login` but this endpoint doesn't exist in your Laravel backend yet.

## Solution: Create Mobile Authentication Controller

### Step 1: Create the Controller

Create a new file: `app/Http/Controllers/Api/MobileAuthController.php`

Copy the code below into this file:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * Mobile Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Create token using Sanctum
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Add other user fields as needed
            ],
        ]);
    }

    /**
     * Mobile Registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string',
            'type' => 'nullable|string', // 'client' or 'carer'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'type' => $request->type ?? 'carer',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    /**
     * Get Current User
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Forgot Password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Use Laravel's password reset functionality
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => $status === Password::RESET_LINK_SENT 
                ? 'Password reset link sent to your email' 
                : 'Unable to send reset link',
        ]);
    }

    /**
     * Verify Phone with OTP
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        // Implement your OTP verification logic here
        // This depends on how you handle OTP in your system
        
        $user = $request->user();
        
        // Example: Check if OTP matches (you'll need to implement this based on your OTP system)
        // if ($user->otp === $request->otp && $user->otp_expires_at > now()) {
        //     $user->phone_verified_at = now();
        //     $user->save();
        //     
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Phone verified successfully',
        //     ]);
        // }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP',
        ], 400);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Implement your OTP resend logic here
        // This depends on how you handle OTP in your system

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
        ]);
    }
}
```

### Step 2: Add Routes to `routes/api.php`

Add these routes to your `routes/api.php` file:

```php
// Mobile Authentication Routes (Public - No Auth Required)
Route::prefix('v1/mobile')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\MobileAuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\MobileAuthController::class, 'register']);
    Route::post('/forgot-password', [\App\Http\Controllers\Api\MobileAuthController::class, 'forgotPassword']);
    Route::post('/verify-phone', [\App\Http\Controllers\Api\MobileAuthController::class, 'verifyPhone']);
    Route::post('/resend-otp', [\App\Http\Controllers\Api\MobileAuthController::class, 'resendOtp']);
});

// Protected Mobile Routes (Requires Authentication)
Route::prefix('v1/mobile')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', [\App\Http\Controllers\Api\MobileAuthController::class, 'user']);
    Route::post('/logout', [\App\Http\Controllers\Api\MobileAuthController::class, 'logout']);
});
```

### Step 3: Ensure Laravel Sanctum is Installed

If not already installed, run:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Step 4: Update User Model

Make sure your `app/Models/User.php` has the `HasApiTokens` trait:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ... rest of your code
}
```

### Step 5: Configure CORS

Update `config/cors.php` to allow requests from your mobile app:

```php
'allowed_origins' => ['*'], // Or specify your domain
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
'supports_credentials' => false,
```

### Step 6: Test the Endpoints

After adding the controller and routes, test with:

```bash
# Test login
curl -X POST https://jimacare.com/api/v1/mobile/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

## Important Notes

1. **OTP Verification**: The `verifyPhone` and `resendOtp` methods need to be implemented based on your existing OTP system. Check how OTP is handled in your `HomeController`.

2. **Password Reset**: The `forgotPassword` method uses Laravel's built-in password reset. Make sure you have the password reset functionality configured.

3. **User Fields**: Update the user data returned in login/register to include all fields your app needs.

4. **Security**: The current implementation is basic. You may want to add:
   - Rate limiting
   - Email verification
   - Account status checks
   - Additional validation

## After Setup

Once you've added these endpoints, your Flutter app should be able to login successfully!

Test it by:
1. Running your Flutter app
2. Entering valid credentials
3. Checking if login works

