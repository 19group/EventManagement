@extends('Emails.Layouts.Master')

@section('message_content')
Hello,<br><br>

You have received a new order for the event <b>{{$order->event->title}}</b>.<br><br>

@if(!$order->is_payment_received)
    <b>Please note: This order still requires payment.</b>
    <br><br>
@endif


Order Summary:
<br><br>
Order Reference: <b>{{$order->order_reference}}</b><br>
Order Name: <b>{{$order->full_name}}</b><br>
Order Date: <b>{{$order->created_at->toDayDateTimeString()}}</b><br>
Order Email: <b>{{$order->email}}</b><br>


<h3>Order Items</h3>
<div style="padding:10px; background: #F9F9F9; border: 1px solid #f1f1f1;">

    <table style="width:100%; margin:10px;">
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
        <!--added by DonaldFeb28-->
        <?php $total_amt_calc = 0; ?>
        <!--end of addition-->
        @foreach($order->orderItems as $order_item)
            <?php 
                if($order_item->title !== 'Donation'){ 
                    $tickets_count=0;
                    foreach ($order->attendees as $order_attendee) {
                        if($order_attendee->ticket->title == $order_item->title){
                            if(!$order_attendee->is_cancelled){
                                ++$tickets_count;
                            }
                        }
                    }
                    if($tickets_count == 0) { continue;}
                }else{$tickets_count = 1;}
            ?>
            <tr>
                <td>
                    {{$order_item->title}}
                </td>
                <td>
                    <!--{{$order_item->quantity}}--> {{$tickets_count}}
                </td>
                <td>
                    @if((int)ceil($order_item->unit_price) == 0)
                    FREE
                    @else
                   {{money($order_item->unit_price, $order->event->currency)}}
                    @endif

                </td>
                <td>
                    @if((int)ceil($order_item->unit_price) == 0)
                    -
                    @else
                    {{money($order_item->unit_booking_fee, $order->event->currency)}}
                    @endif

                </td>
                <td>
                    @if((int)ceil($order_item->unit_price) == 0)
                    FREE
                    @else
                    {{money(($order_item->unit_price + $order_item->unit_booking_fee) * ($tickets_count), $order->event->currency)}}
                    <!--added by DonaldFeb28-->
                    <?php $total_amt_calc += ($order_item->unit_price + $order_item->unit_booking_fee) * ($tickets_count); ?>
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
    </table>


    <br><br>
    You can manage this order at: {{route('showEventOrders', ['event_id' => $order->event->id, 'q'=>$order->order_reference])}}
    <br><br>
</div>
<br><br>
Thank you
@stop
