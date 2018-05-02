<?php $offer=0; $toffers=[];?>
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
  margin: 5px 10px 5px 0;
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

<div role="dialog"  class="modal fade" style="display: none;">
   {!! Form::open(array('url' => route('postReCreateSideEvent', array('event_id' => $event->id)), 'enctype' => 'multipart/form-data')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    Create Side-Event</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('title', 'Side Event Title', array('class'=>'control-label required')) !!}
                            {!!  Form::text('title', Input::old('title'),
                                        array(
                                        'class'=>'form-control',
                                        'placeholder'=>'E.g: Three Days Trip to Ngorongoro'
                                        ))  !!}
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('price', 'Ticket Price', array('class'=>'control-label required')) !!}
                                    {!!  Form::text('price', Input::old('price'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>'E.g: 25.99'
                                                ))  !!}


                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('quantity_available', 'Quantity Available', array('class'=>' control-label')) !!}
                                    {!!  Form::text('quantity_available', Input::old('quantity_available'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>'E.g: 100 (Leave blank for unlimited)'
                                                )
                                                )  !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('description', 'Side Event Short Description', array('class'=>'control-label')) !!}
                            {!!  Form::text('description', Input::old('description'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                        </div>

                        <!--added by DonaldApril10-->
                        <div class="form-group">
                            {!! Form::label('sideevent_image', 'Side Event Main Image (Flyer or Graphic etc.)', array('class'=>'control-label ')) !!}
                            <!--{!! Form::styledFile('event_image', ['onchange'=>"readURL(this);"]) !!}-->
                            <div class="styledFile" id="input-event_image">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-primary btn-file ">
                                            Browse… <input name="sideevent_image" type="file" multiple="" onchange="readURL(this);">
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

                        </div>

<div class="form-group">
    {!! Form::label('event_image', 'Side Event More Images', array('class'=>'control-label ')) !!}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <div class="field" align="left">
      <input type="file" class="files" id="files" name="files[]" multiple="multiple" />
    </div>
</div>

    <hr>
    <h4 style="text-align: center">SIDE EVENT MINI PAGES<h4>
    <h5 style="text-align: center">FIRST MINI PAGE<h5>
        <div id="sideevent_pages">
            <div class="form-group">
                {!! Form::label('title', 'Mini Page Title', array('class'=>'control-label')) !!}
                {!!Form::hidden('content_pages[]','0')!!}
                {!!  Form::text('more_title_0', Input::old('title'),
                            array(
                            'class'=>'form-control',
                            'placeholder'=>'Title for specific content eg First Day Activities'
                            ))  !!}
            </div>
            <?php $convention = 'Enter your description using either of the two conventions. <br>Description as a paragraph <br>Its a default, just enter statements and they will appear as a paragragh. <br>Description as Intro and list of statements. <br>Enter the 2 or 3 statements as usual for intro and statements to appear as listed, prefix them with hash character i.e # e.g (You will get the following:- #National Museum tour #Lunch at the white house)<br>Desciption as a list of statements without intro.<br> Just prefix all statements with # e.g (#National Museum tour #Lunch at the white house)'; ?>
            <div class="form-group">
                {!! Form::label('description', 'Mini Page Description', array('class'=>'control-label')) !!}
                {!!  Form::textarea('more_discription_0', null,
                            array(
                            'class'=>'form-control',
                            'placeholder'=>"Enter your description using either of the two conventions. <br>Description as a paragraph <br>Its a default, just enter statements and they will appear as a paragragh. <br>Description as Intro and list of statements. <br>Enter the 2 or 3 statements as usual for intro and statements to appear as listed, prefix them with hash character i.e # e.g (You will get the following:- #National Museum tour #Lunch at the white house)<br>Desciption as a list of statements without intro.<br> Just prefix all statements with # e.g (#National Museum tour #Lunch at the white house)"
                            ))  !!}
            </div>

            <div class="form-group">
                {!! Form::label('more_images', 'Images for This Page', array('class'=>'control-label ')) !!}
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
                <div class="field" align="left">
                  <input type="file" class="files" id="content_0_files" name="content_0_files[]" multiple="multiple" />
                </div>
            </div>
            <div id="more_contents">
                
            </div>
    <hr style="border-top: 1px solid blue;">
            <div class="col-md-12" style="text-align: center">
                <div class="form-group">
                {!! Form::button('Add Another Page', ['class'=>"btn btn-success", 'id'=>'add_content','align'=>'justify','margin-top'=>'10px']) !!}
                </div>
            </div>
        </div>
                        <!--end of addition by DonaldApril10-->

                <!--edited by DonaldMar12-->    
                        <div class="row">
                            <div class="col-sm-10">
                                <!--<div class="form-group">-->
                                    {!! Form::label('', 'List Of Schedules for The Side Event', array('class'=>' control-label')) !!}
                                <!--</div>-->
                            </div>
                        </div>    
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <!--{!! Form::label('start_sale_date', 'Start Date For First Schedule', array('class'=>' control-label')) !!}-->
                                    {!!  Form::text('start_sale_date', Input::old('start_sale_date'),
                                                    [
                                                'class'=>'form-control start hasDatepicker ',
                                                'data-field'=>'datetime',
                                                'data-startend'=>'start',
                                                'data-startendelem'=>'.end',
                                                'readonly'=>'',
                                                'name'=>'start_schedule_0',
                                                'placeholder'=>'Start Date For First Schedule'

                                            ])  !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    <!--{!! Form::label('end_sale_date', 'End Date For First Schedule', ['class'=>' control-label ' ])  !!}-->
                                    {!!  Form::text('end_sale_date', Input::old('end_sale_date'),
                                            [
                                        'class'=>'form-control end hasDatepicker ',
                                        'data-field'=>'datetime',
                                        'data-startend'=>'end',
                                        'data-startendelem'=>'.start',
                                        'readonly'=>'',
                                        'name'=>'end_schedule_0',
                                        'placeholder'=>'End Date For First Schedule'
                                    ])  !!}
                                </div>
                            </div>
                        </div>

                        <div id="scheduledates">
                            
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                {!! Form::button('Add Another Schedule', ['class'=>"btn btn-success", 'id'=>"add_schedule"]) !!}
                                </div>
                            </div>
                        </div>
                <!--end of addition by DonaldMar12-->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('min_per_person', 'Minimum Tickets Per Order', array('class'=>' control-label')) !!}
                                    {!! Form::selectRange('min_per_person', 1, 100, 1, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('max_per_person', 'Maximum Tickets Per Order', array('class'=>' control-label')) !!}
                                    {!! Form::selectRange('max_per_person', 1, 100, 30, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Create Side Event', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>


<script>
    $(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $(".files").on("change", function(event) {
      var space=event.target.id;
      var files = event.target.files,
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
        i.setAttribute("id", "start_sale_date");
        i.setAttribute("type", "text");
        i.setAttribute("class", "form-control start hasDatepicker");
        i.setAttribute("data-field","datetime");
        i.setAttribute("data-startend", "start");
        i.setAttribute("data-startendelem", ".end");
        i.setAttribute("readonly", "");
        i.setAttribute("placeholder", "Start Date For Schedule" + scheduler);
        t.setAttribute("name", "end_schedule_" + scheduler);
        t.setAttribute("id", "end_sale_date");
        t.setAttribute("type", "text");
        t.setAttribute("class", "form-control end hasDatepicker ");
        t.setAttribute("data-field","datetime");
        t.setAttribute("data-startend", "end");
        t.setAttribute("data-startendelem", ".start");
        t.setAttribute("readonly", "");
        t.setAttribute("placeholder", "End Date For Schedule " + scheduler);
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
                document.getElementById('main_image').innerHTML = '<img id="blah" src="#" width = 150 height = 200 style="margin:0 10px 10px 10px" alt="your image" />';
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

    var content = 1;
    $('#add_content').on('click', function(e) {
        var container=document.createElement('div');
        var content_title=document.createElement('div');
        var content_description=document.createElement('div');
        var content_images=document.createElement('div');
        var testinput=document.createElement('input');
        var separator=document.createElement('hr');
        var parent=document.getElementById('more_contents');
        separator.setAttribute("style","border-top: 1px solid blue")
        container.innerHTML='<h5 style="text-align: center">MINI PAGE ' + (1+content) + '<h5>';
        content_title.classList.add("form-group");
        content_description.classList.add("form-group");
        content_title.innerHTML='<label for="title" class="control-label">Mini Page Title</label><input class="form-control" placeholder="Title for specific content eg First Day Activities" name="more_title_' + content + '" type="text"><input type="hidden" name="content_pages[]" value="' + content +'">';
        content_description.innerHTML='<label for="description" class="control-label">Mini Page Description</label><textarea class="form-control" placeholder="Enter your description using either of the two conventions. <br>Description as a paragraph <br>Its a default, just enter statements and they will appear as a paragragh. <br>Description as Intro and list of statements. <br>Enter the 2 or 3 statements as usual for intro and statements to appear as listed, prefix them with hash character i.e # e.g (You will get the following:- #National Museum tour #Lunch at the white house)<br>Desciption as a list of statements without intro.<br> Just prefix all statements with # e.g (#National Museum tour #Lunch at the white house)" name="more_discription_' + content + '" cols="50" rows="10"></textarea>';
        content_images.innerHTML='<label for="more_images" class="control-label ">Images for This Page</label>';
        testinput.setAttribute('type','file');
        testinput.setAttribute('class','files');
        testinput.setAttribute('id',"content_" + content + "_files");
        testinput.setAttribute('name',"content_" + content + "_files[]");
        testinput.setAttribute('multiple','multiple');
        testinput.setAttribute('onchange',"newfunction(this);");
        //testinput.onchange = function("newfunction('content_" + content + "_files');"){}
        parent.appendChild(separator);
        parent.appendChild(container);
        parent.appendChild(content_title);
        parent.appendChild(content_description);
        parent.appendChild(content_images);
        parent.appendChild(testinput);
        ++content;
    });

</script>
