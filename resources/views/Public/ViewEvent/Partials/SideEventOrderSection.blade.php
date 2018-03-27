<div class="row bg-white">

 <section id="tickets" class="container" >

<div class="col-sm-12">
<h1 class='section_head'>
Add an experince at {{$event->title}}
</h1>
</div>

  <div class="col-md-4 pull-right"><br><br>
   <div class="panel">
    <div class="panel-heading">
     <h3 class="panel-title">
      <i class="ico-cart mr5"></i>
      Order Summary
     </h3>
    </div>

    <div class="panel-body pt0">
     <table class="table mb0 table-condensed">

      <?php $donhead='Donation Amount';if($donation>0){ ?>
       <tr>
        <td class="pl0"><b>Donation Amount:</b></td>
        <td style="text-align: right;">
         {{  money($donation, $event->currency) }}
        </td>
       </tr>
       <?php } ?>

       @php
       $i=0
       @endphp

       @foreach($tickets as $ticket)
       <tr>
        <td class="pl0">{{{$ticket['ticket']['title']}}} X <b>{{$ticket['qty']}}</b></td>
        <td style="text-align: right;">
         @if((int)ceil($ticket['full_price']) === 0)
         FREE
         @else


         @if(  $discount[$i]!='' and $discount_ticket_title[$i]==$ticket['ticket']['title'] )
         <strike>{{ money($ticket['full_price'], $event->currency) }}</strike>
         {{ money($ticket['full_price']-$ticket['full_price']*($discount[$i]/100), $event->currency) }}

         @php
         $i++
         @endphp

         @elseif(  $exact_amount[$i]!='' and $amount_ticket_title[$i]==$ticket['ticket']['title'] )
         <strike>{{ money($ticket['full_price'], $event->currency) }}</strike>
         {{ money($exact_amount[$i], $event->currency) }}

         @php
         $i++
         @endphp

         @elseif(  $exact_amount[$i]=='' and $discount[$i]=='' )
         {{ money($ticket['full_price'], $event->currency) }}
         @php
         $i++
         @endphp

         @endif

         @endif
        </td>
       </tr>
       @endforeach

      </table>
     </div>
     @if($order_total +$donation > 0)
     <div class="panel-footer">
      <h5>
       Subtotal: <span style="float: right;"><b>{{ money($order_total + $total_booking_fee  + $donation, $event->currency) }}</b></span>
      </h5>
     </div>
     @endif

    </div>

        <?php $tickets = $sideeventsar;?>
        @if(count($tickets) > 0)

            {!! Form::open(['url' => route('postOrderSideEvents', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
          <div class="col-md-4 pull-right">
          {!!Form::submit('Next', ['class' => 'btn btn-lg btn-primary pull-right'])!!}
          </div>
        @endif
   </div>






   <div class="col-md-8">
    <div class="col-md-12">

     <div class="content">


      <div class="row">

    @if($event->start_date->isPast())
        <div class="alert alert-boring">
            This event has {{($event->end_date->isFuture() ? 'already started' : 'ended')}}.
        </div>
    @else

        <?php $tickets = $sideeventsar;?>
        @if(count($tickets) > 0)

                          <div class="tickets_table_wrap">
                          <table class="table">
                               <?php foreach ($tickets as $minevent){?>
                                  <tr class="ticket" property="offers" typeof="Offer">
                                      <td>
                              <span class="ticket-title semibold" property="name">
                                  {{$minevent->title}}
                              </span>
                                          <p class="ticket-descripton mb0 side-event-description " property="description">
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
                                 </tr>
                               <?php }//end-foreach($sideevent as $minevent) ?>
                      <!---    </div>   -->
                    </table>
                    </div>
                                  <hr />
<hr />
            {!! Form::hidden('is_embedded', $is_embedded) !!}
            {!! Form::close() !!}

        @else

            <div class="alert alert-boring">
                Tickets are currently unavailable.
            </div>

        @endif
</div>
                  
                    </div> <!-- End Content -->

                </div>
            </div>
    @endif

</section>


</div>