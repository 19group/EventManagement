<section id='order_form' class="row bg-white" style="margin-top: 5%">
    <div class="container"><br><br>
        <h1 class="section_head">
            Accomodation
        </h1>
    </div>

    <div class="container">

         <div class="col-md-4 col-md-push-8">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-cart mr5"></i>
                        Order Summary
                    </h3>
                </div>

                <div class="panel-body pt0">
                    <table class="table mb0 table-condensed">
                        @foreach($tickets as $ticket)
                        <tr>
                            <td class="pl0">{{{$ticket['ticket']['title']}}} X <b>{{$ticket['qty']}}</b></td>
                            <td style="text-align: right;">
                                @if((int)ceil($ticket['full_price']) === 0)
                                FREE
                                @else
                                {{ money($ticket['full_price'], $event->currency) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                @if($order_total+$donation > 0)
                <div class="panel-footer">
                    <h5>
                        Subtotal: <span style="float: right;"><b>{{ money($order_total + $total_booking_fee + $donation,$event->currency) }}</b></span>
                    </h5>
                </div>
                @endif

            </div>
            <div class="help-block">
                Please note you only have <span id='countdown'></span> to complete this transaction before your tickets are re-released.
            </div>
        </div>
         <div class="col-md-8 col-md-pull-4">
     

    </div>


</section>