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
                               Ticket Price
                            </th>
                            <th>
                                Tickets Sold
                            </th>
                            <th>
                               Schedules
                            </th>
                            <th>
                               Tickets purchased
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
                                {{$sideevent->price}}
                            </td>
                            <td>
                                {{$sideevent->quantity_sold}}
                            </td>
                            <td>
                                <?php 
                                    $schedules =[];
                                    if(isset($sideevent->ticket_offers)){
                                        $toffers = explode('+++',$sideevent->ticket_offers);
                                        for($count=0;$count<count($toffers);++$count){
                                        $sched = explode('<==>',$toffers[$count]);
                                        $schedule = date('d-M-Y H:i', strtotime($sched[0]))." to ".date('d-M-Y H:i', strtotime($sched[1]));
                                        echo $schedule; echo '</br>.......</br>';
                                        $schedules[] = $schedule;
                                        }
                                    }else{
                                        echo 'No schedule set.';
                                    }
                                ?>
                                
                            </td>
                            <td>
                                <?php
                                    if(!empty($schedules)){
                                        foreach($schedules as $scheduler){
                                            $tempobj = \App\Models\Attendee::where(['period'=>$scheduler, 'ticket_id'=>$sideevent->id])->get();
                                            echo count($tempobj); echo '</br>......</br>';
                                        }
                                    }else{
                                        echo '0';
                                    }
                                 ?>
                                
                            </td>
                            <td>
                <span style="cursor: pointer;" data-modal-id='ticket-{{ $sideevent->id }}'
                             data-href="{{ route('showEditSideEvent', ['event_id' => $event->id, 'ticket_id' => $sideevent->id]) }}"
                             class="panel-heading loadModal"><i class="ico-edit"></i>Edit </span>
                                <a href="{{route('postDeleteSideEvent', ['ticket_id' => $sideevent->id])}}" onClick="return confirm('Oh you really sure want to delete this SIDE event?');">
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
