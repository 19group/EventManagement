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
    </table>


    <br><br>
    You can manage this order at: {{route('showEventOrders', ['event_id' => $order->event->id, 'q'=>$order->order_reference])}}
    <br><br>
</div>
<br><br>
Thank you
@stop
