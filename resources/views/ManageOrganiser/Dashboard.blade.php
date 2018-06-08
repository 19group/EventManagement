@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Dashboard
@stop

@section('top_nav')
    @include('ManageOrganiser.Partials.TopNav')
@stop
@section('page_title')
    {{ $organiser->name }} Dashboard
@stop

@section('menu')
    @include('ManageOrganiser.Partials.Sidebar')
@stop

@section('head')

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

    {!! HTML::script('https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places') !!}
    {!! HTML::script('vendor/geocomplete/jquery.geocomplete.min.js')!!}
    {!! HTML::script('vendor/moment/moment.js')!!}
    {!! HTML::script('vendor/fullcalendar/dist/fullcalendar.min.js')!!}
    {!! HTML::style('vendor/fullcalendar/dist/fullcalendar.css')!!}

    <script>
        $(function() {
           $('#calendar').fullCalendar({
               events: {!! $calendar_events !!},
            header: {
                left:   'prev,',
                center: 'title',
                right:  'next'
            },
            dayClick: function(date, jsEvent, view) {

               }
           });
        });
    </script>
@stop

@section('content')
<body>
 @php $ticketcount=0; $othercount=0; $ticketsales=0; $othersales=0; $totaldonation=0;@endphp
@foreach($organiser->events as $event)
 @foreach($event->tickets as $ticket)
    @php $discount = isset($event_discounts[$event->id][$ticket->id]) ?  $event_discounts[$event->id][$ticket->id] : 0; @endphp
    @if(!in_array($ticket->type, ['SIDEEVENT','extra','extras','WORKSHOP']))
        @php $ticketcount += $ticket->quantity_sold; $ticketsales+=($ticket->price*$ticket->quantity_sold); $ticketsales -= $discount; @endphp
    @else
        @php $othercount += $ticket->quantity_sold; $othersales+=($ticket->price*$ticket->quantity_sold); $othersales -= $discount; @endphp
    @endif
 @endforeach
 @foreach ($event->orders as $donorder)
     @php $orderwithdonation= \App\Models\OrderItem::where(['order_id'=>$donorder->id, 'title'=>'Donation'])->first(); @endphp
     @if(count($orderwithdonation) != 0)
         @php $totaldonation += $orderwithdonation->unit_price; @endphp
     @endif
 @endforeach
 @endforeach

    <div class="row">
        <div class="col-sm-4">
            <div class="stat-box">
                <h3>
                    {{$organiser->events->count()}}
                </h3>
            <span>
                Events
            </span>
            </div>
        </div>
        <div class="col-sm-4 ">
            <div class="stat-box">
                <h3>
                    {{$ticketcount + $othercount}}
                </h3>
            <span>
                Tickets Sold
            </span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <h3>
                    {{ money($ticketsales + $othersales + $totaldonation, $organiser->account->currency) }}
                </h3>
            <span>
                Sales Volume
            </span>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-8">

            <h4 style="margin-bottom: 25px;margin-top: 20px;">Event Calendar</h4>
                    <div class="stat-box" id="calendar"></div>


            <h4 style="margin-bottom: 25px;margin-top: 20px;">Upcoming Events</h4>
            @if($upcoming_events->count())
                @foreach($upcoming_events as $event)
                    @include('ManageOrganiser.Partials.EventPanel')
                @endforeach
            @else
                <div class="alert alert-success alert-lg">
                    You have no events coming up. <a href="#"
                                                     data-href="{{route('showCreateEvent', ['organiser_id' => $organiser->id])}}"
                                                     class=" loadModal">You can click here to create an event.</a>
                </div>
            @endif
        </div>
        <div class="col-md-4">
            <h4 style="margin-bottom: 25px;margin-top: 20px;">Recent Orders</h4>
              @if($organiser->orders->count())
            <ul class="list-group">
                    @foreach($organiser->orders()->orderBy('created_at', 'desc')->take(5)->get() as $order)
                        <li class="list-group-item stat-box">
                            <h6 class="ellipsis">
                                <a href="{{ route('showEventDashboard', ['event_id' => $order->event->id]) }}">
                                    {{ $order->event->title }}
                                </a>
                            </h6>
                            <p class="list-group-text">
                                <a href="{{ route('showEventOrders', ['event_id' => $order->event_id, 'q' => $order->order_reference]) }}">
                                    <b>#{{ $order->order_reference }}</b></a> -
                                <a href="{{ route('showEventAttendees', ['event_id'=>$order->event->id,'q'=>$order->order_reference]) }}">{{ $order->full_name }}</a>
                                registered {{ $order->attendees()->withTrashed()->count() }} ticket{{ $order->attendees()->withTrashed()->count()  > 1 ? 's' : '' }}.
                            </p>
                            <h6>
                                {{ $order->created_at->diffForHumans() }} &bull; <span
                                        style="color: green;">{{ $order->event->currency_symbol }}{{ $order->amount }}</span>
                            </h6>
                        </li>
                    @endforeach
                  @else
                            <div class="alert alert-success alert-lg">
                                Looks like there are no recent orders.
                            </div>
                @endif
            </ul>

        </div>
    </div>
</body>
@stop
