@extends('Emails.Layouts.Master')

@section('message_content')
Hello {{$attendee->first_name}},<br><br>

Your invitation letter for the event <b>{{$attendee->order->event->title}}</b> is attached to this email.

<br><br>
Thank you
@stop