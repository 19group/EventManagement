  <style type="text/css">
    * {box-sizing:border-box}

/* Slideshow container */
.slideshow-container {
  max-width: 1000px;
  position: relative;
  margin: auto;
}

/* Hide the images by default */
.mySlides {
    display: none;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  margin-top: -22px;
  padding: 16px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.2s ease;
  border-radius: 0 3px 3px 0;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 15px;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

/* Fading animation */
.fade {
  -webkit-animation-name: fade;
  -webkit-animation-duration: 1.5s; /*1.5s;*/
  animation-name: fade;
  animation-duration: 1.5s; /*1.5s;*/
}

@-webkit-keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

  </style>


  <!-- Beginning of Modal -->
    <div class="modal fade" id="more_details_{{$minevent->id}}" >
     <div class="modal-dialog">
      <div class="modal-content">
       <div class="modal-head text-light text-center" style="background-color: #5fa9da">
        <h2> {{ $minevent->title}} </h2>
       </div>
       <!--Form::open(['url' => route('postBookSideEvent', ['event_id' => $event->id]), 'class' => 'ajax']) !!}-->
       <form action="{{ route( 'postOrderSideEvents', ['event_id'=>$event->id]) }}" method="post">

        {{ csrf_field() }}
        {!! Form::hidden('ticket_id', $minevent->id) !!}
        <div class="modal-body">
         <div class="col-md-12 container-fluid">

          <div class="form-group col-md-12" id='datetimepicker4'>
           <div class="col-sm-12">
            <div class="col-sm-12 field-label">
             <span>
              {{$minevent->description}}
             </span>
            </div>
            <div class="col-sm-9">
             <!--<input type="date" name="mydates[]" class="form-control" required>-->
            </div>
           </div>
              <div class="row">
                <!--div class="col-xs-4 side-event-image"-->
                  <?php if($minevent->ticket_main_photo){ ?>
                   <img height=180 width=150 src="{{asset($minevent->ticket_main_photo)}}" />
                  <?php }elseif($minevent->ticket_photos){ 
                    $assumedefault=explode(config('attendize.sideevent_photos_eximploders'),$minevent->ticket_photos)[0];?>
                   <img height=180 width=150 src="{{asset($assumedefault)}}" />
                  <?php }else{ ?>
                  <img src="{{asset('assets/images/default/trip.jpg')}}" />
                  <?php } ?>
                <!--/div-->
              </div>
          <br>
          <br>
  <!--//-----------------------ticket photos-----------------------//-->   
          <!-- Slideshow container -->
          <div class="slideshow-container" style="min-height: 600px">    
        <?php if($minevent->ticket_photos){ 
          $ticketphotos=explode(config('attendize.sideevent_photos_eximploders'),$minevent->ticket_photos); ?>
            <!-- Full-width images with number and caption text -->
            <?php $total=count($ticketphotos);
              for($photopos=0;$photopos<$total;++$photopos){ $realct= 1 + $photopos;?>
            <div class="mySlides fade">
              <div class="numbertext"> {{$realct}}/{{$total}}</div>
              <img height=360 width=300 src="{{asset($ticketphotos[$photopos])}}">
              <div class="text">Photo Number 1</div>
            </div>
            <?php } //for($photopos=0;$photopos<$total;++$photopos) ?>
            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
          </div><!--slideshow-container-->
          <br>
          <!-- The dots/circles -->
          <div style="text-align:center">
            <?php for($phopos=0;$phopos<$total;++$phopos){ $react= 1 + $phopos;?>
            <span class="dot"></span> 
            <?php } //for($phopos=0;$phopos<$total;++$phopos) ?>
        <?php }//if $minevent->ticket_photos ?>
          </div>
  <!--//------------------endofticketphotos------------------------//-->
           <div class="col-sm-12">
            <div class="col-sm-8 field-label">
             <span>

              <?php if($minevent->ticket_offers!=NULL){
                       $ticket_offers = explode('+++',$minevent->ticket_offers);

                       echo "<p><b> Please select the schedule you prefer </b></p>";
                       for($i=0;$i<count($ticket_offers);++$i){
                           $sched = explode('<==>',$ticket_offers[$i]);
                           $count = $i+1;
                           echo '<div class="row">';
                           echo '<p>';
                           echo'<input type="radio" name="mydates" value="'.$ticket_offers[$i].'" required>'.date('d-M-Y H:i', strtotime($sched[0])).' to '.date('d-M-Y H:i', strtotime($sched[1])).'';
                           echo '</p>';
                           echo '</div>';
                       } ?>
             <?php } ?>

             </span>
            </div>
            <div class="col-sm-4 field-label">
             <div>
              <span><b>{{money($minevent->price, $event->currency)}}</b></span>
              <br/>
              <p>No of Tickets</p>

              <select name="ticket_{{$minevent->id}}" class="form-control"
                      style="text-align: center">
                  @if ($tickets->count() > 1)
                      <option value="0">0</option>
                  @endif
                  @for($i=$minevent->min_per_person; $i<=$minevent->max_per_person; $i++)
                      <option value="{{$i}}">{{$i}}</option>
                  @endfor
              </select><br>


             </div>
            </div>
            <div class="col-sm-9">
             <!--<input type="date" name="mydates[]" class="form-control" required>-->
            </div>
           </div>

           <div class="form-group col-md-12" id="dategenerator{{ $minevent->id}}">
           </div>
          </div>

          <input type="text" name="price" hidden value="{{$minevent->price}}" >
          <input type="text" name="event_id" hidden value"{{$minevent->id}}" >
          <input type="text" name="status" hidden value="{{$minevent->status}}" >
          <input type="text" name="title" hidden value="{{$minevent->title}}" >
          <input type="text" name="old_total" hidden value="{{$order_total}}" >
         </div>

        </div>
        <div class="modal-footer">
         <button class="btn btn-danger" data-dismiss="modal">Close</button>
         <!--button class="btn btn-success" type="submit" value="submit">Save</button-->
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

var slideIndex = 0;
showSlides();

function showSlides() {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    var dots = document.getElementsByClassName("dot");
    for (i = 0; i < slides.length; i++) {
       slides[i].style.display = "none";  
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}    
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";  
    dots[slideIndex-1].className += " active";
    setTimeout(showSlides, 500); // 2000Change image every 2 seconds
}

       </script>
      </div>
     </div>
    </div>
    <!-- End of Modal -->