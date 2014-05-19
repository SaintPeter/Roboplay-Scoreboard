<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>@if(isset($title)){{ $title . ' | ' }}RoboPlay Scoreboard @endif</title>
		<link rel="icon" type="image/ico" href="http://cstem.ucdavis.edu/scoreboard/favicon.ico"/>
		{{ HTML::style('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css') }}
		{{ HTML::style('//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css') }}
		{{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') }}
        {{ HTML::script('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js') }}

        @yield('head')

		<style>
			table form { margin-bottom: 0; }
			form>ul { margin-left: 0; list-style: none; }
			.error { color: red; font-style: italic; }
			label { display: block; }
			body { padding-top: 20px; padding-bottom: 50px;}
			.btn-margin {
				margin: 2px;
			}
			::-webkit-scrollbar {
			    -webkit-appearance: none;
			    width: 7px;
			}
			::-webkit-scrollbar-thumb {
			    border-radius: 4px;
			    background-color: rgba(0,0,0,.5);
			    box-shadow: 0 0 1px rgba(255,255,255,.5);
			}
			@yield('style')
		</style>

		<script>@yield('script')</script>
	</head>

	<body>

		<div class="container">
			@yield('before_header')

			@if(isset($title) and !isset($skip_title))
				<h1>{{ $title }}</h1>
			@endif
			{{ Breadcrumbs::render() }}

@section('message')
			@if (Session::has('message'))
				<div class="flash alert">
					<p>{{ Session::get('message') }}</p>
				</div>
			@endif
@show

			@yield('main')
		</div>

	</body>

</html>