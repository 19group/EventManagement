<div class="row bg-white">

 <section id="tickets" class="container" >

  <div class="col-xs-12">
  <h1 class='section_head'>
  Add Extra Accommodation
  </h1>
  </div>

    <div class="col-sm-12 col-lg-4 pull-right col-event-order">
     @include('Public.ViewEvent.Partials.OrderSummary')

     <div class="">
      @if(Utils::isSuperAdmin())
        <?php if($order_total + $donation > 0){ ?>
        <a class="btn btn-lg btn-primary" href="{{ route('orgskippayment', ['event_id'=> $event_id]) }}">Skip Pay</a>  <?php } ?>
        <?php if(count($order_has_validdiscount)>0 || $order_total + $donation > 0){ ?>
       <!--a href="{{ route('showEventCheckout', ['event_id'=> $event_id]) }}" class="btn btn-lg btn-primary pull-right">Go Pay</a><?php //} ?>-->
       <a href="{{ route('completeAccommodation', ['event_id'=> $event_id]) }}" class="btn btn-lg btn-primary pull-right">Tickets</a><?php } ?>
      @else
       <?php if(count($order_has_validdiscount)>0 || $order_total + $donation > 0){ ?>
       <a href="{{ route('showEventCheckout', ['event_id'=> $event_id]) }}" class="btn btn-lg btn-primary pull-right">Tickets</a><?php } ?>
      @endif
    </div>
     </div>

     <div class="col-sm-12 col-lg-8 col-event-details">

       @foreach($accomodations as $accomodation)

       <div class="row accommodation-container">
        <div class="col-xs-12 no-left-padding">
        <div id="title" class="col-xs-6 ">
         <h5><b>{{ $accomodation->title }}</b></h5>
        </div>
        <div class="col-xs-6 text-light text-right">
         @for($i=0; $i<$accomodation->status; $i++)
         <i class="glyphicon glyphicon-star" style="color: #FFD700"></i>
         @endfor
         <br>
        </div>
        </div>
          <div class="col-xs-12 no-left-padding ">
           <div id="image" class="col-xs-5 no-padding-left">
            <img src="{{asset('assets/images/default/hotel.jpg')}}" />
           </div>
           <div id="content" class="col-xs-7">
            <p>{{$accomodation->description}}</p>

            @if($accomodation->ticket_offers!='')
            <?php
            $offers = explode("#@#",$accomodation->ticket_offers)
            ?>
            <ul>
            <div id="offers" class="col-xs-12">
             @foreach($offers as $offer)
             <li>{{ $offer }}</li>

             @endforeach
            </div>
            </ul>
            @endif


            </div>
           </div>
           <div class="col-xs-12 no-left-padding ">
             <div class="col-xs-8" style="padding-top:10px">
              <span>Price:&nbsp;</span> <b>{{ money($accomodation->price, $event->currency) }}</b> <span> per day</span>
             </div>
            <div id="" class="row col-xs-4 " style="text-right">
             <button style="" onClick="updateTitle()" data-toggle="modal" data-target="#{{$accomodation->status}}" class="btn btn-primary">
              Add Additional Nights
             </button>
            </div>
           </div>
         </div>

             <!-- Beginning of Modal -->
             <div class="modal fade" id="{{$accomodation->status}}" >
              <div class="modal-dialog">
               <div class="modal-content">
                <div class="modal-head text-light text-center" style="background-color: #5fa9da">
                 <h2> {{ $accomodation->title}} </h2>
                </div>

                <form action="{{ route( 'postOrderAccommodation', ['event_id'=>$accomodation->event_id]) }}" method="post">

                 {{ csrf_field() }}
                 {!! Form::hidden('ticket_id', $accomodation->id) !!}
                 <div class="modal-body">
                  <div class="col-md-12 container-fluid">
                   <div class="form-group col-md-12" id='datetimepicker4'>
                    <div class="col-sm-12">
                     <div class="col-sm-3 field-label">
                      <label>
                       Extra Day
                      </label>
                     </div>
                     <div class="col-sm-9">
                      <input type="text" name="mydates[]" class="form-control" min="2018-08-15" max="2018-09-15" list="thesedates">

                      <datalist id="thesedates">
                          <option label="7 Days Before" value="2018-08-22">2018-08-22</option>
                          <option label="6 Days Before" value="2018-08-23">2018-08-23</option>
                          <option label="5 Days Before" value="2018-08-24">2018-08-24</option>
                          <option label="4 Days Before" value="2018-08-25">2018-08-25</option>
                          <option label="3 Days Before" value="2018-08-26">2018-08-26</option>
                          <option label="2 Days Before" value="2018-08-27">2018-08-27</option>
                          <option label="Check-in-day" value="2018-08-28">2018-08-28</option>
                          <option label="Event Day 1" value="2018-08-29">2018-08-29</option>
                          <option label="Event Day 2" value="2018-08-30">2018-08-30</option>
                          <option label="Check-out-day" value="2018-08-31">2018-08-31</option>
                          <option label="1 Day After" value="2018-09-1">2018-09-1</option>
                          <option label="2 Days After" value="2018-09-2">2018-09-2</option>
                          <option label="3 Days After" value="2018-09-3">2018-09-3</option>
                          <option label="4 Days After" value="2018-09-4">2018-09-4</option>
                          <option label="5 Days After" value="2018-09-5">2018-09-5</option>
                          <option label="6 Days After" value="2018-09-6">2018-09-6</option>
                          <option label="7 Days After" value="2018-09-7">2018-09-7</option>
                      </datalist>
                     </div>
                    </div>

                    <div class="form-group col-md-12" id="dategenerator{{$accomodation->id}}">
                    </div>

                    <div class="col-md-12">
                     <button type="button" class="btn btn-primary" onClick="addInput('dategenerator{{$accomodation->id}}');">Add another day</button>
                    </div>
                   </div>
                   <input type="text" name="price" hidden value="{{$accomodation->price}}" >
                   <input type="text" name="event_id" hidden value"{{$accomodation->event_id}}" >
                   <input type="text" name="status" hidden value="{{$accomodation->status}}" >
                   <input type="text" name="title" hidden value="{{$accomodation->title}}" >
                   <input type="text" name="old_total" hidden value="{{$order_total}}" >
                  </div>
                 </div>

                 <div class="modal-footer">
                  <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  <button class="btn btn-success" type="submit" value="submit">Save</button>
                 </div>
                </form>
                <script>

                var accommodationCounter = 2;
                var limit = 15;

                function addInput(divName){

                 //Checkif there is a variable with the value
                 if(typeof(window[divName+"&"+accommodationCounter])==="undefined"){
                  window[divName+"&"+accommodationCounter] = accommodationCounter;
                  var currentAccomodationCounter = window[divName+"&"+accommodationCounter];
                 }
                 else{
                  var currentAccomodationCounter = window[divName+"&"+accommodationCounter]
                 }

                 if (currentAccomodationCounter == limit)  {
                  alert("You have reached the limit of adding extra days");
                 }
                 else {
                  var newdiv = document.createElement('div');
                  newdiv.classList.add("row");
                  newdiv.classList.add("day-row");
                  newdiv.innerHTML = "<div class='col-sm-3 field-label'><label> Day " + (currentAccomodationCounter) + " </label></div><div class='col-sm-9'><input type='text' class='form-control' min='2018-08-15' max='2018-09-15' name='mydates[]' list='thesedates' ></div>";
                  document.getElementById(divName).appendChild(newdiv);
                  window[divName+"&"+accommodationCounter]++;
                 }
                }

                </script>
               </div>
              </div>
             </div>
             <!-- End of Modal -->

