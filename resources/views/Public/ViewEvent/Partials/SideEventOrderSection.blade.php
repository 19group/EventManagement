<div class="row bg-white" ">

<section id="tickets" class="container" ">



    @if($event->start_date->isPast())
        <div class="alert alert-boring">
            This event has {{($event->end_date->isFuture() ? 'already started' : 'ended')}}.
        </div>
    @else

        <?php $tickets = $sideeventsar;?>
        @if(count($tickets) > 0)

            {!! Form::open(['url' => route('postOrderSideEvents', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="content">

                        <!--Tickets and Side Events Details -->
                        <div class="row">

                         <div class="col-sm-12">
                             <h1 class='section_head'>
                                Side Events for {{$event->title}}
                             </h1>
                         </div>

                         <hr/>

                <div class="p20 pl0">
                    <a style="height:35px; float: right;" href="{{route('showEventCheckout', array('event_id'=>$event->id))}}" class="btn btn-primary btn-xs">
                        No, Just Take Me To The Next Page
                    </a>
                </div>

                <hr/>

                          <!--Side event container -->
                    <!---      <div class="col-md-5 side-events">
                             <h4> SIDE EVENTS</h4>   -->
                          <div class="tickets_table_wrap">
                          <table class="table">
                               <?php foreach ($tickets as $minevent){?>
                                  <tr class="ticket" property="offers" typeof="Offer">
                                      <td>
                              <span class="ticket-title semibold" property="name">
                                  {{$minevent->title}}
                              </span>
                                          <p class="ticket-descripton mb0 " property="description">
                                              {{$minevent->description}}
                                          </p>
                                <!--   <p><b>{{$minevent->title}} </b></p>
                                   <p>{{$minevent->description}} </br></p>  -->
                                   <?php if($minevent->ticket_offers!=NULL){
                                            $toffers = explode('+++',$minevent->ticket_offers);
                                        if(count($toffers)==1){
                                          echo "<p><b> Available schedule for this side event is </b></p>";
                                                $sched = explode('<==>',$toffers[0]);
                                                echo "<div class=\"row\">";
                                                echo date('d-M-Y H:i', strtotime($sched[0]))." to ".date('d-M-Y H:i', strtotime($sched[1]));
                                                echo "</div>";
                                        }else{
                                   echo "<p><b> Available schedules for this side event:- </b></p>";
                                            for($i=0;$i<count($toffers);++$i){
                                                $sched = explode('<==>',$toffers[$i]);
                                                echo '<div class="row"><ul>';
                                                echo'<li>'.date('d-M-Y H:i', strtotime($sched[0])).' to '.date('d-M-Y H:i', strtotime($sched[1])).'</li>';
                                                echo '</ul></div>';
                                            } ?>
                                      <!--DonaldMar14    </?php  for($i=0;$i<count($toffers);++$i){
                                                $sched = explode('<==>',$toffers[$i]);
                                                $checkbox = ' From '.date('d-M-Y H:i', strtotime($sched[0])).' To '.date('d-M-Y H:i', strtotime($sched[1])); ?>
                                      <div class="row">
                                      {//{ Form::checkbox($minevent->id."selscheds[]",$checkbox) }//} {//{ $checkbox}//}
                                      </div> end of comment by DonaldMar14-->
                                           <?php   }?>
                                  <?php }//end-if-minevent->ticket_offers ?>
                                </td>
                                <td style="width:180px; text-align: right;">
                                    <div class="ticket-pricing" style="margin-right: 20px;">
                                            <span title='{{money($minevent->price, $event->currency)}} Ticket Price + {{money($minevent->total_booking_fee, $event->currency)}} Booking Fees'>{{money($minevent->price, $event->currency)}} </span>
                                            <meta property="priceCurrency"
                                                  content="{{ $event->currency->code }}">
                                            <meta property="price"
                                                  content="{{ number_format($minevent->price, 2, '.', '') }}">
                                    </div>
                                </td>
                                <td style="width:85px;">
                                         {!! Form::hidden('tickets[]', $minevent->id) !!}
                                        <meta property="availability" content="http://schema.org/InStock">
                                        <select name="ticket_{{$minevent->id}}" class="form-control"
                                                 style="text-align: center">
                                             @if ($minevent->count() > 1)
                                                 <option value="0">0</option>
                                             @endif
                                             @for($i=$minevent->min_per_person; $i<=$minevent->max_per_person; $i++)
                                                 <option value="{{$i}}">{{$i}}</option>
                                             @endfor
                                        </select>
                                </td>
                      <!--             <div class="row">
                                         <div class="col-sm-6">
                                          <b class="col-sm-6" left>Price:</b>
                                           <span class="col-sm-6" title='{{money($minevent->price, $event->currency)}} Ticket Price + {{money($minevent->total_booking_fee, $event->currency)}} Booking Fees'>{{money($minevent->price, $event->currency)}} </span>
                                         </div>
                                         <div class="col-sm-6">
                                                 {!! Form::hidden('tickets[]', $minevent->id) !!}
                                                <meta property="availability" content="http://schema.org/InStock">
                                                <select name="ticket_{{$minevent->id}}" class="form-control"
                                                         style="text-align: center">
                                                     @if ($minevent->count() > 1)
                                                         <option value="0">0</option>
                                                     @endif
                                                     @for($i=$minevent->min_per_person; $i<=$minevent->max_per_person; $i++)
                                                         <option value="{{$i}}">{{$i}}</option>
                                                     @endfor
                                                </select>
                                         </div>
                                   </div>    -->
                               <?php }//end-foreach($sideevent as $minevent) ?>
                      <!---    </div>   -->
                    </table>
                    </div>
                          <hr />
                          {!!Form::submit('Register', ['class' => 'btn btn-lg btn-primary pull-right'])!!}
                        </div>

                    </div> <!-- End Content -->

                </div>
            </div>
            {!! Form::hidden('is_embedded', $is_embedded) !!}
            {!! Form::close() !!}

        @else

            <div class="alert alert-boring">
                Tickets are currently unavailable.
            </div>

        @endif

    @endif

</section>
</div>