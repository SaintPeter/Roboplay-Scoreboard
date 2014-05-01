@extends('layouts.scaffold')

@section('main')
<H1>Error</H1>
{{ Breadcrumbs::render() }}
<p>{{ $message }}</p>
@stop