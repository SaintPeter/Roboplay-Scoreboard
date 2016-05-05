@extends('layouts.scaffold')

@section('main')
    @include('partials.year_select')
    @if($year)
        <h1>T-shirts</h1>
        <ul>
            <li>{{ link_to_route('data_export.student_tshirts', 'Student T-Shirts (' . $year . ')', [ 'year' => $year ]) }}</li>
            <li>{{ link_to_route('data_export.teacher_tshirts', 'Teacher T-Shirts (' . $year . ')', [ 'year' => $year ]) }}</li>
        </ul>
        <h1>Competition Data</h1>
        <ul>
        	<li>{{ link_to_route('data_export.teacher_teams', 'Teacher/Team Data (' . $year . ')', [ 'year' => $year ]) }}</li>
        </ul>
    @else
        <h2>You must select a year.</h2>
    @endif
@stop