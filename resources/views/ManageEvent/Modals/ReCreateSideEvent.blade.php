<?php $offer=0; $toffers=[];?>

<div role="dialog"  class="modal fade" style="display: none;">
   {!! Form::open(array('url' => route('postReCreateSideEvent', array('event_id' => $event->id)), 'class' => 'ajax')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Create Side-Event</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('title', 'Side Event Title', array('class'=>'control-label required')) !!}
                            {!!  Form::text('title', Input::old('title'),
                                        array(
                                        'class'=>'form-control',
                                        'placeholder'=>'E.g: Three Days Trip to Ngorongoro'
                                        ))  !!}
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('price', 'Ticket Price', array('class'=>'control-label required')) !!}
                                    {!!  Form::text('price', Input::old('price'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>'E.g: 25.99'
                                                ))  !!}


                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('quantity_available', 'Quantity Available', array('class'=>' control-label')) !!}
                                    {!!  Form::text('quantity_available', Input::old('quantity_available'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>'E.g: 100 (Leave blank for unlimited)'
                                                )
                                                )  !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('description', 'Side Event Short Description', array('class'=>'control-label')) !!}
                            {!!  Form::text('description', Input::old('description'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                        </div>

                <!--edited by DonaldMar12-->    
                        <div class="row">
                            <div class="col-sm-10">
                                <!--<div class="form-group">-->
                                    {!! Form::label('', 'List Of Schedules for The Side Event', array('class'=>' control-label')) !!}
                                <!--</div>-->
                            </div>
                        </div>    
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <!--{!! Form::label('start_sale_date', 'Start Date For First Schedule', array('class'=>' control-label')) !!}-->
                                    {!!  Form::text('start_sale_date', Input::old('start_sale_date'),
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
                                    {!!  Form::text('end_sale_date', Input::old('end_sale_date'),
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

                        <div id="scheduledates">
                            
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                {!! Form::button('Add Another Schedule', ['class'=>"btn btn-success", 'id'=>"add_schedule"]) !!}
                                </div>
                            </div>
                        </div>
                <!--end of addition by DonaldMar12-->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('min_per_person', 'Minimum Tickets Per Order', array('class'=>' control-label')) !!}
                                    {!! Form::selectRange('min_per_person', 1, 100, 1, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('max_per_person', 'Maximum Tickets Per Order', array('class'=>' control-label')) !!}
                                    {!! Form::selectRange('max_per_person', 1, 100, 30, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Create Side Event', ['class'=>"btn btn-success"]) !!}
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
        i.setAttribute("id", "start_sale_date");
        i.setAttribute("type", "text");
        i.setAttribute("class", "form-control start hasDatepicker");
        i.setAttribute("data-field","datetime");
        i.setAttribute("data-startend", "start");
        i.setAttribute("data-startendelem", ".end");
        i.setAttribute("readonly", "");
        i.setAttribute("placeholder", "Start Date For Schedule" + scheduler);
        t.setAttribute("name", "end_schedule_" + scheduler);
        t.setAttribute("id", "end_sale_date");
        t.setAttribute("type", "text");
        t.setAttribute("class", "form-control end hasDatepicker ");
        t.setAttribute("data-field","datetime");
        t.setAttribute("data-startend", "end");
        t.setAttribute("data-startendelem", ".start");
        t.setAttribute("readonly", "");
        t.setAttribute("placeholder", "End Date For Schedule " + scheduler);
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
