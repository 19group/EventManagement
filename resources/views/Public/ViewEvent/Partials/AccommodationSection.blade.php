<div class="row bg-white">

 <section id="tickets" class="container" >

  <div class="col-md-4 pull-right"><br><br>
   @include('Public.ViewEvent.Partials.OrderSummary')
   </div>

   <div class="col-md-8">
    <div class="col-md-12">

     <div class="content">


      <div class="row">

       <div class="col-sm-12">
        <h3 class='section_head'>
         Extra Accomodation
        </h3>
       </div>


       <div class="">

         @foreach($accomodations as $accomodation)
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
            <div id="" class="row col-md-12">
             <button onClick="updateTitle()" data-toggle="modal" data-target="#{{$accomodation->status}}" class="btn btn-primary">
              Book
             </button>

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
                      <input type="date" name="mydates[]" class="form-control" required>
                     </div>
                    </div>

                    <div class="form-group col-md-12" id="dategenerator{{ $accomodation->id}}">
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

                var counter = 2;
                var limit = 14;

                function addInput(divName){
                 //Checkif there is a variable with the value
                 if(typeof(window[divName+counter])==="undefined"){
                  window[divName+counter] = counter;
                 }
                 var currentcounter = window[divName+counter];

                 if (currentcounter == limit)  {
                  alert("You have reached the limit of adding extra days");
                 }
                 else {
                  var newdiv = document.createElement('div');
                  newdiv.classList.add("row");
                  newdiv.classList.add("day-row");
                  newdiv.innerHTML = "<div class='col-sm-3 field-label'><label> Day " + (currentcounter) + " </label></div><div class='col-sm-9'><input type='date' class='form-control' name='mydates[]' ></div>";
                  document.getElementById(divName).appendChild(newdiv);
                  window[divName+counter]++;
                 }
                }

                </script>
               </div>
              </div>
             </div>
             <!-- End of Modal -->

    </div>
   </div>

  </div>
  <div class="col-md-12 text-light text-center bg-secondary">
   @for($i=0; $i<$accomodation->status; $i++)
   <i class="glyphicon glyphicon-star" style="color: #FFD700"></i>
   @endfor
   <br>
  </div>
 </div>
 <div style="height: 35vh;">

 </div>

@endforeach

</div>
<hr />
</div>

</div> <!-- End Content -->



</div>
</div>
<div class="col-md-4 pull-right">

  <a href="{{ route('checkOut', ['event_id'=> $event_id]) }}" class="btn btn-lg btn-primary pull-right">CheckOut</a>
  <!--for testing without pesapal: uncomment this--<a href="/e/{{$event_id}}/pesament/create?is_embedded=0#order_form" class="btn btn-lg btn-primary pull-right">CheckOut</a>-->

</div>

</section>


</div>
<script type="text/javascript">
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
