@extends('Shared.Layouts.Master')

@section('title')
@parent
Event Attendees
@stop

<style type="text/css">
    
    #snackbar {
                visibility: hidden;
                min-width: 150px;
                margin-left: -125px;
                background-color: #5fa9da;
                color: #fff;
                text-align: center;
                border-radius: 2px;
                padding: 10px;
                position: fixed;
                z-index: 1;
                left: 50%;
                bottom: 30px;
                font-size: 17px;
            }

            #snackbar.show {
                visibility: visible;
                -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
                animation: fadein 0.5s, fadeout 0.5s 2.5s;
            }

            @-webkit-keyframes fadein {
                from {bottom: 0; opacity: 0;} 
                to {bottom: 30px; opacity: 1;}
            }

            @keyframes fadein {
                from {bottom: 0; opacity: 0;}
                to {bottom: 30px; opacity: 1;}
            }

            @-webkit-keyframes fadeout {
                from {bottom: 30px; opacity: 1;} 
                to {bottom: 0; opacity: 0;}
            }

            @keyframes fadeout {
                from {bottom: 30px; opacity: 1;}
                to {bottom: 0; opacity: 0;}
            }

</style>


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
    @if(Utils::isSuperUser())
        <div class="btn-group btn-group-responsive">
            <button data-modal-id="InviteAttendee" href="javascript:void(0);"  data-href="{{route('showCreateCoupon', array('event_id'=>$event->id))}}" class="loadModal btn" type="button"><i class="glyphicon glyphicon-tag"></i> Generate Coupon</button>
        </div>
        <div class="btn-group btn-group-responsive">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                <i class="ico-users"></i> Export <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{route('showExportCoupons', ['event_id'=>$event->id,'export_as'=>'xlsx'])}}">Excel (XLSX)</a></li>
                <li><a href="{{route('showExportCoupons', ['event_id'=>$event->id,'export_as'=>'xls'])}}">Excel (XLS)</a></li>
                <li><a href="{{route('showExportCoupons', ['event_id'=>$event->id,'export_as'=>'csv'])}}">CSV</a></li>
                <li><a href="{{route('showExportCoupons', ['event_id'=>$event->id,'export_as'=>'html'])}}">HTML</a></li>
            </ul>
        </div>
    @endif
        
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
                               Associated Ticket
                            </th>
                            <th>
                               Discount
                            </th>
                            <th>
                               Exact Amount
                            </th>
                            <th>
                               State
                            </th>
                            <th>
                                Order Used
                           </th>
                            <th>
                                Manage
                           </th>
                        </tr>
                    </thead>
                    <div id="snackbar">Coupon token copied..</div>
                    <tbody>
                        @foreach($attendees as $coupon)
                        <tr>
                            <!--<td><input type="" class="btn btn-danger" style="width: 150px" value="{{$coupon->coupon_code}}" disabled /></td>-->
                            <td><button class="btn btn-danger" onClick="copy(this)" style="width: 110px;">{{$coupon->coupon_code}}</button></td>
                            <td>
                                 {{$coupon->ticket}}
                            </td>
                            <td>
                               <?php if($coupon->discount!='')
                                 
                                 echo($coupon->discount.'%');

                                 else 

                                    echo("-");

                                 ?>
                            </td>
                            <td>
                                <?php if($coupon->exact_amount!='')
                                 
                                 echo($coupon->exact_amount);

                                 else 

                                    echo("-");

                                 ?>
                            </td>
                             <td>
                                {{$coupon->state}}
                            </td>
                            <td>
                                {{$coupon->user}}
                            </td>
                            <td>
                                <span style="cursor: pointer;" data-modal-id='coupon-{{ $coupon->coupon_code }}'
                                             data-href="{{ route('showEditCoupon', ['event_id' => $event->id, 'coupon_id' => $coupon->id]) }}"
                                             class="panel-heading loadModal"><i class="ico-edit"></i>Edit </span>
                                                <a href="{{route('postDeleteCoupon', ['coupon_id' => $coupon->id])}}" onClick="return confirm('You are about to delete this coupon?');">
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
    <div class="col-md-12">
    </div>
</div>    <!--/End attendees table-->

<script type="text/javascript">

function copy(that){

    var inp =document.createElement('input');
    document.body.appendChild(inp)
    inp.value =that.textContent
    inp.select();
    document.execCommand('copy',false);
    inp.remove();
    //alert('Copied');

    var x = document.getElementById("snackbar")
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);


}




</script>

@stop


