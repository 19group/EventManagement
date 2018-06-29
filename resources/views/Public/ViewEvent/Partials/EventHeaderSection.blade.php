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
    <div class="row ">
     <div class="col-sm-12">
          <div onclick="window.location='{{$event->event_url}}#organiser'" class="event_organizer">
                    <h2>{{$event->title}}</h2>
            </div>

            <div class="event_venue col-sm-12">
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
                </span></div><div class="col-sm-12">
                @
                <span property="location" typeof="Place">
                    <b property="name">{{$event->venue_name}}</b>
                    <meta property="address" content="{{ urldecode($event->venue_name) }}">
                </span>
            </div>
       </div>
    </div>
</section>

<section class="container" id="progress">
 <div class="row" style="color:black; min-height: 30px;">
     <?php if (session()->has('transaction_'.$event->id)){
         $step = session()->get('transaction_'.$event->id);
         $progress=['tickets','workshops','accommodation','tickets','payments','complete'];
         $colordispatcher = []; $taken=false;
         for($pass=0; $pass<count($progress);++$pass){
             if($progress[$pass]==$step){
                 $colordispatcher[]='#82CAFF'; $taken=true;
             }elseif(!$taken){
                 $colordispatcher[]='#3BB9FF';
             }else{
                 $colordispatcher[]='#ADDFFF';
             }
         }

     }else{
         $colordispatcher=['#82CAFF','#ADDFFF','#ADDFFF','#ADDFFF','#ADDFFF','#ADDFFF','#ADDFFF'];
     }
     ?>
     <div class="col-sm-2" style="background-color:{{$colordispatcher[0]}};padding:5px;text-align:center;"><strong>Registration</strong></div>
     <div class="col-sm-2" style="background-color:{{$colordispatcher[1]}};padding:5px;text-align:center;"><strong>Workshops</strong></div>
     <div class="col-sm-2" style="background-color:{{$colordispatcher[2]}};padding:5px;text-align:center;"><strong>Accommodations</strong></div>
     <div class="col-sm-2" style="background-color:{{$colordispatcher[3]}};padding:5px;text-align:center;"><strong>Tickets</strong></div>
     <div class="col-sm-2" style="background-color:{{$colordispatcher[4]}};padding:5px;text-align:center;"><strong>Payments</strong></div>
     <div class="col-sm-2" style="background-color:{{$colordispatcher[5]}};padding:5px;text-align:center;"><strong>Save Tickets</strong></div>
 </div>
</section>
