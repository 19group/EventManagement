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
<<<<<<< HEAD
                    if(count($toffers)>0){
                    for($i=0;$i<count($toffers);++$i){
=======
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
>>>>>>> ade17bd0d991b90aa00060aa67f22427af3e9446
                ?>

                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php if($i==0){ ?> {!! Form::label('ticket_offer', 'Ticket Offers', array('class'=>'control-label')) !!} <?php } ?>
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

<<<<<<< HEAD
                <!--added by DonaldMar12-->
                <?php if($ticket->ticket_extras){
                    $textras = explode('{+}',$ticket->ticket_extras);
                    if(count($textras)>0){
                    for($i=0;$i<count($textras);++$i){
                ?>

                <div class="row more-options">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php if($i==0){ ?> {!! Form::label('ticket_extra', 'Ticket Extras', array('class'=>'control-label')) !!} <?php } ?>
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
                                    {!! Form::label('ticket_extras', 'Ticket Extras', array('class'=>'control-label')) !!}
                                    {!!  Form::text('ticket_extraad_0', Input::old('ticket_extra'),
                                                array(
                                                'class'=>'form-control',
                                                'name' => "ticket_extraad_0",
                                                'placeholder'=>'Enter chargable extra service e.g extra nights at Seven Star Hotel @ 500 USD/night'
                                                ))  !!}
                                </div>
                            </div>
                        </div> 
   
                        <div class="row more-options">
                            <div class="col-md-8">
                                <div class="form-group">
                                    {!!  Form::text('ticket_extra_option_0', Input::old('ticket_extra'),
                                                array(
                                                'class'=>'form-control',
                                                'name' => "ticket_extraad_option_0",
                                                'placeholder'=>'Extra options separated by \'/\' e.g NightsBeforeEvent/NightsAfterEvent'
                                                ))  !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!!  Form::text('ticket_extra_amt_0', Input::old('ticket_extra'),
                                                array(
                                                'class'=>'form-control',
                                                'name' => "ticket_extraad_amt_0",
                                                'placeholder'=>'Amount for extra'
                                                ))  !!}
                                </div>
                            </div>
                        </div>
                <?php } ?> 

                        <div class = "row more options" id="ticketextras">
                            
                        </div>

                        <div class="row more-options">
                            <div class="col-md-12">
                                <div class="form-group">
                                {!! Form::button('Another Extra', ['class'=>"btn btn-success", 'id'=>"add_extra"]) !!}
                                </div>
                            </div>
                        </div>
                <!--end of addition by DonaldMar12-->


            
=======

>>>>>>> ade17bd0d991b90aa00060aa67f22427af3e9446

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
                             {!! Form::select('type', ['normal'=>'Normal', 'extras'=>'Extras'],null, ['class' => 'form-control']) !!}

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

<script>
$(function() {

    var offerad = 1;  var extraad=0;

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

    $('#add_extra').on('click', function(e) {
        extraad+=1;
        var m = document.createElement('div');
        var k = document.createElement('div');
        var s = document.createElement('span');
        var i = document.createElement("INPUT");
        i.setAttribute("type", "text");
        i.setAttribute("class", "form-control");
        i.setAttribute("placeholder", "Add extra..." + extraad);
        m.setAttribute("class", "col-md-12");
        k.setAttribute("class", "form-group");
        i.setAttribute("name", "ticket_extraad_" + extraad);
        m.appendChild(k);
        k.appendChild(i);
        s.setAttribute("id", "extra_" + extraad); 
        document.getElementById("ticketextras").appendChild(m);


        var na = document.createElement('div');  
        var ne = document.createElement('div');
        var la = document.createElement('div');
        var le = document.createElement('div');
        var ta = document.createElement('span');
        var te = document.createElement('span');
        var ja = document.createElement("INPUT");
        var je = document.createElement("INPUT");
        ja.setAttribute("type", "text");
        je.setAttribute("type", "number");
        ja.setAttribute("class", "form-control");
        je.setAttribute("class", "form-control");
        ja.setAttribute("placeholder", "New extra " + extraad + "'s Options separated by Forward Slash '/'");
        je.setAttribute("placeholder", "New extra " + extraad + "'s Amount...");
        na.setAttribute("class", "col-md-8");
        ne.setAttribute("class", "col-md-4");
        la.setAttribute("class", "form-group");
        le.setAttribute("class", "form-group");
        ja.setAttribute("name", "ticket_extraad_option_" + extraad);
        je.setAttribute("name", "ticket_extraad_amt_" + extraad);
        na.appendChild(la);
        ne.appendChild(le);
        la.appendChild(ja);
        le.appendChild(je);
        ta.setAttribute("id", "extra_option_" + extraad); 
        te.setAttribute("id", "extra_amt_" + extraad); 
        document.getElementById("ticketextras").appendChild(na);
        document.getElementById("ticketextras").appendChild(ne);


    });


});
</script>
