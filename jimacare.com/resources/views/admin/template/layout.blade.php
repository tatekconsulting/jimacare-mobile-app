<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
		<title>Dashboard</title>
		<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAemE-RYiok4C3WvIOuLLo3nhmMNaffl6s&libraries=places&ver=1.0"></script>
	</head>
	<body id="page-top">

		<div id="wrapper">
			@include('admin.template.sidebar')
			<div id="content-wrapper" class="d-flex flex-column">
				<div id="content">
					@include('admin.template.header')
					<div class="container-fluid">
						@yield('content')
					</div>
				</div>
				@include('admin.template.footer')
			</div>
		</div>

		@if(session()->has('notice'))
			<div class='alert alert-success alert-notice alert-dismissible fade show' role='alert'>
				{{ session('notice') }}
				<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
			</div>
		@endif

		@if ($errors->any())
			<div class="alert alert-danger alert-notice alert-dismissible fade show" role='alert'>
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<!-- Scroll to Top Button-->
		<a class="scroll-to-top rounded" href="#page-top">
			<i class="fa fa-angle-up"></i>
		</a>

		<script src="{{ asset('js/admin.js') }}"></script>
		@stack('scripts')
	</body>
</html>
