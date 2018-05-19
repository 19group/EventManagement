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

    <div class="">

        @if($event->start_date->isPast())
            <div class="alert alert-boring">
                This event has {{($event->end_date->isFuture() ? 'already started' : 'ended')}}.
            </div>
        @else

        <?php $tickets = $sideeventsar;?>
              @if(count($tickets) > 0)

              <div class="col-md-12 workshop-day">

              </div>
                          @foreach ($tickets as $minevent)
                          <div class="workshop-event-container col-sm-12 col-md-6 col-lg-6">
                           <div class="col-xs-12 workshop-image-container">
                            <div class="col-xs-2 side-event-image">
                              <?php if($minevent->ticket_main_photo){ ?>
                               <img height=70 src="{{asset($minevent->ticket_main_photo)}}" />
                              <?php }else{ ?>
                              <img height=70 src="{{asset('assets/images/default/trip.jpg')}}" />
                              <?php } ?>
                            </div>
                            <div class="col-xs-10">
                             <p class="ticket-descripton mb0 side-event-description " property="description">
                             <!-- {{$minevent->description}}-->
                             </p>
                            </div>
                           </div>
                           <div class="col-sm-12 workshop-content">
                            <div class="col-xs-12 no-left-padding">
                            <span class="ticket-title semibold" property="name">
                             {{$minevent->title}}
                            </span>
                           </div>
                           <div class="col-xs-12">

                           <?php if($minevent->ticket_offers!=NULL){
                                    $ticket_offers = explode('+++',$minevent->ticket_offers);

                                    for($i=0;$i<count($ticket_offers);++$i){
                                        $sched = explode('<==>',$ticket_offers[$i]);
                                        $count = $i+1;
                                        echo '<div class="row">';
                                        echo '<p>';
                                        echo'<b>'.date('l, d-M-Y', strtotime($sched[0])).'</b>';
                                        echo '</p>';
                                        echo '</div>';
                                    } ?>
                          <?php } ?>

                           </div>
                            <br />
                           </div>

                           <div class="col-xs-12 workshop-footer">
                            <!--

                            <div class="col-xs-2 no-left-padding">
                             <!--button  data-toggle="modal" data-target="#more_details_{{$minevent->id}}" class="btn btn-primary" style="width:150px"> More Details</button>
                            </div>
                            -->
                            <div class="col-xs-6">
                             <span>{{money($minevent->price, $event->currency)}} </span>
                            </div>
                            <div class="col-xs-6">
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

</div>
