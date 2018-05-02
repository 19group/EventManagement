<div class="panel">
   <div class="panel-heading">
    <h3 class="panel-title">
     <i class="ico-cart mr5"></i>
     Order Summary
    </h3>
   </div>

   <div class="panel-body pt0">
    <table class="table mb0 table-condensed">

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

     </table>
    </div>
    @if($order_total + $donation > 0)
    <div class="panel-footer">
     <h5>
      Subtotal: <span style="float: right;"><b>{{ money($order_total + $total_booking_fee  + $donation, $event->currency) }}</b></span>
     </h5>
    </div>
    @elseif(!(count($order_has_validdiscount)>0 && ($order_total + $donation == 0)))
    <div class="panel-footer">
     <h5>
      Your order list is empty. Please go back to ticket purchase and add some items to proceed.
     </h5>
    </div>
    @endif

</div>
