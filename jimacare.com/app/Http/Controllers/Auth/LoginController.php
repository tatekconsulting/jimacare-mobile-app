<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /**
     * Maximum number of login attempts per minute.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * Number of minutes to lock the user out after max attempts.
     *
     * @var int
     */
    protected $decayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // Add rate limiting to login attempts
        $this->middleware('throttle:login')->only('login');
    }

	public function showLoginForm()
	{
		return view('app.pages.login');
	}

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if user must change password (using temporary password)
        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        // Role-based redirect after login
        return $this->redirectBasedOnRole($request, $user);
    }

    /**
     * Redirect user based on their role after login
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole(Request $request, $user)
    {
        $intended = $request->session()->pull('url.intended');
        
        // If there's an intended URL, use it
        if ($intended) {
            return redirect($intended);
        }

        // Role-based redirects
        $userRole = $user->role->slug ?? '';
        
        switch ($userRole) {
            case 'admin':
                return redirect()->route('dashboard');
            case 'client':
                return redirect()->route('contract.index');
            case 'carer':
            case 'childminder':
            case 'housekeeper':
                return redirect()->route('contract.index');
            default:
                return redirect()->intended($this->redirectPath());
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Check if user exists and account status
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            // Check account status
            if ($user->status === 'block') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Your account has been blocked. Please contact support.'],
                ]);
            }
            
            if ($user->status === 'pending') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Your account is pending approval. Please wait for admin approval or contact support.'],
                ]);
            }
            
            if ($user->status === 'review') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Your account is under review. Please wait for admin approval or contact support.'],
                ]);
            }
        }
        
        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // First, check if user exists and account is active
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            // Prevent login for blocked accounts
            if ($user->status === 'block') {
                return false;
            }
            
            // For service providers (carers, childminders, housekeepers), check if approved
            $userRole = $user->role->slug ?? '';
            if (in_array($userRole, ['carer', 'childminder', 'housekeeper'])) {
                // Allow login even if not approved, but we'll handle messaging in sendFailedLoginResponse
                // This allows users to see their account status
            }
        }
        
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to home page after logout to avoid CSRF issues
        return $this->loggedOut($request) ?: redirect('/');
    }
}
