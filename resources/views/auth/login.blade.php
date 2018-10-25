<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name') }} - Login</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body class="hold-transition login-page">
	<div class="login-box" id="app">
		<div class="login-logo">
			<a href="#">{{ config('app.name') }}</a>
		</div>
		<div class="login-box-body">
			<p class="login-box-msg">Sign in to start your session</p>
			<login-form></login-form>
		</div>
	</div>
	<script>
		window.Laravel = {!! json_encode([
			'apiToken' => Auth::user()->passport_token ?? null
		]) !!}
	</script>
	<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
