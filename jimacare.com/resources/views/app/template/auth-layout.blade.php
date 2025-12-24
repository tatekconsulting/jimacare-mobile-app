<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
		<title>{{ config('app.name', 'Laravel') }}</title>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	</head>
	<body>
		@include('app.template.header')
		<main>
			@yield('content')
		</main>
		<script src="{{ asset('js/app.js') }}" defer></script>
	</body>
</html>
