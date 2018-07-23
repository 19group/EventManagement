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
Workshops for {{$event->title}}
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
            <button data-modal-id="InviteAttendee" href="javascript:void(0);"  data-href="{{route('showCreateWorkshop', array('event_id'=>$event->id))}}" class="loadModal btn" type="button"><i class="glyphicon glyphicon-tag"></i> Create New Workshop</button>
        </div>
    </div>
</div>
@stop


@section('content')
<!--Start Attendees table-->
<div class="row">

    @if($workshops)


    <div class="col-md-12">

        <!-- START  panel -->
        <div class="panel">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Workshop Title
                            </th>
                            <th>
                                Workshop Description
                            </th>
                            <th>
                                Attendance Price
                            </th>
                            <th>
                                Tickets Sold
                            </th>
                            <th>
                                Sessions
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

                        @foreach($workshops as $workshop)
                        <tr>
                            <td>
                                {{$workshop->title}}
                            </td>
                            <td>
                                {{$workshop->description}}
                            </td>
                            <td>
                                {{$workshop->price}}
                            </td>
                            <td>
                                <?php 
                                    $attendschedules = [];
                                    if(isset($workshop->ticket_offers)){
                                        $toffers = explode('+++',$workshop->ticket_offers);
                                        for($count=0;$count<count($toffers);++$count){
                                        $sched = explode('<==>',$toffers[$count]);
                                        $schedule = date('d-M-Y H:i', strtotime($sched[0]))." to ".date('d-M-Y H:i', strtotime($sched[1]));
                                        $attendschedules[$schedule] = 0;
                                        }
                                    }
                                    $workcounts = \App\Models\Attendee::where(['ticket_id'=>$workshop->id, 'is_cancelled'=>0])->get();
                                    foreach($workcounts as $workattend){
                                        if(!$workattend->period){continue;}
                                        if(!array_key_exists($workattend->period, $attendschedules) && !array_key_exists('Cancelled '.$workattend->period, $attendschedules)){
                                            $attendschedules['Cancelled '.$workattend->period] = 1;
                                        }elseif(array_key_exists($workattend->period, $attendschedules)){
                                            $attendschedules[$workattend->period] += 1;
                                        }else{
                                            $attendschedules['Cancelled '.$workattend->period] += 1;
                                        }
                                    }
                                    echo count($workcounts);
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if($attendschedules){
                                        $toffers = array_keys($attendschedules);
                                        foreach($toffers as $schedule){
                                            if(substr($schedule,0,9)=='Cancelled'){
                                                echo '<span style="color:red">'.substr($schedule,0,9).' </span>'.substr($schedule,10);
                                            }else{
                                                echo $schedule; 
                                            }
                                        echo '</br>.......</br>';
                                        }
                                    }else{
                                        echo 'No session set.';
                                    }
                                ?>
                                
                            </td>
                            <td>
                                <?php
                                    if($attendschedules){
                                        foreach($attendschedules as $schedule){
                                        echo $schedule; echo '</br>.......</br>';
                                        }
                                    }else{
                                        echo '0';
                                    }
                                ?>
                                
                            </td>
                            <td>
                <span style="cursor: pointer;" data-modal-id='ticket-{{ $workshop->id }}'
                             data-href="{{ route('showEditWorkshop', ['event_id' => $event->id, 'ticket_id' => $workshop->id]) }}"
                             class="panel-heading loadModal"><i class="ico-edit"></i>Edit </span>
                                <a href="{{route('postDeleteWorkshop', ['ticket_id' => $workshop->id])}}" onClick="return confirm('Oh you really sure want to delete this Workshop?');">
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
