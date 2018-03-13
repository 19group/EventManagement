<div role="dialog"  class="modal fade " style="display: none;">
    {!! Form::model($ticket, ['url' => route('postEditSideEvent', ['ticket_id' => $ticket->id, 'event_id' => $event->id]), 'class' => 'ajax']) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Edit Side Event: <em>{{$ticket->title}}</em></h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('title', 'Side Event Title', ['class'=>'control-label required']) !!}
                    {!!  Form::text('title', null,['class'=>'form-control', 'placeholder'=>'E.g: General Admission']) !!}
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('price', 'Side Event\'s Ticket Price', ['class'=>'control-label required']) !!}
                            {!!  Form::text('price', null,['class' => 'form-control', 'placeholder' => 'E.g: 25.99']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('quantity_available', 'Quantity Available', ['class'=>' control-label']) !!}
                            {!!  Form::text('quantity_available', null, ['class' => 'form-control', 'placeholder' => 'E.g: 100 (Leave blank for unlimited)']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('description', 'Side Event Short Description', ['class'=>'control-label']) !!}
                    {!!  Form::text('description', null,['class'=>'form-control']) !!}
                </div>

                <!--added by Donald-->

                <div class="row">
                    <div class="col-sm-10">
                        <!--<div class="form-group">-->
                            {!! Form::label('', 'List Of Schedules for The Side Event', array('class'=>' control-label')) !!}
                        <!--</div>-->
                    </div>
                </div> 
                <?php if($ticket->ticket_offers!=NULL){
                    $toffers = explode('+++',$ticket->ticket_offers);
                    if(count($toffers)>0){
                    for($i=0;$i<count($toffers);++$i){
                        $sched = explode('<==>',$toffers[$i]);
                ?>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <!--{!! Form::label('start_sale_date', 'Start Date For First Schedule', array('class'=>' control-label')) !!}-->

                                {!!  Form::text('ogstart_schedule_'.$i, date('d-m-Y H:i', strtotime($sched[0])),
                                [
                                    'class' => 'form-control start hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'start',
                                    'data-startendelem' => '.end',
                                    'name'=>'ogstart_schedule_'.$i,
                                    'readonly' => '',
                                    'value'=>date('d-m-Y H:i', strtotime($sched[0])),
                                ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    <!--{!! Form::label('end_sale_date', 'End Date For First Schedule', ['class'=>' control-label ' ])  !!}-->
                                    
                                {!!  Form::text('ogend_schedule_'.$i, date('d-m-Y H:i', strtotime($sched[1])),
                                [
                                    'class' => 'form-control start hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'end',
                                    'data-startendelem' => '.start',
                                    'name'=>'ogend_schedule_'.$i,
                                    'readonly' => '',
                                    'value'=>date('d-m-Y H:i', strtotime($sched[1])),
                                ]) !!}
                                </div>
                            </div>
                        </div>
                <?php

                        }
                    } 
                } else { ?>   
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <!--{!! Form::label('start_sale_date', 'Start Date For First Schedule', array('class'=>' control-label')) !!}-->
                                    {!!  Form::text('start_schedule_0', Input::old('start_sale_date'),
                                                    [
                                                'class'=>'form-control start hasDatepicker ',
                                                'data-field'=>'datetime',
                                                'data-startend'=>'start',
                                                'data-startendelem'=>'.end',
                                                'readonly'=>'',
                                                'name'=>'start_schedule_0',
                                                'placeholder'=>'Start Date For First Schedule'

                                            ])  !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    <!--{!! Form::label('end_sale_date', 'End Date For First Schedule', ['class'=>' control-label ' ])  !!}-->
                                    {!!  Form::text('end_schedule_0', Input::old('end_sale_date'),
                                            [
                                        'class'=>'form-control end hasDatepicker ',
                                        'data-field'=>'datetime',
                                        'data-startend'=>'end',
                                        'data-startendelem'=>'.start',
                                        'readonly'=>'',
                                        'name'=>'end_schedule_0',
                                        'placeholder'=>'End Date For First Schedule'
                                    ])  !!}
                                </div>
                            </div>
                        </div>

                <?php } ?>

                        <div id="scheduledates">
                            
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                {!! Form::button('Add Another Schedule', ['class'=>"btn btn-success", 'id'=>"add_schedule"]) !!}
                                </div>
                            </div>
                        </div>
                <!--end of addition-->
            

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('start_sale_date', 'Start Sale On', ['class'=>' control-label']) !!}

                            {!!  Form::text('start_sale_date', $ticket->getFormattedDate('start_sale_date'),
                                [
                                    'class' => 'form-control start hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'start',
                                    'data-startendelem' => '.end',
                                    'readonly' => ''
                                ]) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!  Form::label('end_sale_date', 'End Sale On',
                                        [
                                    'class'=>' control-label '
                                ])  !!}
                            {!!  Form::text('end_sale_date', $ticket->getFormattedDate('end_sale_date'),
                                [
                                    'class' => 'form-control end hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'end',
                                    'data-startendelem' => '.start',
                                    'readonly' => ''
                                ])  !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('min_per_person', 'Minimum Tickets Per Order', ['class'=>' control-label']) !!}
                           {!! Form::selectRange('min_per_person', 1, 100, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('max_per_person', 'Maximum Tickets Per Order', ['class'=>' control-label']) !!}
                           {!! Form::selectRange('max_per_person', 1, 100, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('Save Side Event', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>




<script>
    

        
    var scheduler = 1;
    $('#add_schedule').on('click', function(e) {
        var p = document.createElement('div');
        var f = document.createElement("div");
        var d = document.createElement('div');
        var z = document.createElement('div');
        var y = document.createElement('div');
        var t = document.createElement('INPUT');
        var i = document.createElement('INPUT');
        p.setAttribute("class", "row");
        f.setAttribute("class", "col-sm-6");
        d.setAttribute("class", "col-sm-6");
        z.setAttribute("class", "form-group");
        y.setAttribute("class", "form-group");
        i.setAttribute("name", "start_schedule_" + scheduler);
        i.setAttribute("data-startend", "");
        i.setAttribute("data-startendelem", "");
        i.setAttribute("type", "text");
        i.setAttribute("class", "form-control start hasDatepicker");
        i.setAttribute("data-field","datetime");
        i.setAttribute("data-startend", "start");
        i.setAttribute("data-startendelem", ".end");
        i.setAttribute("readonly", "");
        i.setAttribute("placeholder", "Start Date For New Schedule " + scheduler);
        t.setAttribute("name", "end_schedule_" + scheduler);
        t.setAttribute("type", "text");
        t.setAttribute("class", "form-control end hasDatepicker ");
        t.setAttribute("data-field","datetime");
        t.setAttribute("data-startend", "end");
        t.setAttribute("data-startendelem", ".start");
        t.setAttribute("readonly", "");
        t.setAttribute("placeholder", "End Date For New Schedule " + scheduler);
        p.appendChild(d);
        p.appendChild(f);
        d.appendChild(y);
        f.appendChild(z);
        y.appendChild(i);
        z.appendChild(t);
        scheduler+=1;
        document.getElementById("scheduledates").appendChild(p);  
        i.setAttribute("data-startend", "");
        i.setAttribute("data-startendelem", "");
    //    t.setAttribute("data-startend", "");
    //    t.setAttribute("data-startendelem", "");
    });

</script>
