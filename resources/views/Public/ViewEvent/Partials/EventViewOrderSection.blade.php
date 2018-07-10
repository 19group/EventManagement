<style>
    /*@todo This is temp - move to styles*/
    h3 {
        border: none !important;
        font-size: 30px;
        text-align: center;
        margin: 0;
        margin-bottom: 30px;
        letter-spacing: .2em;
        font-weight: 200;
    }

    .order_header {
        text-align: center
    }

    .order_header .massive-icon {
        display: block;
        width: 120px;
        height: 120px;
        font-size: 100px;
        margin: 0 auto;
        color: #63C05E;
    }

    .order_header h1 {
        margin-top: 20px;
        text-transform: uppercase;
    }

    .order_header h2 {
        margin-top: 5px;
        font-size: 20px;
    }

    .order_details.well, .offline_payment_instructions {
        margin-top: 25px;
        background-color: #FCFCFC;
        line-height: 30px;
        text-shadow: 0 1px 0 rgba(255,255,255,.9);
        color: #656565;
        overflow: hidden;
    }

    .ticket_download_link {
        border-bottom: 3px solid;
    }
</style>

<section id="order_form" class="row bg-white">
    <div class="container">
        <div class="col-md-12 order_header">
            <span class="massive-icon">
                <i class="ico ico-checkmark-circle"></i>
            </span>
            <h1>Thank you for your order!</h1>
            <h2>
                Your <a title="Download Tickets" class="ticket_download_link" href="{{route('showOrderTickets', ['order_reference' => $order->order_reference])}}?download=1">tickets</a> and a confirmation email have been sent to you.
            </h2>
        </div>
    </div>

    <div class="container">
        <div class="col-md-12">
            <div class="content event_view_order">

                @if($event->post_order_display_message)
                <div class="alert alert-dismissable alert-info">
                    {{ nl2br(e($event->post_order_display_message)) }}
                </div>
                @endif

                <div class="order_details well">
                    <div class="row">
                        <div class="col-sm-4 col-xs-6">
                            <b>First Name</b><br> {{$order->first_name}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>Last Name</b><br> {{$order->last_name}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <!--added by DonaldMar15-->
                            <?php 
                                $donatedamt=0;
                                foreach($order->orderItems as $ordereditem){
                                    if($ordereditem->title==='Donation'){
                                        $donatedamt += $ordereditem->unit_price;
                                    }
                                }
                            ?>
                            <b>Amount</b><br> {{$order->event->currency_symbol}}{{number_format($order->total_amount + $donatedamt,2)}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>Reference</b><br> {{$order->order_reference}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>Date</b><br> {{$order->created_at->toDateTimeString()}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>Email</b><br> {{$order->email}}
                        </div>
                    </div>
                </div>


                    @if(!$order->is_payment_received)
                        <h3>
                            Payment Instructions
                        </h3>
                    <div class="alert alert-info">
                        This order is awaiting payment. Please read the below instructions on how to make payment.
                    </div>
                    <div class="offline_payment_instructions well">
                        {!! Markdown::parse($event->offline_payment_instructions) !!}
                    </div>

                    @endif

                <h3>
                    Order Items
                </h3>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    Ticket
                                </th>
                                <th>
                                    Quantity
                                </th>
                                <th>
                                    Price
                                </th>
                                <th>
                                    Booking Fee
                                </th>
                                <th>
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <!--added by DonaldFeb28-->
                        <?php $total_amt_calc = 0; ?>
                        <!--end of addition-->
                        <?php 
                            $art_tickets = [];
                            foreach ($order->orderItems as $orderItem) {
                                if($orderItem->title=='Donation'){
                                        $art_tickets['donation'] = [
                                            'quantity' => 1,
                                            'total' => $orderItem->unit_price,
                                            'title' => $orderItem->title,
                                            'price' => $orderItem->unit_price,
                                            'booking_fee' => $orderItem->unit_booking_fee
                                        ];
                                }
                            }
                            foreach($order->attendees as $order_attendee) {
                                if(!$order_attendee->is_cancelled){
                                    if(array_key_exists($order_attendee->ticket->id, $art_tickets)){
                                       $art_tickets[$order_attendee->ticket->id]['quantity'] += 1;
                                       $art_tickets[$order_attendee->ticket->id]['total'] += $order_attendee->ticket->price;
                                    }else{
                                        $art_tickets[$order_attendee->ticket->id] = [
                                            'quantity' => 1,
                                            'total' => $order_attendee->ticket->price,
                                            'title' => $order_attendee->ticket->title,
                                            'price' => $order_attendee->ticket->price,
                                            'booking_fee' => $order_attendee->ticket->booking_fee
                                        ];
                                    }
                                }
                            }
                        ?>
                            @foreach($art_tickets as $order_item)
                                <tr>
                                    <td>
                                        {{$order_item['title']}}
                                    </td>
                                    <td>
                                        {{$order_item['quantity']}}
                                    </td>
                                    <td>
                                        @if($order_item['price'] == 0)
                                        FREE
                                        @else
                                       {{money($order_item['price'], $order->event->currency)}}
                                        @endif

                                    </td>
                                    <td>
                                        @if($order_item['price'] == 0)
                                        -
                                        @else
                                        {{money($order_item['booking_fee'], $order->event->currency)}}
                                        @endif

                                    </td>
                                    <td>
                                        @if($order_item['price'] == 0)
                                        FREE
                                        @else
                                        {{money(($order_item['price'] + $order_item['booking_fee']) * ($order_item['quantity']), $order->event->currency)}}
                                        <!--added by DonaldFeb28-->
                                        <?php $total_amt_calc += ($order_item['price'] + $order_item['booking_fee']) * ($order_item['quantity']); ?>
                                        <!--end of addition DonaldFeb28-->
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                    <b>Sub Total</b>
                                </td>
                                <td colspan="2">
                                <!--edited by DonaldFeb28 replacing $order->total_amount-->
                                {{money($total_amt_calc, $order->event->currency)}}
                                </td>
                            </tr>
                            @if($order->is_refunded || $order->is_partially_refunded)
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <b>Refunded Amount</b>
                                    </td>
                                    <td colspan="2">
                                        {{money($order->amount_refunded, $order->event->currency)}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <b>Total</b>
                                    </td>
                                    <td colspan="2">
                                        {{money($order->total_amount - $order->amount_refunded, $order->event->currency)}}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>

                <h3>
                    Order Attendees
                </h3>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <tbody>
                            @foreach($order->attendees as $attendee)
                            <tr>
                                <td>
                                    {{$attendee->first_name}}
                                    {{$attendee->last_name}}
                                    (<a href="mailto:{{$attendee->email}}">{{$attendee->email}}</a>)
                                </td>
                                <td>
                                    {{{$attendee->ticket->title}}}
                                </td>
                                <td>
                                    @if($attendee->is_cancelled)
                                        Cancelled
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</section>
