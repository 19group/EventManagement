<div role="dialog"  class="modal fade" style="display: none;">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Choose Side Events</h3>
            </div>
            <form action="{{route('postChooseSideEvents',['event_id'=>$event->id])}}" method="post">
                {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">

    @if($sideevents->count())

    <div class="col-md-12">
        <?php $no=0; ?>

        <!-- START  panel -->
        <div class="panel">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                               Event Title
                            </th>
                            <th>
                               Start Date
                            </th>
                            <th>
                               End Date
                            </th>
                            <th>
                               Organiser
                            </th>
                            <th>
                                Tickets
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($sideevents as $sideevent)
                        <tr>
                            <td>
                                <?php $identifier = 'side_event_'.$no; ?>
                                {{Form::checkbox($identifier, $sideevent->id)}} <?php ++$no;?>
                            </td>
                            <td>
                                {{$sideevent->title}}
                            </td>
                            <td>
                                {{$sideevent->start_date}}
                            </td>
                            <td>
                                {{$sideevent->end_date}}
                            </td>
                            <td>
                                {{\App\Models\Organiser::where(['id'=>$sideevent->organiser_id])->first()->name}}
                            </td>
                            <td>
                                <?php $tickets=\App\Models\Ticket::where(['event_id'=>$sideevent->id])->get(); foreach($tickets as $ticket)?>
                                {{$ticket->title}}({{$ticket->price}}{{\App\Models\Currency::where(['id'=>$sideevent->currency_id])->first()->code}})
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
                                Confirm
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
    
    function generate_coupons(){

        var coupons_limit = document.getElementById('max_coupons').value;

        var token_no = [];

        if (coupons_limit!='') {

            for (i = 0; i <coupons_limit; i++) { 
                    
                token_no[i] = (Math.random().toString(36).substring(2, 12));

                    document.getElementById('target_div').innerHTML += "<br><input id='' class='form-control text-center text-uppercase' disabled type='text' name='token_no["+ i +"]' value='"+ token_no[i] +"'><br>" ;
                    //document.getElementById('tokens').innerHTML += "<input id='discount' class='form-control btn btn-info' disabled type='number' name= " + token[i] + " placeholder="+token[i]+">" ;



                    //(Math.random().toString(36).substring(2, 15)+"\n");


                }
        }
        else{
                    document.getElementById('target_div').innerHTML += "<br><p> Insert a valid number first.</p><br>" ;

        }

        return true;
    }
</script>