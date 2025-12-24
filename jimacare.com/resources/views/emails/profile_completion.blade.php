@component('mail::message')
	# Hello Admin

	{{$user->firstname.' '.$user->lastname}} just completed his/her profile.

	@component('mail::button', ['url' => route('dashboard.user.edit', ['user' => $user->id])])
		View Profile
	@endcomponent

	Thanks,<br>
	{{ config('app.name') }}
@endcomponent
