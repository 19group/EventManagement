@if(!$event->is_live)
<section id="goLiveBar">
    <div class="container">
                @if(!$event->is_live)
                This event is not visible to the public - <a style="background-color: green; border-color: green;" class="btn btn-success btn-xs" href="{{route('MakeEventLive' , ['event_id' => $event->id])}}" >Publish Event</a>
                @endif
    </div>
</section>
@endif

<section id="intro" class="container-fluid">
    <div class="row">
        <div class="col-md-4 pull-left position-fixed">
            <br><br>

            <div onclick="window.location='{{$event->event_url}}#organiser'" class="event_organizer">
                    <b>{{$event->organiser->name}}</b> Presents
                </div>

            <h1 property="name">{{$event->title}}</h1>
            <div class="event_venue">
                <span property="startDate" content="{{ $event->start_date->toIso8601String() }}">
                    {{ $event->start_date->format('D d M H:i A') }}
                </span>
                -
                <span property="endDate" content="{{ $event->end_date->toIso8601String() }}">
                     @if($event->start_date->diffInHours($event->end_date) <= 12)
                        {{ $event->end_date->format('H:i A') }}
                     @else
                        {{ $event->end_date->format('D d M H:i A') }}
                     @endif
                </span><br>
                @
                <span property="location" typeof="Place">
                    <b property="name">{{$event->venue_name}}</b>
                    <meta property="address" content="{{ urldecode($event->venue_name) }}">
                </span>
            </div>

            
        </div>
    </div>
</section>
