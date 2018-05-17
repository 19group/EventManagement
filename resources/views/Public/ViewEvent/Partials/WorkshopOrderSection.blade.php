<div class="row bg-white">

 <section id="tickets" class="container" >

<div class="col-sm-12">
<h1 class='section_head'>
Attend a Workshop at {{$event->title}}
</h1>
</div>

  <div class="col-sm-12 col-lg-4 pull-right col-event-order">

   @include('Public.ViewEvent.Partials.OrderSummary')

   <div class="">
         <a href="{{ route('completeOrderWorkshops', ['event_id'=> $event_id]) }}" class="btn btn-lg btn-primary pull-right">Next</a>
   </div>

   </div>

   <div class="col-sm-12 col-lg-8 col-event-details">

        @if($event->start_date->isPast())
            <div class="alert alert-boring">
                This event has {{($event->end_date->isFuture() ? 'already started' : 'ended')}}.
            </div>
        @else

        <?php $tickets = $sideeventsar;?>
              @if(count($tickets) > 0)

                          @foreach ($tickets as $minevent)
                          <div class="row side-event-container">
                           <div class="col-sm-12">
                            <div class="col-xs-8 no-left-padding">
                            <span class="ticket-title semibold" property="name">
                             {{$minevent->title}}
                            </span>
                           </div>
                           <div class="col-xs-4">
                             <!--<span class="side-event-days">3 days and 2 nights</span>-->
                           </div>
                            <br />
                           </div>
                           <div class="col-xs-12">
                            <div class="col-xs-4 side-event-image">
                              <?php if($minevent->ticket_main_photo){ ?>
                               <img height=180 width=150 src="{{asset($minevent->ticket_main_photo)}}" />
                              <?php }elseif($minevent->ticket_photos){
                                $assumedefault=explode(config('attendize.sideevent_photos_eximploders'),$minevent->ticket_photos)[0];?>
                               <img height=180 width=150 src="{{asset($assumedefault)}}" />
                              <?php }else{ ?>
                              <img src="{{asset('assets/images/default/trip.jpg')}}" />
                              <?php } ?>
                            </div>
                            <div class="col-xs-8">
                             <p class="ticket-descripton mb0 side-event-description " property="description">
                              {{$minevent->description}}
                             </p>
                            </div>
                           </div>
                           <div class="col-xs-12">
                            <div class="col-xs-8 no-left-padding">
                             <button  data-toggle="modal" data-target="#more_details_{{$minevent->id}}" class="btn btn-primary" style="width:150px"> More Details</button>
                            </div>
                            <div class="col-xs-2">
                             <span>{{money($minevent->price, $event->currency)}} </span>
                            </div>
                            <div class="col-xs-2">
                             <button data-toggle="modal" data-target="#{{$minevent->id}}" class="btn btn-primary">
                             <i class="ico-ticket"></i> Book
                             </button>
                            </div>
                           </div>
                          </div>

  @include('Public.ViewEvent.Partials.WorkshopBookModal')
  @include('Public.ViewEvent.Partials.WorkshopMoreDetailsModal')


                          @endforeach

            {!! Form::hidden('is_embedded', $is_embedded) !!}
            {!! Form::close() !!}

        @else

            <div class="alert alert-boring">
                Tickets are currently unavailable.
            </div>

        @endif
            </div>
    @endif


</section>


</div>
