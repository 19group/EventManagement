@extends('Shared.Layouts.Master')

@section('title')
@parent

Event Orders
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class='ico-cart mr5'></i>
Side Events for {{$event->title}}
<span class="page_title_sub_title hide">
    <!--Showing 30 orders out of <b></b> Total-->
</span>
@stop

@section('head')

@stop

@section('page_header')
<!--div class="col-md-9 col-sm-6">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id="InviteAttendee" href="javascript:void(0);"  data-href="{{route('showReCreateSideEvent', array('event_id'=>$event->id))}}" class="loadModal btn" type="button"><i class="glyphicon glyphicon-tag"></i> Create Side Event</button>
        </div>
    </div>
</div-->
    <div class="col-md-3">
        {!! Form::open(array('url' => route('showPayments', ['event_id'=>$event->id]), 'method' => 'get')) !!}
        <div class="input-group">
            <input name="q" value="{{$search['q'] or ''}}" placeholder="Search Payments.." type="text" class="form-control set-shadow" >
        <span class="input-group-btn">
            <button class="btn btn-default set-shadow" type="submit"><i class="ico-search "></i></button>
        </span>
        </div>
        <input type="hidden" name='sort_by' value="{{$search['sort_by']}}"/>
        {!! Form::close() !!}
    </div>
@stop


@section('content')
<!--Start Attendees table-->
<div class="row">

    @if($payments)


    <div class="col-md-12">

        <!-- START  panel -->
        <div class="panel">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                               Payer's Full Name
                            </th>
                            <th>
                               Payer's Email
                            </th>
                            <th>
                               Transaction Id
                            </th>
                            <th>
                                Payment Date
                            </th>
                            <th>
                               Payment Status
                            </th>
                            <th>
                               Amount
                            </th>
                            <th>
                                Order Completion
                            </th>
                            <th>
                                Paypal Verification
                            </th>
                            <!--th>
                                Manage
                            </th-->
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($payments as $payment)
                        <tr>
                            <td>
                                {{$payment->full_name}}
                            </td>
                            <td>
                                {{$payment->payer_email}}
                            </td>
                            <td>
                                {{$payment->txn_id}}
                            </td>
                            <td>
                                {{$payment->payment_date}}
                            </td>
                            <td>
                                {{$payment->payment_status}}
                            </td>
                            <td>
                                {{$payment->amount}}
                            </td>
                            <td>
                                {{$payment->order_completed}}
                            </td>
                            <td>
                                {{$payment->paypal_verified}}
                            </td>
                            <!--td>
                <span style="cursor: pointer;" data-modal-id='ticket-{{ $payment->id }}'
                             data-href="{{ route('showEditSideEvent', ['event_id' => $event->id, 'ticket_id' => $payment->id]) }}"
                             class="panel-heading loadModal"><i class="ico-edit"></i>Edit </span>
                                <a href="{{route('postDeleteSideEvent', ['ticket_id' => $payment->id])}}" onClick="return confirm('Oh you really sure want to delete this SIDE event?');">
                    <i class="ico-remove"></i> Delete
                </a>
                            </td-->
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @else

    @endif
</div>    <!--/End attendees table-->
@stop
