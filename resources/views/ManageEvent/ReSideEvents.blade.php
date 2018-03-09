<?php use App\Models\OrderItem; ?>
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
    Showing 30 orders out of <b></b> Total
</span>
@stop

@section('head')

@stop

@section('page_header')
<div class="col-md-9 col-sm-6">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id="InviteAttendee" href="javascript:void(0);"  data-href="{{route('showReCreateSideEvent', array('event_id'=>$event->id))}}" class="loadModal btn" type="button"><i class="glyphicon glyphicon-tag"></i> Create Side Event</button>
        </div>
    </div>
</div>
@stop


@section('content')
<!--Start Attendees table-->
<div class="row">

    @if($sideevents)


    <div class="col-md-12">

        <!-- START  panel -->
        <div class="panel">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                               SIDE Event Title
                            </th>
                            <th>
                               SIDE Event Description
                            </th>
                            <th>
                               Start Date
                            </th>
                            <th>
                               End Date
                            </th>
                            <th>
                               Ticket Price
                            </th>
                            <th>
                                Total Sold
                            </th>
                            <th>
                                Amount Collected
                            </th>
                            <th>
                                Manage
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($sideevents as $sideevent)
                        <tr>
                            <td>
                                {{$sideevent->title}}
                            </td>
                            <td>
                                {{$sideevent->description}}
                            </td>
                            <td>
                                {{$sideevent->start_date}}
                            </td>
                            <td>
                                {{$sideevent->end_date}}
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                {{$sideevent->price}}
                            </td>
                            <td>
                                <a href="{{route('postDeleteTicket', ['ticket_id' => $sideevent->id])}}" onClick="return confirm('Oh you really sure want to delete this SIDE event?');">
                    <i class="ico-remove"></i> Delete
                </a>
                            </td>
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
