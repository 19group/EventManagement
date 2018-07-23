@extends('Public.ViewEvent.Layouts.EventPage')

@section('head')
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
@stop

@section('content')
    <div class="row bg-white">
<section id="tickets" class="container">

            <div class="row">
                <div class="col-md-12">

                    <div class="content">

<!-- //////////////////////////// Edit - Order - Section /////////////////////-->

<div class="row order_button">
</div>
  <h1 class='section_head'>Direct Payment</h1>
  <h4> Hello {{$first_name}}  {{$last_name}}, </h4>
  <h4>Foss4g committee kindly requests you to pay USD {{$amount}} for your conference participation</h4>


  


        <a class="nav-button" target="_blank" href="{{route('paypalDirectPay', ['event_id' => $event->id, 'token'=>$token])}}">
            <span>
                Proceed to Payment
            </span>
        </a>
<!-- //////////////////////////// End Edit - Order - Section /////////////////////-->

                    </div> <!-- End Content -->

                </div>
            </div>

</section>
</div>

    @include('Public.ViewEvent.Partials.EventDescriptionSection')
    @include('Public.ViewEvent.Partials.EventOrganiserSection')
    @include('Public.ViewEvent.Partials.EventFooterSection')
@stop
