<?php $offer=0; $toffers=[];?>
<script>
    $(function() {
        var offer = 0;
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

    });
</script>
<div role="dialog"  class="modal fade" style="display: none;">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Create Coupon</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-md-12">Generate Coupons</label>
                            <input id="max_coupons" class="form-control" type="number" name="" placeholder="Enter number of coupons to generate">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input id="discount" class="form-control" type="number" name="" placeholder="Enter number of coupons to generate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-info" onclick="generate_coupons()">
                            Generate Coupons
                        </button>
                    </div>
                    <div class="col-md-5">
                        <div class="col-md-12 form-group" id="tokens">
                        </div>
                    </div>
                </div>

            </div> <!-- /end modal body-->
                

                <div class="modal-footer">
                    <div class="col-md-6 col-md-offset-6">
                        <div class="col-md-6">
                             <button data-dismiss="modal" class="btn btn-danger modal-close ">
                                 Cancel
                            </button>
                        </div>
                        <div class="col-md-6">
                             <button class="btn btn-success">
                                Save
                            </button>
                        </div>
                    </div>
                </div>




  <!-- {!! Form::open(array('url' => route('postCreateTicket', array('event_id' => $event->id)), 'class' => 'ajax')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Create Coupon</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('coupons_number', 'Generate Coupon Code', array('class'=>'control-label required')) !!}
                            {!!  Form::number('title', Input::old('coupon_number'),
                                        array(
                                        'class'=>'form-control',
                                        'placeholder'=>'Enter number of Coupons to generate'
                                        ))  !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-info">
                            Generate Coupons
                        </button>
                    </div>
                    <div class="col-md-6 col-md-offset-4">
                         @for($i=1; $i<5; $i++)
                                <p>This here</p>
                            @endfor
                    </div>
                </div>



            </div> <!-- /end modal body-->
          <!--  <div class="modal-footer">
               {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Create Ticket', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content
       {!! Form::close() !!} -->
</div>

<script type="text/javascript">
    
    function generate_coupons(){

        var coupons_limit = document.getElementById('max_coupons').value;

        var token = [];

        if (max_coupons!='') {

            for (i = 0; i <coupons_limit; i++) { 
                    
                token[i] = (Math.random().toString(36).substring(2, 12));

                    document.getElementById('tokens').innerHTML += "<br><button class='btn btn-warning form-control' name='token"+ i +"'>"+ token[i] +"</button><br>" ;
                    //document.getElementById('tokens').innerHTML += "<input id='discount' class='form-control btn btn-info' disabled type='number' name= " + token[i] + " placeholder="+token[i]+">" ;



                    //(Math.random().toString(36).substring(2, 15)+"\n");


                }
        }
    }
</script>