<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\TwilioService;
use \Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Twilio\Rest\Client;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $twilioService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TwilioService $twilioService)
    {
        $this->middleware('guest');
        $this->twilioService = $twilioService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validation = Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'role' => ['required', 'numeric', 'in:2,3,4,5'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => [
                'required', 
                'string', 
                'max:255', 
                'unique:users,phone',
                function ($attribute, $value, $fail) {
                    // Remove all non-numeric characters except +
                    $cleaned = preg_replace('/[^0-9+]/', '', $value);
                    
                    // Check if it's a UK number
                    $isUK = false;
                    if (substr($cleaned, 0, 3) === '+44') {
                        $isUK = true;
                    } elseif (substr($cleaned, 0, 1) === '0') {
                        $isUK = true; // UK number starting with 0
                    } elseif (substr($cleaned, 0, 2) === '44') {
                        $isUK = true; // UK number with 44 but no +
                    } elseif (strlen($cleaned) >= 10 && strlen($cleaned) <= 11 && substr($cleaned, 0, 1) !== '+') {
                        $isUK = true; // UK number without country code
                    }
                    
                    if (!$isUK && substr($cleaned, 0, 1) === '+') {
                        $fail('Only UK phone numbers are accepted. Please use a UK number (e.g., +44 7700 900000 or 07700 900000).');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Validate phone number using Twilio Lookup (if configured)
        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');

            if ($sid && $token) {
                $twilio = new Client($sid, $token);
                $phone_number = $twilio->lookups->v1->phoneNumbers($data['phone'])
                    ->fetch(["type" => ["carrier"]]);

                if (!is_null($phone_number->carrier['error_code'])) {
                    throw ValidationException::withMessages(['phone' => "{$data['phone']} is invalid phone number."]);
                }
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $exception) {
            // Log error but don't block registration if lookup fails
            \Log::warning('Phone validation failed: ' . $exception->getMessage());
        }

        return $validation;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'role_id'   => $data['role'] ?? 2,
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'phone'     => $data['phone'],
            'password'  => Hash::make($data['password']),
            'status'    => 'pending'
        ]);
    }

    public function showRegistrationForm()
    {
        return redirect()->route('register.type', ['type' => 'client']);
    }

    public function registrationForm($type)
    {
        $role = Role::where('slug', $type)->where('active', true)->firstOrFail();
        return view('app.pages.register', compact('role'));
    }

    public function registered(Request $request, $user)
    {
        // Send verification SMS using secure TwilioService
        $result = $this->twilioService->sendVerificationCode($user->phone);
        
        if (!$result['success']) {
            // Log error but don't block registration
            \Log::warning('Failed to send OTP during registration: ' . $result['message'], [
                'user_id' => $user->id,
                'phone' => $user->phone
            ]);
        }

        return redirect()->route('verification.phone');
    }
}
