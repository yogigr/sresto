<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ config('app.name') }} - @yield('title')</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body class="hold-transition skin-blue sidebar-mini
{{ \Route::currentRouteName() == 'order.create' ? 'sidebar-collapse' : 'fixed' }}">
	<div class="wrapper">
		@include('layouts.header')
		@include('layouts.sidebar')

		<div class="content-wrapper">
			@if(\Route::currentRouteName() != 'order.create')
			<section class="content-header">
				<h1>
					@hasSection('page-title')
						@yield('page-title')
					@else
						@yield('title')
					@endif
					<small>@yield('description')</small></h1>
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
					@yield('breadcrumb')
			  </ol>
			</section>
			@endif
			<section class="content" id="app">
				@yield('content')
			</section>
		</div>

		<footer class="main-footer">
			<strong>Copyright &copy; {{ date('Y') }}
				<a href="{{ url('dashboard') }}">{{ config('app.name') }}</a>.
			</strong> All rights reserved.
		</footer>
	</div>
	<script>
		window.Laravel = {!! json_encode([
			'apiToken' => Auth::user()->passport_token ?? null
		]) !!}
	</script>
	<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
