@extends('Emails.Layouts.Master')

@section('message_content')
Hello {{$order->first_name}},<br><br>

Your invitation letter for the event <b>{{$order->event->title}}</b> is attached to this email.

<br><br>
Thank you
@stop