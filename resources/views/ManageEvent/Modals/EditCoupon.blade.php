
<style type="text/css">
    input[type="file"] {
  display: block;
}
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>

<div role="dialog"  class="modal fade " style="display: none;">
    {!! Form::model($coupon, ['url' => route('postEditCoupon', [ 'event_id' => $event->id, 'coupon_id' => $coupon->id])]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Edit Coupon: <em>{{$coupon->coupon_code}}</em></h3>
            </div>
                    {!! Form::hidden('coupon_id',$coupon->id) !!}

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Associated Ticket</label> 
                              <div class="form-group">
                                  <select class="form-control" name="id">
                                      <option value="{{$coupon->ticket_id}}">{{$coupon->ticket}}</option>
                                    @foreach($tickets as $item)
                                        @if($item->id!=$coupon->ticket_id)
                                      <option value="{{$item->id}}">{{$item->title}}</option>
                                        @endif
                                    @endforeach
                                  </select>
                            </div>
                
                        </div>
                     </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Coupon State</label> 
                              <div class="form-group">
                                  <select class="form-control" name="state">
                                      <option value="{{$coupon->state}}">{{$coupon->state}}</option>
                                    @php $choices = ['Valid','Used','Invalid']; @endphp
                                    @foreach($choices as $item)
                                        @if($item!=$coupon->state)
                                      <option value="{{$item}}">{{$item}}</option>
                                        @endif
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
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon's Exact Amount</label>
                                <input id="exact_amt" class="form-control" type="number" onkeyup="validate()" name="exact_amt" value="{{$coupon->exact_amount}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon's Percentage Discount</label>
                                <input id="perc_discount" class="form-control" type="number" onkeyup="validate()" name="discount" value="{{$coupon->discount}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon's User Group</label>
                                <input id="group" class="form-control" type="text" name="group" placeholder="Enter User Group's Name" value="{{$coupon->group}}">
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
               {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('Save Coupon', ['class'=>"btn btn-success"]) !!}
            </div>



        </div> <!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>



<script type="text/javascript">

    /*document.onload({ function(){

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
    })
    */
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