@endforeach

</div>

</section>


</div>
<script type="text/javascript">

$('#pathtracker').onclick(function(){
  alert('got here');
  var form=document.createElement('form');
  var tracker = document.createElement('input');
  form.setAttribute('csrf_field');
  tracker.setAttribute('style','display:none');
  tracker.setAttribute('name','accomodationroute');
  form.appendChild('tracke');
});

function generateDate(){
 document.write('This is a test');

 var selectcount=1;

 function myFunction(trigger) {
  var ticksdiv = document.createElement('div');
  var daysdiv = document.createElement('div');
  var ticksselect = document.createElement('select');
  var daysselect = document.createElement('select');
  var optnumbers;
  var optionsarr = [];

  ticksdiv.setAttribute("class", "form-group col-md-6");
  daysdiv.setAttribute("class", "form-group col-md-6");

  ticksselect.setAttribute("name", trigger + "_accommotickets_" + selectcount);
  ticksselect.setAttribute("id", "accommoticketsid_" + selectcount);
  ticksselect.setAttribute("class", "form-control");
  ticksselect.setAttribute("style", "text-align: center");

  daysselect.setAttribute("name", trigger + "_accommodays_" + selectcount);
  daysselect.setAttribute("id", "accommodaysid_" + selectcount);
  daysselect.setAttribute("class", "form-control");
  daysselect.setAttribute("style", "text-align: center");

  ticksdiv.appendChild(ticksselect);
  daysdiv.appendChild(daysselect);

  for (optnumbers = 0; optnumbers < 16; optnumbers++) {
   ticksselect.options[ticksselect.options.length] = new Option(optnumbers,optnumbers);
  }
  for (daysoptnumbers = 0; daysoptnumbers < 16; daysoptnumbers++) {
   daysselect.options[daysselect.options.length] = new Option(daysoptnumbers,daysoptnumbers);
  }

  document.getElementById("extraaccommos_" + trigger).appendChild(ticksdiv);
  document.getElementById("extraaccommos_" + trigger).appendChild(daysdiv);

  selectcount++;
 }

 $(function() {
  $('#accommobutton').on('click', function(e) {
   alert('new value is ');
  });

 });
}
 </script>
