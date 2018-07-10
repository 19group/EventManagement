@extends('Public.ViewEvent.Layouts.EventPage')

@section('head')
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
@stop

@section('content')
    <div class="row bg-white">
<section id="tickets" class="container">

            {!! Form::open(['url' => route('postDirectPay', ['event_id' => $event->id])]) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="content">

<!-- //////////////////////////// Edit - Order - Section /////////////////////-->

<div class="row order_button">
</div>
  <h1 class='section_head'>Enter Amount to be paid</h1>
  <div class="row">
   <div class="col-md-12">
       <div class="form-group">
           {!! Form::label('amount', 'Amount', array('class'=>'font-weight-bold control-label required')) !!}
           {!!  Form::text('amount', Input::old('order_ref'),
       array(
       'class'=>'form-control',''
       ))  !!}
       </div>
   </div>
 </div>

<!-- //////////////////////////// End Edit - Order - Section /////////////////////-->

                        <div class="col-sm-12">
                        {!!Form::submit('Go To Pay', ['class' => 'btn btn-lg btn-primary pull-right'])!!}
                       </div>

                    </div> <!-- End Content -->

                </div>
            </div>
            {!! Form::close() !!}

</section>
</div>

    @include('Public.ViewEvent.Partials.EventDescriptionSection')
    @include('Public.ViewEvent.Partials.EventOrganiserSection')
    @include('Public.ViewEvent.Partials.EventFooterSection')
@stop
