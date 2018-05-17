
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
    {!! Form::model($ticket, ['url' => route('postEditWorkshop', ['ticket_id' => $ticket->id, 'event_id' => $event->id]), 'enctype' => 'multipart/form-data']) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Edit Event Workshop: <em>{{$ticket->title}}</em></h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('title', 'Workshop Title', ['class'=>'control-label required']) !!}
                    {!!  Form::text('title', null,['class'=>'form-control', 'placeholder'=>'E.g: Creating systems For Identifying Personality From Geographical Data']) !!}
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('price', 'Workshop\'s Ticket Price', ['class'=>'control-label required']) !!}
                            {!!  Form::text('price', null,['class' => 'form-control', 'placeholder' => 'E.g: 25.99']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('quantity_available', 'Quantity Available', ['class'=>' control-label']) !!}
                            {!!  Form::text('quantity_available', null, ['class' => 'form-control', 'placeholder' => 'E.g: 100 (Leave blank for unlimited)']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('description', 'Workshop Short Description', ['class'=>'control-label']) !!}
                    {!!  Form::text('description', null,['class'=>'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('ticket_extras', 'Workshop Presenter',
                     ['class'=>'control-label']) !!}
                    {!!  Form::text('ticket_extras', null,['class'=>'form-control']) !!}
                </div>

                <!--added by Donald-->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12 col-sm-12">
                    {!! Form::label('workshop_image', 'Workshop Main Image (Flyer or Graphic etc.)', array('class'=>'control-label ')) !!}
                    <div class="styledFile" id="input-event_image">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-primary btn-file ">
                                    <?php echo $ticket->ticket_main_photo ? 'Change' : 'Browse…';?> <input name="workshop_image" type="file" multiple="" onchange="readURL(this);">
                                </span>
                            </span>
                            <input type="text" class="form-control" readonly="">
                            <span style="display: none;" class="input-group-btn btn-upload-file">
                                <span class="btn btn-success ">
                                    Upload
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class='row' id='main_image'>
                    <?php if($ticket->ticket_main_photo){?>
                    <img height=180 width=150 style="margin: 5px 1px 10px 200px; align:justify" src="{{asset($ticket->ticket_main_photo)}}" />
                    </br>
                    <?php }//if-ticket_main_photo?>
                </div>
            </div>
        </div>
                <div class="row">
                    <div class="col-sm-10">
                        <!--<div class="form-group">-->
                            {!! Form::label('', 'List Of Sessions for The Workshop', array('class'=>' control-label')) !!}
                        <!--</div>-->
                    </div>
                </div> 
                <?php if($ticket->ticket_offers!=NULL){
                    $toffers = explode('+++',$ticket->ticket_offers);
                    if(count($toffers)>0){
                    for($i=0;$i<count($toffers);++$i){
                        $sched = explode('<==>',$toffers[$i]);
                ?>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <!--{!! Form::label('start_sale_date', 'Start Date For First Schedule', array('class'=>' control-label')) !!}-->

                                {!!  Form::text('ogstart_schedule_'.$i, date('d-m-Y H:i', strtotime($sched[0])),
                                [
                                    'class' => 'form-control start hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'start',
                                    'data-startendelem' => '.end',
                                    'name'=>'ogstart_schedule_'.$i,
                                    'readonly' => '',
                                    'value'=>date('d-m-Y H:i', strtotime($sched[0])),
                                ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    <!--{!! Form::label('end_sale_date', 'End Date For First Schedule', ['class'=>' control-label ' ])  !!}-->
                                    
                                {!!  Form::text('ogend_schedule_'.$i, date('d-m-Y H:i', strtotime($sched[1])),
                                [
                                    'class' => 'form-control start hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'end',
                                    'data-startendelem' => '.start',
                                    'name'=>'ogend_schedule_'.$i,
                                    'readonly' => '',
                                    'value'=>date('d-m-Y H:i', strtotime($sched[1])),
                                ]) !!}
                                </div>
                            </div>
                        </div>
                <?php

                        }
                    } 
                } else { ?>   
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <!--{!! Form::label('start_sale_date', 'Start Date For First Schedule', array('class'=>' control-label')) !!}-->
                                    {!!  Form::text('start_schedule_0', Input::old('start_sale_date'),
                                                    [
                                                'class'=>'form-control start hasDatepicker ',
                                                'data-field'=>'datetime',
                                                'data-startend'=>'start',
                                                'data-startendelem'=>'.end',
                                                'readonly'=>'',
                                                'name'=>'start_schedule_0',
                                                'placeholder'=>'Start Time For First Session'

                                            ])  !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    <!--{!! Form::label('end_sale_date', 'End Date For First Schedule', ['class'=>' control-label ' ])  !!}-->
                                    {!!  Form::text('end_schedule_0', Input::old('end_sale_date'),
                                            [
                                        'class'=>'form-control end hasDatepicker ',
                                        'data-field'=>'datetime',
                                        'data-startend'=>'end',
                                        'data-startendelem'=>'.start',
                                        'readonly'=>'',
                                        'name'=>'end_schedule_0',
                                        'placeholder'=>'End Time For First Session'
                                    ])  !!}
                                </div>
                            </div>
                        </div>

                <?php } ?>

                        <div id="scheduledates">
                            
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                {!! Form::button('Add Another Session', ['class'=>"btn btn-success", 'id'=>"add_schedule"]) !!}
                                </div>
                            </div>
                        </div>
                <!--end of addition-->
            

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('start_sale_date', 'Start Sale On', ['class'=>' control-label']) !!}

                            {!!  Form::text('start_sale_date', $ticket->getFormattedDate('start_sale_date'),
                                [
                                    'class' => 'form-control start hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'start',
                                    'data-startendelem' => '.end',
                                    'readonly' => ''
                                ]) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!  Form::label('end_sale_date', 'End Sale On',
                                        [
                                    'class'=>' control-label '
                                ])  !!}
                            {!!  Form::text('end_sale_date', $ticket->getFormattedDate('end_sale_date'),
                                [
                                    'class' => 'form-control end hasDatepicker',
                                    'data-field' => 'datetime',
                                    'data-startend' => 'end',
                                    'data-startendelem' => '.start',
                                    'readonly' => ''
                                ])  !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('min_per_person', 'Minimum Tickets Per Order', ['class'=>' control-label']) !!}
                           {!! Form::selectRange('min_per_person', 1, 100, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('max_per_person', 'Maximum Tickets Per Order', ['class'=>' control-label']) !!}
                           {!! Form::selectRange('max_per_person', 1, 100, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('Save Workshop', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>




<script>
    $(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("<span class=\"pip\">" +
            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "<br/><span class=\"remove\">Remove photo</span>" +
            "</span>").insertAfter("#files");
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });
          
          // Old code here
          /*$("<img></img>", {
            class: "imageThumb",
            src: e.target.result,
            title: file.name + " | Click to remove"
          }).insertAfter("#files").click(function(){$(this).remove();});*/
          
        });
        fileReader.readAsDataURL(f);
      }
    });
  } else {
    alert("Your browser doesn't support to File API")
  }
}); 
       
    var scheduler = 1;
    $('#add_schedule').on('click', function(e) {
        var p = document.createElement('div');
        var f = document.createElement("div");
        var d = document.createElement('div');
        var z = document.createElement('div');
        var y = document.createElement('div');
        var t = document.createElement('INPUT');
        var i = document.createElement('INPUT');
        p.setAttribute("class", "row");
        f.setAttribute("class", "col-sm-6");
        d.setAttribute("class", "col-sm-6");
        z.setAttribute("class", "form-group");
        y.setAttribute("class", "form-group");
        i.setAttribute("name", "start_schedule_" + scheduler);
        i.setAttribute("data-startend", "");
        i.setAttribute("data-startendelem", "");
        i.setAttribute("type", "text");
        i.setAttribute("class", "form-control start hasDatepicker");
        i.setAttribute("data-field","datetime");
        i.setAttribute("data-startend", "start");
        i.setAttribute("data-startendelem", ".end");
        i.setAttribute("readonly", "");
        i.setAttribute("placeholder", "Start Time For New Session " + scheduler);
        t.setAttribute("name", "end_schedule_" + scheduler);
        t.setAttribute("type", "text");
        t.setAttribute("class", "form-control end hasDatepicker ");
        t.setAttribute("data-field","datetime");
        t.setAttribute("data-startend", "end");
        t.setAttribute("data-startendelem", ".start");
        t.setAttribute("readonly", "");
        t.setAttribute("placeholder", "End Time For New Session " + scheduler);
        p.appendChild(d);
        p.appendChild(f);
        d.appendChild(y);
        f.appendChild(z);
        y.appendChild(i);
        z.appendChild(t);
        scheduler+=1;
        document.getElementById("scheduledates").appendChild(p);  
        i.setAttribute("data-startend", "");
        i.setAttribute("data-startendelem", "");
    //    t.setAttribute("data-startend", "");
    //    t.setAttribute("data-startendelem", "");
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                document.getElementById('main_image').innerHTML = '<img id="blah" src="#" width = 150 height = 180 style="margin:1px 1px 10px 200px" alt="your image" />';
                $('#blah')
                    .attr('src', e.target.result);

            //    $("<span class=\"remove\" onclick = \"$(this).parent(\"#blah\").remove();\">Remove photo</span>").insertAfter("#blah");
                };
        //  $(".remove").click(function(){
        //    $(this).parent("#blah").remove();
        //  });

            reader.readAsDataURL(input.files[0]);

        }
    }

    function newfunction(event) {
      //var event=document.getElementById(passedid);
      var space=event.id;console.dir(event);
      var files = event.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {        
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("<span class=\"pip\">" +
            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "<br/><span class=\"remove\">Remove photo</span>" +
            "</span>").insertAfter("#" + space);
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });
          
        });
        fileReader.readAsDataURL(f);
      }
    }

</script>
