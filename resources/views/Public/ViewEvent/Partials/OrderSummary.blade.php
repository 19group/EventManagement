<div class="panel">
  <!--//////////////////////////working for past order in case of adding items for past order///-->
  @if(isset($past_order_id))
   <div class="panel-heading">
    <h3 class="panel-title">
     <i class="ico-cart mr5"></i>
     Past Order Summary
    </h3>
   </div>

   <div class="panel-body pt0">
    <table class="table mb0 table-condensed">
    @if(isset($past_donation))
      @if($past_donation > 0)
      <tr>
       <td class="pl0">Donation:</td>
       <td style="text-align: right;">
        {{  money($past_donation, $event->currency) }}
       </td>
      </tr>
      @endif
    @endif

    @if(isset($past_tickets))
      @if(count($past_tickets)>0)
        @foreach($past_tickets as $past_ticket)
          <?php 
             // if($order_item->title !== 'Donation'){ 
                  $order_object = App\Models\Order::where(['id'=>$past_order_id])->first();
                  $tickets_count=0; 
                  foreach ($order_object->attendees as $order_attendee) {
                      if($order_attendee->ticket->title == $past_ticket['ticket_title']){
                          if(!$order_attendee->is_cancelled){
                              ++$tickets_count;
                          }
                      }
                  }
                  if($tickets_count == 0) { continue;} 
              //}else{$tickets_count = 1;}
          ?>
        <tr>
         <td class="pl0">{{{$past_ticket['ticket_title']}}} X <b>{{$tickets_count}}</b></td>
         <td style="text-align: right;">
          @if((int)ceil($past_ticket['full_price']) === 0)
          FREE
          @else
          {{ money($past_ticket['full_price'], $event->currency) }}
          @endif
         </td>
        </tr>
        @endforeach
      @endif
        <tr>
         <td class="pl0"><b>Past Order Total</b></td>
         <td style="text-align: right;">
          {{ money($past_order_amount + $past_donation, $event->currency) }}
         </td>
        </tr>

     </table>
    </div>
   <div class="panel-heading">
    <h3 class="panel-title">
     <i class="ico-cart mr5"></i>
     New Order Summary
    </h3>
   </div>

   <div class="panel-body pt0">
    <table class="table mb0 table-condensed">
    @endif
  @else
   <div class="panel-heading">
    <h3 class="panel-title">
     <i class="ico-cart mr5"></i>
     Order Summary
    </h3>
   </div>

   <div class="panel-body pt0">
    <table class="table mb0 table-condensed">
  @endif
  <!--////////////////////////end of working for past order/////////////////////////////////////-->

      @if($donation > 0)
      <tr>
       <td class="pl0">Donation Amount:</td>
       <td style="text-align: right;">
        {{  money($donation, $event->currency) }}
       </td>
      </tr>
      @endif

      @php
      $i=0
      @endphp

    @if(count($tickets)>0)
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
        {{ money($ticket['full_price'] * $ticket['qty'], $event->currency) }}
        @php
        $i++
        @endphp

        @endif

        @endif
       </td>
       <td><a href="{{url()->current()}}/removeOrderTicket/{{$ticket['ticket']['id']}}" onclick="return confirm('Oh you really sure want to delete this ticket?');"><i class="ico-trash mr5"></i></a></td>

      </tr>
      @endforeach
    @endif

     </table>
    </div>
    @if($order_total + $donation > 0)
    <div class="panel-footer">
     <h5>
      Subtotal: <span style="float: right;"><b>{{ money($order_total + $total_booking_fee  + $donation, $event->currency) }}</b></span>
     </h5>
    </div>
    @elseif(count($tickets)==0)
    <div class="panel-footer">
     <h5>
      Your current order list is empty.
     </h5>
    </div>
    @endif

</div>

<div class="help-block">
    Please note you only have <span id='countdown'></span> to complete this transaction before your tickets are re-released.
</div>
