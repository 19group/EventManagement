<script>
    $(function() {
        var offerad = 1;
$('#add_offer').on('click', function(e) {
var p = document.createElement('div');
var d = document.createElement('div');
var r = document.createElement('span');
var f = document.createElement("INPUT");
f.setAttribute("type", "text");
f.setAttribute("class", "form-control");
f.setAttribute("placeholder", "Add another ticket offer...");
p.setAttribute("class", "col-md-12");
d.setAttribute("class", "form-group");
offerad+=1;
f.setAttribute("name", "ticket_offerad_" + offerad);
p.appendChild(d);
d.appendChild(f);
r.setAttribute("id", "offer_" + offerad);
document.getElementById("ticketoffers").appendChild(p);
        });

    });
</script>
<div role="dialog"  class="modal fade " style="display: none;">
    {!! Form::model($ticket, ['url' => route('postEditTicket', ['ticket_id' => $ticket->id, 'event_id' => $event->id]), 'class' => 'ajax']) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Edit Ticket: <em>{{$ticket->title}}</em></h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('title', 'Ticket Title', ['class'=>'control-label required']) !!}
                    {!!  Form::text('title', null,['class'=>'form-control', 'placeholder'=>'E.g: General Admission']) !!}
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('price', 'Ticket Price', ['class'=>'control-label required']) !!}
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


                <div class="form-group more-options">
                    {!! Form::label('description', 'Ticket Description', ['class'=>'control-label']) !!}
                    {!!  Form::text('description', null,['class'=>'form-control']) !!}
                </div>

                <!--added by Donald-->
                <?php if($ticket->ticket_offers!=NULL){
                    $toffers = explode('#@#',$ticket->ticket_offers);
                    $firstholder = $toffers[0];

                // }else{
                //    $firstholder = 'E.g: Ticket Holder will get a free drink at the entrance';
                //    } ?>
                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('ticket_offer', 'Ticket Offers', array('class'=>'control-label')) !!}
                            {!!  Form::text('ticket_offer_0', $firstholder,
                                        array(
                                        'class'=>'form-control',
                                        'name' =>'ticket_offer_0',
                                        'placeholder'=>$firstholder
                                        ))  !!}
                        </div>
                    </div>
                </div>
                <?php if(count($toffers)>1){
                    for($i=1;$i<count($toffers);++$i){
                ?>

                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!  Form::text('ticket_offer_'.$i, $toffers[$i],
                                        array(
                                        'class'=>'form-control',
                                        'name'=>'ticket_offer_'.$i,
                                        'placeholder'=>$toffers[$i]
                                        ))  !!}
                        </div>
                    </div>
                </div>
                <?php

                        }
                    }
                } else { ?>
                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('ticket_offer', 'Ticket Offers', array('class'=>'control-label')) !!}
                            {!!  Form::text('ticket_offerad_0', null,
                                        array(
                                        'class'=>'form-control',
                                        'name' =>'ticket_offerad_0',
                                        'placeholder' =>'E.g: Ticket Holder will get a free drink at the entrance'
                                        ))  !!}
                        </div>
                    </div>
                </div>
                <?php } ?>

                <div class = "row more options" id="ticketoffers">

                </div>

                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                        {!! Form::button('Add Another Offer', ['class'=>"btn btn-success", 'id'=>"add_offer"]) !!}
                        </div>
                    </div>
                </div>
                <!--end of addition-->



                <div class="row more-options">
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

                <div class="row more-options">
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                             {!! Form::label('type', 'Ticket Type', array('class'=>' control-label')) !!}
                             {!! Form::select('type', ['normal'=>'Normal', 'extra'=>'Extra'],null, ['class' => 'form-control']) !!}

                        </div>
                    </div>
                </div>
                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="custom-checkbox">
                                {!! Form::checkbox('is_hidden', null, null, ['id' => 'is_hidden']) !!}
                                {!! Form::label('is_hidden', 'Hide this ticket', array('class'=>' control-label')) !!}
                            </div>

                        </div>
                    </div>
                </div>

                <a href="javascript:void(0);" class="show-more-options">
                    More Options
                </a>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('Save Ticket', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>
