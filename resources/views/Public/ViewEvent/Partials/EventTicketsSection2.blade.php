<div class="row bg-white" ">

<section id="tickets" class="container" >

  <div class="col-md-4 pull-right"><br><br>
     <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-cart mr5"></i>
                        Order Summary
                    </h3>
                </div>

                <div class="panel-body pt0">
                    <table class="table mb0 table-condensed">

                     <?php $donhead='Donation Amount';if($donation>0){ ?>
                             <tr>
                                 <td class="pl0"><b>Donation Amount:</b></td>
                                 <td style="text-align: right;">
                                     {{  money($donation, $event->currency) }}
                                 </td>
                             </tr>
                         <?php } ?>

                         @php
                            $i=0
                        @endphp

                        @foreach($tickets as $ticket)
                        <tr>
                            <td class="pl0">{{{$ticket['ticket']['title']}}} X <b>{{$ticket['qty']}}</b></td>
                            <td style="text-align: right;">
                                @if((int)ceil($ticket['full_price']) === 0)
                                FREE
                                @else
                                        

                                    @if(  $discount[$i]!='' and $discount_ticket_title[$i]==$ticket['ticket']['title'] )
                                        <strike>{{ money($ticket['full_price'], $event->currency) }}</strike> 
                                        {{ money($ticket['full_price']-$ticket['full_price']*($discount[$i]/100), $event->currency) }}

                                        @php
                                            $i++
                                        @endphp
                                    
                                    @elseif(  $exact_amount[$i]!='' and $amount_ticket_title[$i]==$ticket['ticket']['title'] )
                                        <strike>{{ money($ticket['full_price'], $event->currency) }}</strike> 
                                        {{ money($exact_amount[$i], $event->currency) }}

                                         @php
                                            $i++
                                        @endphp

                                    @elseif(  $exact_amount[$i]=='' and $discount[$i]=='' )
                                        {{ money($ticket['full_price'], $event->currency) }}
                                        @php
                                            $i++
                                        @endphp
                                        
                                    @endif

                                @endif
                            </td>
                        </tr>
                        @endforeach
                        
                    </table>
                </div>
                @if($order_total +$donation > 0)
                <div class="panel-footer">
                    <h5>
                        Subtotal: <span style="float: right;"><b>{{ money($order_total + $total_booking_fee  + $donation, $event->currency) }}</b></span>
                    </h5>
                </div>
                @endif

            </div>
  </div>




        
      

            <div class="row">
                <div class="col-md-12">

                    <div class="content">

                        
                        <div class="row">

                         <div class="col-sm-12">
                             <h3 class='section_head'>
                               Accomodation
                             </h3>
                         </div>

                         
                          <div class="">
                            <?php foreach ($accomodations as $accomodation){?>

                              <div class="col-md-12 ">
                                <div class="col-md-12">
                                  <div id="image" class="col-md-5 ">
                                      <img width="100%" height="auto" src="https://cmkt-image-prd.global.ssl.fastly.net/0.1.0/ps/2418804/580/385/m1/fpnw/wm0/hotel-icon-.jpg?1489704131&s=bd2b9f851b8c1c4d8eaa11486cc67398">
                                  </div>

                                  <div id="content" class="col-md-7">
                                    <div id="title" class="col-md-12">
                                       <h5><b>{{ $accomodation->title }}</b></h5>
                                    </div>
                                     <div id="" class="col-md-12">
                                       <span>Price:&nbsp;</span> <b>{{ money($accomodation->price, $event->currency) }}</b>
                                    </div>
                                    @if($accomodation->ticket_offers!='')
                                    <div id="offers" class="col-md-10 col-md-offset-1"><br>
                                        *{{ $accomodation->ticket_offers }}                                    
                                    </div>
                                    @endif
                                    <div id="" class="col-md-12">
                                       
                                              <button onClick="updateTitle()" data-toggle="modal" data-target=<?php echo "#".$accomodation->title; ?> class="btn btn-primary">
                                                Book
                                              </button>

                                              <div class="modal fade" id=<?php echo $accomodation->title; ?> >
                                                <div class="modal-dialog">
                                                  <div class="modal-content">
                                                    <div class="modal-head text-light text-center" style="background-color: #5fa9da">
                                                      <h2> {{ $accomodation->title}} </h2>
                                                    </div>

                                                    <form action="{{ route( 'updateBooking', ['event_id'=>$accomodation->event_id]) }}" method="post">
                                                        {{ csrf_field() }}
                                                    <div class="modal-body">
                                                      <div class="col-md-12">
                                                          <div class="form-group col-md-6">
                                                            <label>
                                                              First Name:
                                                            </label>
                                                            <input type="text"  class="form-control" name="first_name" value="{{ $first_name }}" required>
                                                          </div>

                                                          <div class="form-group col-md-6">
                                                            <label>
                                                              Last Name:
                                                            </label>
                                                            <input type="text"  class="form-control" name="last_name" value="{{ $last_name }}" required>
                                                          </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                          <div class="form-group col-md-6">
                                                            <label>
                                                              Email:
                                                            </label>
                                                            <input type="text"  class="form-control" name="email" value="{{ $email }}" required>
                                                          </div>
                                                    </div>

                                                      <div class="col-md-12 container-fluid">
                                                          <div class="form-group col-md-6">
                                                            <label>
                                                              Number of Days:
                                                            </label>
                                                            <input type="number" class="form-control" name="days" placeholder="0" required>
                                                          </div>
                                                      <div class="form-group col-md-6" id='datetimepicker4'>
                                                        <label>
                                                          From Date:
                                                        </label>
                                                          <input type="date" name="bookingDate" class="form-control" required>
                                                      </div>
                                                      <input type="text" name="price" hidden value=<?php echo $accomodation->price;  ?> >
                                                      <input type="text" name="event_id" hidden value=<?php echo $accomodation->event_id;  ?> >
                                                      <input type="text" name="status" hidden value=<?php echo $accomodation->status;  ?> >
                                                      <input type="text" name="title" hidden value=<?php echo $accomodation->title;  ?> >
                                                      <input type="text" name="old_total" hidden value=<?php echo $order_total;  ?> >
                                                    </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                          <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                          <button class="btn btn-success" type="submit" value="submit">Save</button>
                                                    </div>
                                                    </form>
                                                  </div>
                                                </div>
                                              </div>
                                         
                                    </div>
                                  </div>

                                </div>
                                <div class="col-md-12 text-light text-center bg-secondary"><br>
                                  Status:&nbsp;
                                   @for($i=0; $i<$accomodation->status; $i++)
                                        <i class="glyphicon glyphicon-star" style="color: #FFD700"></i>
                                    @endfor
                                    <br>
                                    <br>
                                </div>
                              </div>
                             <div style="height: 35vh;">
                               
                             </div>

                             <?php } ?>
                          
                            </div>
                          <hr />
                        </div>

                    </div> <!-- End Content -->

                </div>
            </div>
            {!! Form::hidden('is_embedded', $is_embedded) !!}
            {!! Form::close() !!}

       
</section>

<script type="text/javascript">
           $('.input-group.date').datepicker({format: "dd.mm.yyyy"}); 
        </script>
</div>