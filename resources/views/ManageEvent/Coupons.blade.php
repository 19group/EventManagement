@extends('Shared.Layouts.Master')

@section('title')
@parent
Event Attendees
@stop


@section('page_title')
<i class="ico-users"></i>
Coupons
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop


@section('head')

@stop


@section('page_header')
<div class="col-md-9">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id="InviteAttendee" href="javascript:void(0);"  data-href="{{route('showCreateCoupon', array('event_id'=>$event->id))}}" class="loadModal btn" type="button"><i class="glyphicon glyphicon-tag"></i> Generate Coupon</button>
        </div>
        
        <!--<div class="btn-group btn-group-responsive">
            <button data-modal-id="ImportAttendees" href="javascript:void(0);"  data-href="{{route('showImportAttendee', ['event_id'=>$event->id])}}" class="loadModal btn btn-success" type="button"><i class="ico-file"></i> Invite Attendees</button>
        </div>
        
        <div class="btn-group btn-group-responsive">
            <a class="btn btn-success" href="{{route('showPrintAttendees', ['event_id'=>$event->id])}}" target="_blank" ><i class="ico-print"></i> Print Attendee List</a>
        </div>
        <div class="btn-group btn-group-responsive">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                <i class="ico-users"></i> Export <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{route('showExportAttendees', ['event_id'=>$event->id,'export_as'=>'xlsx'])}}">Excel (XLSX)</a></li>
                <li><a href="{{route('showExportAttendees', ['event_id'=>$event->id,'export_as'=>'xls'])}}">Excel (XLS)</a></li>
                <li><a href="{{route('showExportAttendees', ['event_id'=>$event->id,'export_as'=>'csv'])}}">CSV</a></li>
                <li><a href="{{route('showExportAttendees', ['event_id'=>$event->id,'export_as'=>'html'])}}">HTML</a></li>
            </ul>
        </div>
        <div class="btn-group btn-group-responsive">
            <button data-modal-id="MessageAttendees" href="javascript:void(0);" data-href="{{route('showMessageAttendees', ['event_id'=>$event->id])}}" class="loadModal btn btn-success" type="button"><i class="ico-envelope"></i> Message</button>
        </div>-->
    </div>
</div>
<!--<div class="col-md-3">
    <div class="input-group">
        <input name="q" value="{{$q or ''}}" placeholder="Search Attendees.." type="text" class="form-control" />
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
    </div>
</div>-->
@stop


@section('content')

<!--Start Attendees table-->
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                               Coupon code
                            </th>
                            <th>
                               Discount
                            </th>
                            <th>
                               State
                            </th>
                            <th>
                                Used by
                           </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendees as $attendee)
                        <tr>
                            <td><button class="btn btn-danger" style="width: 150px">{{$attendee->coupon_code}}</button></td>
                            <td>
                                 {{$attendee->discount}} %
                            </td>
                            <td>
                                {{$attendee->state}}
                            </td>
                            <td>
                                {{$attendee->user}}
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        

    </div>
    <div class="col-md-12">
    </div>
</div>    <!--/End attendees table-->

@stop


