@extends('Public.ViewEvent.Layouts.EventPage')

@section('head')

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

@stop

@section('content')

    @include('Public.ViewEvent.Partials.EventViewOrderSection')
    @include('Public.ViewEvent.Partials.EventFooterSection')

@stop
