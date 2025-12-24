<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="user-token" content="{{ auth()->id() ?? '0' }}">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
		<title>{{ config('app.name', 'Laravel') }}</title>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDjDE1KwC-5bMjKT760oi6JcHTAatqPIvw&libraries=places&ver=1.0"></script>
	</head>
	<body>
	@include('app.template.header')
	<main>
		@yield('content')
	</main>
	@include('app.template.footer')

	@if(session()->has('notice'))
		<div class='alert alert-{{session('type')??'success'}} alert-notice alert-dismissible fade show' role='alert'>
			{{ session('notice') }}
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</div>
	@endif
	<script src="{{ asset('js/app.js') }}"></script>

	@stack('scripts')
	</body>
</html>
