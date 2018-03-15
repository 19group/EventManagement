<div class="row bg-white" ">

<section id="tickets" class="container" ">



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

                                   </div>
                                     <div class="row">
                                                   <p class="col-sm-12 ticket-title semibold" property="name">
                                                    {{'Donate for this event'}}
                                                   </p>
                                                   <div class="col-md-8">
                                                    <p class="ticket-descripton mb0 " property="description">
                                                     {{'Your contribution will be added as part of your ticket prices.'}}
                                                    </p>
                                                   </div>
                                                   <div class="col-md-4">
                                                    <div class="input-group form-group">
                                                     <span class="input-group-addon"></span>
                                                     <input class="form-control" type="input" name="donation" placeholder="Donation in USD">
                                                    </input>
                                                    </div>
                                                   </div>
                                      </div>

                               </div>

                        </div>
                        <hr>

                        <!--Tickets and Side Events Details -->
                        <div class="row">

                         <div class="col-sm-12">
                             <h1 class='section_head'>
                                 Tickets and Side Events
                             </h1>
                         </div>

                          <!-- Tickets -->
                          <div class="col-md-7">


                            <div class="tickets_table_wrap">
                             <table class="table">
                              <?php
                              $is_free_event = true;
                              ?>
                              @foreach($tickets as $ticket)
                                  <tr class="ticket" property="offers" typeof="Offer">
                                      <td>
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
                                      <td style="width:180px; text-align: right;">
                                          <div class="ticket-pricing" style="margin-right: 20px;">
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
                                      <td style="width:85px;">
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
                                                  </select>
                                              @endif

                                          @endif
                                      </td>
                                  </tr>
                              @endforeach



                                                            @if($extras->count()>0)

                                                            <tr class="ticket" property="offers" typeof="Offer">
                                                                <td>
                                                            <h3>Extras</h3>
                                                           </td>
                                                          </tr>

                                                            @foreach($extras as $extra)
                                                                <tr class="ticket" property="offers" typeof="Offer">
                                                                    <td>
                                                            <span class="ticket-title semibold" property="name">
                                                                {{$extra->title}}
                                                            </span>
                                                                    <td style="width:180px; text-align: right;">
                                                                        <div class="ticket-pricing" style="margin-right: 20px;">
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
                                                                    <td style="width:85px;">
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
                                                                                </select>
                                                                            @endif

                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @endif

                              <tr class="checkout">
                                  <td colspan="3">
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
                          </div>

                          <!--Side event container -->
                          <div class="col-md-5 side-events">
                             <h4> SIDE EVENTS</h4>
                               <?php foreach ($sideevent as $minevent){?>
                                   <hr>
                                   <p><b>{{$minevent->title}} </b></p>
                                   <p>{{$minevent->description}} </br></p>
                                   <div class="row">
                                         <div class="col-sm-6">
                                          <b class="col-sm-6" left>Price:</b>
                                           <span class="col-sm-6" title='{{money($ticket->price, $event->currency)}} Ticket Price + {{money($ticket->total_booking_fee, $event->currency)}} Booking Fees'>{{money($minevent->price, $event->currency)}} </span>
                                         </div>
                                         <div class="col-sm-6">
                                                 {!! Form::hidden('tickets[]', $minevent->id) !!}
                                                 <select name="ticket_{{$minevent->id}}" class="form-control"
                                                         style="text-align: center">
                                                     @if ($minevent->count() > 1)
                                                         <option value="0">0</option>
                                                     @endif
                                                     @for($i=$minevent->min_per_person; $i<=$minevent->max_per_person; $i++)
                                                         <option value="{{$i}}">{{$i}}</option>
                                                     @endfor
                                                 </select>
                                         </div>
                                   </div>
                               <?php } ?>
                          </div>
                          <hr />
                          {!!Form::submit('Register', ['class' => 'btn btn-lg btn-primary pull-right'])!!}
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
