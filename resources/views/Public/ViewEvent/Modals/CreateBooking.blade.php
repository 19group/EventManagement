<?php $offer=0; $toffers=[];?>

<div role="dialog"  class="modal fade" id="CreateBooking" style="display: none;">
   {!! Form::open(array('url' => route('postCreateAccommodation', array('event_id' => $event->id)), 'class' => '')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="glyphicon glyphicon-star"></i>
                    Create an Accommodation</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('title', 'Title', array('class'=>'control-label required')) !!}
                            {!!  Form::text('title', Input::old('title'),
                                        array(
                                        'class'=>'form-control',
                                        'placeholder'=>'E.g: General Admission'
                                        ))  !!}
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('price', 'Price', array('class'=>'control-label required')) !!}
                                    {!!  Form::text('price', Input::old('price'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>'E.g: 25.99'
                                                ))  !!}


                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                     {!! Form::label('status', 'Status', array('class'=>' control-label')) !!}
                                     {!! Form::select('status', ['5'=>'5 Stars', '4'=>'4 Stars', '3'=>'3 Stars'],null, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                        </div>

<!-- Added by Donald on Jan 31 -->

                        <div class="row more-options">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('ticket_offer', 'Ticket Offers', array('class'=>'control-label')) !!}
                                    {!!  Form::text('ticket_offer_0', Input::old('ticket_offer'),
                                                array(
                                                'class'=>'form-control',
                                                'id' => "ticket_offer_0",
                                                'placeholder'=>'E.g: Ticket Holder will get a free drink at the entrance'
                                                ))  !!}
                                </div>
                            </div>
                        </div>
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

<!-- Added by Donald on March 9 -->

                        <div class="row more-options">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('ticket_extra', 'Ticket Extras', array('class'=>'control-label')) !!}
                                    {!!  Form::text('ticket_extra_0', Input::old('ticket_extra'),
                                                array(
                                                'class'=>'form-control',
                                                'id' => "ticket_extra_0",
                                                'placeholder'=>'Enter chargable extra service e.g extra nights at Seven Star Hotel @ 500 USD/night'
                                                ))  !!}
                                </div>
                            </div>
                        </div>

                        <div class="row more-options">
                            <div class="col-md-8">
                                <div class="form-group">
                                    {!!  Form::text('ticket_extra_options_0', Input::old('ticket_extra'),
                                                array(
                                                'class'=>'form-control',
                                                'id' => "ticket_extra_option_0",
                                                'placeholder'=>'Extra options separated by \'/\' e.g NightsBeforeEvent/NightsAfterEvent'
                                                ))  !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!!  Form::text('ticket_extra_amt_0', Input::old('ticket_extra'),
                                                array(
                                                'class'=>'form-control',
                                                'id' => "ticket_extra_amt_0",
                                                'placeholder'=>'Amount for extra'
                                                ))  !!}
                                </div>
                            </div>
                        </div>

                        <div class = "row more options" id="ticketextras">

                        </div>

                        <div class = "row more options" id="extrasinfos">

                        </div>
                        <div class="row more-options">
                            <div class="col-md-12">
                                <div class="form-group">
                                {!! Form::button('Another Extra', ['class'=>"btn btn-success", 'id'=>"add_extra"]) !!}
                                </div>
                            </div>
                        </div>
<!--end of addition-->


                        <div class="row more-options">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('start_sale_date', 'Start Sale On', array('class'=>' control-label')) !!}
                                    {!!  Form::text('start_sale_date', Input::old('start_sale_date'),
                                                    [
                                                'class'=>'form-control start hasDatepicker ',
                                                'data-field'=>'datetime',
                                                'data-startend'=>'start',
                                                'data-startendelem'=>'.end',
                                                'readonly'=>''

                                            ])  !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    {!!  Form::label('end_sale_date', 'End Sale On',
                                                [
                                            'class'=>' control-label '
                                        ])  !!}
                                    {!!  Form::text('end_sale_date', Input::old('end_sale_date'),
                                            [
                                        'class'=>'form-control end hasDatepicker ',
                                        'data-field'=>'datetime',
                                        'data-startend'=>'end',
                                        'data-startendelem'=>'.start',
                                        'readonly'=>''
                                    ])  !!}
                                </div>
                            </div>
                        </div>

                        <div class="row more-options">
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
                        <div class="row more-options">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-checkbox">
                                        {!! Form::checkbox('is_hidden', 1, false, ['id' => 'is_hidden']) !!}
                                        {!! Form::label('is_hidden', 'Hide this ticket', array('class'=>' control-label')) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                     {!! Form::label('type', 'Ticket Type', array('class'=>' control-label')) !!}
                                     {!! Form::select('type', ['extra'=>'Extras'],null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <a href="javascript:void(0);" class="show-more-options">
                            More Options
                        </a>
                    </div>

                </div>

            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Create Ticket', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>


<script>
$(function() {

    var offer = 0; var extra=0;
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
        offer+=1;
        f.setAttribute("name", "ticket_offer_" + offer);
        p.appendChild(d);
        d.appendChild(f);
        r.setAttribute("id", "offer_" + offer);
        document.getElementById("ticketoffers").appendChild(p);
    });

    $('#add_extra').on('click', function(e) {
        extra+=1;
        var m = document.createElement('div');
        var k = document.createElement('div');
        var s = document.createElement('span');
        var i = document.createElement("INPUT");
        i.setAttribute("type", "text");
        i.setAttribute("class", "form-control");
        i.setAttribute("placeholder", "Add extra..." + extra);
        m.setAttribute("class", "col-md-12");
        k.setAttribute("class", "form-group");
        i.setAttribute("name", "ticket_extra_" + extra);
        m.appendChild(k);
        k.appendChild(i);
        s.setAttribute("id", "extra_" + extra);
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
        ja.setAttribute("placeholder", "Extra " + extra + "'s Options separated by Forward Slash '/'");
        je.setAttribute("placeholder", "Extra " + extra + "'s Amount...");
        na.setAttribute("class", "col-md-8");
        ne.setAttribute("class", "col-md-4");
        la.setAttribute("class", "form-group");
        le.setAttribute("class", "form-group");
        ja.setAttribute("name", "ticket_extra_option_" + extra);
        je.setAttribute("name", "ticket_extra_amt_" + extra);
        na.appendChild(la);
        ne.appendChild(le);
        la.appendChild(ja);
        le.appendChild(je);
        ja.setAttribute("id", "ticket_extra_option_" + extra);
        je.setAttribute("id", "ticket_extra_amt_" + extra);
        document.getElementById("ticketextras").appendChild(na);
        document.getElementById("ticketextras").appendChild(ne);


    });



});
</script>
