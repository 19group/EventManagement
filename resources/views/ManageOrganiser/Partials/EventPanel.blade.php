@php $ticketcount=0; $othercount=0; $ticketsales=0; $othersales=0; $totaldonation=0;@endphp
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
<div class="panel panel-success event rounded">
    <div class="panel-heading" data-style="background-color: {{{$event->bg_color}}};background-image: url({{{$event->bg_image_url}}}); background-size: cover;">
        <div class="event-date">
            <div class="month">
                {{strtoupper($event->start_date->format('M'))}}
            </div>
            <div class="day">
                {{$event->start_date->format('d')}}
            </div>
        </div>
        <ul class="event-meta">
            <li class="event-title">
                <a title="{{{$event->title}}}" href="{{route('showEventDashboard', ['event_id'=>$event->id])}}">
                    {{{ str_limit($event->title, $limit = 75, $end = '...') }}}
                </a>
            </li>
            <li class="event-organiser">
                By <a href='{{route('showOrganiserDashboard', ['organiser_id' => $event->organiser->id])}}'>{{{$event->organiser->name}}}</a>
            </li>
        </ul>

    </div>

    <div class="panel-body">
        <ul class="nav nav-section nav-justified mt5 mb5">
            <li>
                <div class="section">
                    <h4 class="nm">{{$event->tickets->sum('quantity_sold')}}</h4>
                    <p class="nm text-muted">All Tickets Sold</p>
                </div>
            </li>

            <li>
                <div class="section">
                    <!--h4 class="nm">{{{money($event->sales_volume + $event->organiser_fees_volume, $event->currency)}}}</h4-->
                    <h4 class="nm">{{{money($ticketsales + $othersales + $totaldonation, $event->currency)}}}</h4>
                    <p class="nm text-muted">Revenue</p>
                </div>
            </li>
        </ul>
    </div>
    <div class="panel-footer">
        <ul class="nav nav-section nav-justified">
            <li>
                <a href="{{route('showEventCustomize', ['event_id' => $event->id])}}">
                    <i class="ico-edit"></i> Edit
                </a>
            </li>

            <!--added by DonaldFeb21-->
            <li>
                <a href="{{route('postDeleteEvent', ['event_id' => $event->id])}}" onClick="return confirm('Oh you really sure want to delete this event?');">
                    <i class="ico-remove"></i> Delete
                </a>
            </li>
            <!--end of addition-->

            <li>
                <a href="{{route('showEventDashboard', ['event_id' => $event->id])}}">
                    <i class="ico-cog"></i> Manage
                </a>
            </li>
        </ul>
    </div>
</div>
