<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'firstname' => ['required', 'string', 'max:255'],
			'lastname' => ['required', 'string', 'max:255'],
			'role' => ['required', 'numeric', 'in:2,3,4,5'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
			'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);
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

	public function showRegistrationForm(){
		return redirect()->route('register.type', [ 'type' => 'client']);
	}


	public function registrationForm($type){
		$role = Role::where('slug', $type)->where('active', true)->firstOrFail();
		return view('app.pages.register', compact('role'));
	}
}
