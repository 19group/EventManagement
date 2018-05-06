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

              @php
              $days = explode ("[+]",$minevent->ticket_extras);
              @endphp

              @foreach ($days as $day)

              @php
              $contents = explode("{#}",$day,3);
              $count = 0;
              @endphp

              @foreach ($contents as $content)

              @if($count == 0)
              <h4><b>{{$contents[0]}}</b></h4>
              @endif

              @if($count == 1)
              <p>{{$content}}</p>
              @endif

              @php
              $count = $count + 1;
              @endphp
              @endforeach

              @endforeach


             <span>

             </span>
             </span>
            </div>
            <div class="col-sm-9">
             <!--<input type="date" name="mydates[]" class="form-control" required>-->
            </div>
           </div>
           <div class="col-sm-12">
            <div class="col-sm-8 field-label">
<!--------------------ticket_main_photo----------------------------//-->
@if($minevent->ticket_main_photo)
  @php $mainphotopath=$minevent->ticket_main_photo; @endphp
@elseif($minevent->ticket_photos)
  @php $mainphotopath=explode(config('attendize.sideevent_photos_eximploders'),$minevent->ticket_photos)[0]; @endphp
@endif
@if(isset($mainphotopath))
  <!--display script here using <img src = "{{asset($mainphotopath)}}"> -->
@endif
<!--------------------end-ticket_main_photo------------------------//-->

<!--------------------side-event-more-photos----------------------//-->
@if($minevent->ticket_photos)
   @php $morephotospaths=explode(config('attendize.sideevent_photos_eximploders'),$minevent->ticket_photos); @endphp
  @foreach($morephotospaths as $morephoto)
    <!--display each photo script using <img src="{{asset($morephoto)}}"-->
  @endforeach
@endif
<!------------------end-side-event-more-photos---------------------//-->
             <span>
              @if($minevent->ticket_offers!=NULL)
                       @php $ticket_offers = explode('+++',$minevent->ticket_offers); @endphp

                       <p><b> Ticket Schedules for this side event </b></p>
                       @for($i=0;$i<count($ticket_offers);++$i)
                           @php $sched = explode('<==>',$ticket_offers[$i]);
                           $count = $i+1; @endphp
                           <div class="row">
                           <p>
                           {{date('d-M-Y H:i', strtotime($sched[0]))}}to{{date('d-M-Y H:i', strtotime($sched[1]))}}&nbsp
                           </p>
                           </div>
                       @endfor
             @endif
             </span>
            </div>

<!---------------------------side-event-pages-------------------------------//-->
@if($minevent->ticket_extras)
  @php $sideeventpages=explode(config('attendize.sideevent_pages_eximploders'), $minevent->ticket_extras); @endphp
  @foreach($sideeventpages as $sidepage)
    @php list($pagetitle,$pagediscription,$pagephotosstring)=explode(config('attendize.sideevent_singlepage_eximploders'),$sidepage);
    $pagephotospaths=explode(config('attendize.sideevent_photos_eximploders'),$pagephotosstring);
    $discripts=explode('#', $pagediscription);
    @endphp
    <h4><!--{{$pagetitle}}--></h4>
    <!--/*
     * $pagetitle is a title for a page
     * $pagediscription is a formulatable disription: can be paragraph only, list only or intro
     * paragraph plus a list... depends on the hash character usages
     * $pagephotospaths is an array of page-photos-paths where for each( $pagephotospaths as $srcpath) can
     * be displayed using <img src = "{|{asset($srcpath)}}"
     */-->
    @php $discriptcount=count($discripts);@endphp
    @if($discriptcount==1)
      <!--display discription as a single paragraph-->
      <p><!--{{$pagediscription}}--></p>
    @elseif(strlen(trim($discripts[0]))==0)
      <!--display discription as a list of discripts-->
      <ul>
        @foreach($discripts as $discript)
        <li><!--{{$discript}}--></li>
        @endforeach
      </ul>
    @else
      <!--display $discripts[0] as an intro paragraph and the rest as a list of items-->
      <p><!--{{$discript[0]}}--></p>
      @for($dis=1;$dis<$discriptcount;++$dis)
      <li><!--{{$discripts[$dis]}}--></li>
      @endfor
    @endif
  @endforeach
@endif
<!-------------------------end-side-event-pages-----------------------------//-->

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
