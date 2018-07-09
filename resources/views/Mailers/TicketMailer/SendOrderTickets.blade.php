@extends('Emails.Layouts.Master')

@section('message_content')
Hello {{$order->full_name}},<br><br>

Welcome to {{$order->event->title}} in Dar es Salaam!<br><br>

We look forward to welcoming you to our city and for you to<br><br>

Your order for the event <b>{{$order->event->title}}</b> was successful.<br><br>

Your tickets are attached to this email. You can also view you order details and download your tickets at: {{route('showOrderDetails', ['order_reference' => $order->order_reference])}}<br><br>

@if(!$order->is_payment_received)
<br><br>
<b>Please note: This order still requires payment. Instructions on how to make payment can be found on your order page: {{route('showOrderDetails', ['order_reference' => $order->order_reference])}}</b>
<br><br>
@endif
<h3>Order Details</h3>
Order Reference: <b>{{$order->order_reference}}</b><br>
Order Name: <b>{{$order->full_name}}</b><br>
Order Date: <b>{{$order->created_at->toDayDateTimeString()}}</b><br>
Order Email: <b>{{$order->email}}</b><br>
<a href="{!! route('downloadCalendarIcs', ['event_id' => $order->event->id]) !!}">Add To Calendar</a>
<h3>Order Items</h3>
<div style="padding:10px; background: #F9F9F9; border: 1px solid #f1f1f1;">
    <table style="width:100%; margin:10px;">
        <tr>
            <td>
                <b>Ticket</b>
            </td>
            <td>
                <b>Qty.</b>
            </td>
            <td>
                <b>Price</b>
            </td>
            <td>
                <b>Fee</b>
            </td>
            <td>
                <b>Total</b>
            </td>
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
                {{money($total_amt_calc + $order->order_fee, $order->event->currency)}}
            </td>
        </tr>
    </table>

    <br><br>
</div>
<br><br>
If you have any questions regarding the event, please feel free to reply to this email.<br><br>

Thankyou, and We look forward to seeing you at the event,<br>
FOSS4G 2018 Dar es Salaam Local Organising Committee (DLOC)<br>

@stop
