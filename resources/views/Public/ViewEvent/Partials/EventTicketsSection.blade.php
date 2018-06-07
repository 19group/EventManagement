<div class="row bg-white">
<section id="tickets" class="container">

    @if($event->start_date->isPast())
        <div class="alert alert-boring">
            This event has {{($event->end_date->isFuture() ? 'already started' : 'ended')}}.
        </div>
    @else

        @if($tickets->count() > 0)

            {!! Form::open(['url' => route('postValidateTickets', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="content">

                        <!-- Personnel Details -->
                        <div class="row">

                               <!--Personnel Detail Container -->
                               <div class="col-md-12">

                                <div class="row">
                                    <h1 class='col-sm-12 section_head'>
                                        Your Details
                                    </h1>
                                </div>
                                   <div class="row">
                                               <div class="col-md-4">
                                                   <div class="form-group">
                                                       {!! Form::label('first_name', 'First Name', array('class'=>'font-weight-bold control-label required')) !!}
                                                       {!!  Form::text('first_name', Input::old('first_name'),
                                                   array(
                                                   'class'=>'form-control','required'=>'yes'
                                                   ))  !!}
                                                   </div>
                                               </div>
                                               <div class="col-md-4">
                                                   <div class="form-group">
                                                       {!! Form::label('last_name', 'Last Name', array('class'=>'font-weight-bold control-label required')) !!}
                                                       {!!  Form::text('last_name', Input::old('last_name'),
                                                   array(
                                                   'class'=>'form-control ','required'
                                                   ))  !!}
                                                   </div>
                                               </div>

                                               <div class="col-md-4">
                                                   <div class="form-group">
                                                       {!! Form::label('email', 'Email', array('class'=>'font-weight-bold control-label required')) !!}
                                                       {!!  Form::text('email', Input::old('email'),
                                                   array(
                                                   'class'=>'form-control','required'
                                                   ))  !!}
                                                   </div>
                                               </div>
                                  </div>

                                     <div class="row">
                                                   <p class="col-sm-12 ticket-title semibold" property="name">
                                                    {{'Donate for this event'}}
                                                   </p>
                                                   <div class="col-sm-12 col-md-6">
                                                    <p class="ticket-descripton mb0 " property="description">
                                                     {{'Your contribution will be added as part of your ticket prices.'}}
                                                     <span>Contributions goes towards helping <a href="https://www.osgeo.org/foundation-news/foss4g-2018-travel-grant-programme/" target="_blank"> The Travel Grant Programme</a></span>
                                                    </p>
                                                   </div>
                                                   <div class="col-sm-12 col-md-6 donation-price-container">
                                                   <div class="col-xs-4">
                                                     {!!Form::checkbox('defaultdonation','1',true,['id'=>'chkdonation', 'onchange'=>'handleChange(this)'])!!} 5% of ticket price

                                                   </div>
                                                   <div class="col-xs-8">
                                                     <div class="input-group form-group">
                                                      <label> Or enter custom amount below</label>
                                                      <input class="form-control" id="txtdonation" onchange="txtchanged()" type="number" name="donation" placeholder=" Amount in {{$event->currency['title']}}">
                                                     </input>
                                                     </div>
                                                   </div>
                                                  </div>
                                      </div>

                               </div>

                        </div>
                        <hr>

<!-- //////////////////////////// Edit - Order - Section /////////////////////-->

<!--
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item">
    <a class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
  </li>
</ul>
<div class="tab-content" id="pills-tabContent">
  <div class="tab-pane fade " id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">First Page</div>
  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">Second Page</div>
  <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">Third Page</div>
</div>
-->

<div class="row order_button">
    <div class="col-md-12">
        <div class="form-group">
        {!! Form::button('I want to add items to my past order', ['class'=>"btn btn-success pull-right", 'id'=>"control_entry", 'onclick'=>"myFunction();"]) !!}
        </div>
    </div>
</div>
<div id='editorder' style="display: none;">
  <h1 class='section_head'>Adding items to Past Order</h1>
  <div class="row">
   <div class="col-md-12">
       <div class="form-group">
           {!! Form::label('order_ref', 'Remind us your Order Reference', array('class'=>'font-weight-bold control-label required')) !!}
           {!!  Form::text('order_ref', Input::old('order_ref'),
       array(
       'class'=>'form-control',''
       ))  !!}
       </div>
   </div>
 </div>
 <!--<p>Please make sure you have filled in the form your details just as they appear in your last order</p>-->
</div>
<script>
  function myFunction(){
      var button = document.getElementById('control_entry');
      var edit = document.getElementById('editorder');
    //  var ticketord = document.getElementById('createorder');
      if(button.value == 'I want create new order'){
        edit.setAttribute('style','display:none');
    //    ticketord.setAttribute('style','display:block');
        button.value = 'I want to add items to my past order';
      }else{
        edit.setAttribute('style','display:block');
    //    ticketord.setAttribute('style','display:none');
        button.value = 'I want create new order';
      }
  }
</script>

<!-- //////////////////////////// End Edit - Order - Section /////////////////////-->

                        <!--Tickets Details -->
                        <div class="row" id='createorder'>

                         <div class="col-sm-12">
                             <h1 class='section_head'>
                                 Choose Your Tickets
                             </h1>
                         </div>


                          <!-- Tickets -->
                          <!----<div class="col-md-7">---->


                            <div class="tickets_table_wrap row col-md-12 ">
                             <table class="table">
                              <?php
                              $is_free_event = true;
                              ?>
                              @foreach($tickets as $ticket)
                                  <tr  class="ticket " property="offers" typeof="Offer">
                                      <td class="col-xs-7 col-md-6">
                              <span class="ticket-title semibold" property="name">
                                  {{$ticket->title}}
                              </span>
                                          <p class="ticket-descripton mb0 " property="description">
                                              <!--{{$ticket->description}}-->
                                              {!! Markdown::parse($ticket->description) !!}
                                          </p>
                                      <!--added by Donald --Ticket Offers Display-->
                                      <?php if(strlen($ticket->ticket_offers)) {?>
                                          <?php $toffers=explode('#@#',$ticket->ticket_offers);
                                          echo '<p class="ticket-descripton mb0 " property="ticket offers"><b>'.'This ticket offers:- '.'</b></p>';
                                          echo '<ul>';
                                          foreach($toffers as $toffer){
                                          echo '<li><p class="ticket-descripton mb0 " property="ticket offers">'.$toffer.'</p></li>';
                                          }
                                          echo '</ul>' ?>
                                      </td>
                                      <?php } ?>
                                      <!--end of addition-->
                                      <td class="col-xs-2">
                                          <div class="ticket-pricing" style="margin-right: 20px; text-align:right">
                                              @if($ticket->is_free)
                                                  FREE
                                                  <meta property="price" content="0">
                                              @else
                                                  <?php
                                                  $is_free_event = false;
                                                  ?>
                                                  <span title='{{money($ticket->price, $event->currency)}} Ticket Price + {{money($ticket->total_booking_fee, $event->currency)}} Booking Fees'>{{money($ticket->total_price, $event->currency)}} </span>
                                                  <meta property="priceCurrency"
                                                        content="{{ $event->currency->code }}">
                                                  <meta property="price"
                                                        content="{{ number_format($ticket->price, 2, '.', '') }}">
                                              @endif
                                          </div>
                                      </td>
                                      <td class="col-xs-3 col-md-4">
                                          @if($ticket->is_paused)

                                              <span class="text-danger">
                                  Currently Not On Sale
                              </span>

                                          @else

                                              @if($ticket->sale_status === config('attendize.ticket_status_sold_out'))
                                                  <span class="text-danger" property="availability"
                                                        content="http://schema.org/SoldOut">
                                  Sold Out
                              </span>
                                              @elseif($ticket->sale_status === config('attendize.ticket_status_before_sale_date'))
                                                  <span class="text-danger">
                                  Sales Have Not Started
                              </span>
                                              @elseif($ticket->sale_status === config('attendize.ticket_status_after_sale_date'))
                                                  <span class="text-danger">
                                  Sales Have Ended
                              </span>
                                              @else
                                                  {!! Form::hidden('tickets[]', $ticket->id) !!}
                                                  <meta property="availability" content="http://schema.org/InStock">
                                                  <select name="ticket_{{$ticket->id}}" class="form-control"
                                                          style="text-align: center">
                                                      @if ($tickets->count() > 1)
                                                          <option value="0">0</option>
                                                      @endif
                                                      @for($i=$ticket->min_per_person; $i<=$ticket->max_per_person; $i++)
                                                          <option value="{{$i}}">{{$i}}</option>
                                                      @endfor
                                                  </select><br>
                                                   <input type="text" name="coupon_{{$ticket->id}}" class="form-control" placeholder="Coupon#">
                                              @endif

                                          @endif
                                      </td>
                                  </tr>
                              @endforeach

                              <tr class="checkout">
                                  <td>
                                      @if(!$is_free_event)
                                          <div class="hidden-xs pull-left">
                                              <img class=""
                                                   src="{{asset('assets/images/public/EventPage/credit-card-logos.png')}}"/>
                                              @if($event->enable_offline_payments)

                                                  <div class="help-block" style="font-size: 11px;">
                                                      Offline Payment Methods Available
                                                  </div>
                                              @endif
                                          </div>

                                      @endif
                                  </td>
                              </tr>
                               </table>
                            </div>
                          <!---</div>  -->
                        </div>


                         <div class="row col-sm-12">
                            <div class="form-group">
                           {!!Form::checkbox('subscription','1','')!!} &nbsp Please email me about future FOSS4G events
                            </div>
                         </div>
                         <div class="row col-sm-12 alert-danger">
                            <div class="form-group">
                           {!!Form::checkbox('validated','1','',['id'=>'validatereg', 'onchange'=>'controlRegister(this)'])!!} &nbsp I understand that by registering here I will receive emails relating to the planning and logistics of FOSS4G2018
                            </div>
                         </div>
                        <div class="col-sm-12">
                        {!!Form::submit('Register', ['class' => 'btn btn-lg btn-primary pull-right', 'id'=>'regbutton', 'disabled'=>'true'])!!}
                       </div>

                    </div> <!-- End Content -->

                </div>
            </div>
            {!! Form::hidden('is_embedded', $is_embedded) !!}
            {!! Form::close() !!}

        @else

            <div class="alert alert-boring">
                Tickets are currently unavailable.
            </div>

        @endif

    @endif

</section>
</div>
<script>
/*$(document).ready(function () {
    $('#validatereg').on('click', function () {
        if (!this.checked)
         alert("am not checked");
        else
            //$('#autoUpdate').fadeOut('slow');
          alert("am checked");
    });
});*/

function txtchanged(){
 if(document.getElementById('txtdonation').value == ""){
  document.getElementById('chkdonation').checked = true;
 }else{
  document.getElementById('chkdonation').checked = false;
 }
}
//Removes the value from the txt
function handleChange(checkbox){
 if(checkbox.checked){
  document.getElementById('txtdonation').value = "";
 }
}
function controlRegister(acceptemail){
  if(acceptemail.checked){
    document.getElementById('regbutton').disabled=false;
  }else{
    document.getElementById('regbutton').disabled=true;
  }
}
/*$(document).ready(function() {
$('#validatereg').on('change', function(e){
  alert("am changed");
  document.getElementById('regbutton').style.display="block";
});
});*/

</script>
