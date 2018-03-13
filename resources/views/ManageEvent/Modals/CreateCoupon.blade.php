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
            <form action="tickets/postCreateCoupons" method="post">
                {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-12">Number of Coupons</label>
                            <input id="max_coupons" class="form-control" type="number" name="max_coupons" placeholder="Enter number of coupons to generate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-12">Associated Ticket</label> 
                              <div class="form-group">
                                  <select class="form-control" name="title">
                                    @foreach($ticks as $item)
                                      <option value="{{$item->title}}">{{$item->title}}</option>
                                    @endforeach
                                  </select>
                            </div>
                
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-md-12">Enter Coupon Mode (Exact Amount OR Percentage Discount)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-12">Coupon's Exact Amount</label>
                            <input id="exact_amt" class="form-control" type="number" onkeyup="validate()" name="exact_amt" placeholder="Enter number of coupons to generate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-12">Coupon's Percentage Discount</label>
                            <input id="perc_discount" class="form-control" type="number" onkeyup="validate()" name="discount" placeholder="Enter % discount">
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
                             <button class="btn btn-success" type="submit" value="submit">
                                Generate
                            </button>
                        </div>
                    </div>
                </div>
                <!--</form>-->




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
    
    function validate(){

        var exact_amountt = document.getElementById('exact_amt').value;
        var percentage_discount = document.getElementById('perc_discount').value;

        if (exact_amountt!='') {

           document.getElementById("perc_discount").disabled = true;
        }
        else if (percentage_discount!='') {
              
           document.getElementById("exact_amt").disabled = true;

            if(percentage_discount < 0){

                perc_discount.value = '0';
            }
            else if (percentage_discount > 100) {
               
                perc_discount.value = '100';

            }

        }
        else if (exact_amountt==''&&percentage_discount==''){

           document.getElementById("exact_amt").disabled = false;
           document.getElementById("perc_discount").disabled = false;

        }

    }


</script>