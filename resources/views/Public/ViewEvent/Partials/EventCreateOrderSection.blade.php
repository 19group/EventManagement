<section id='order_form' class="row bg-white">
 <div class="container"><br><br>
  <h1 class="section_head">
   Order Details
  </h1>
 </div>
 <div class="container">
  <!-- Order Summary Page -->
  <div class="col-md-4 col-md-push-8">
   @include('Public.ViewEvent.Partials.OrderSummary')

   <div class="help-block">
    Please note you only have <span id='countdown'></span> to complete this transaction before your tickets are re-released.
   </div>
  </div>
  <div class="col-md-8 col-md-pull-4">
   <div class="event_order_form">
    <!--    {!! Form::open(['url' => route('postCreateOrder', ['event_id' => $event->id]),'class' => ($order_requires_payment && @$payment_gateway->is_on_site) ? 'ajax payment-form' : 'ajax','data-stripe-pub-key' => isset($account_payment_gateway->config['publishableKey']) ? $account_payment_gateway->config['publishableKey'] : '']) !!} -->
    {!! Form::open(['url' => route('postPayment', ['event_id' => $event->id]),'data-stripe-pub-key' => isset($account_payment_gateway->config['publishableKey']) ? $account_payment_gateway->config['publishableKey'] : '']) !!}
    {!! Form::hidden('event_id', $event->id) !!}

    <div class="row" style="display: none;">
     <div class="col-xs-6">
      <div class="form-group">
       {!! Form::label("order_first_name", 'First Name') !!}
       {!! Form::text("order_first_name", $first_name, ['required' => 'required', 'class' => 'form-control']) !!}
      </div>
     </div>
     <div class="col-xs-6">
      <div class="form-group">
       {!! Form::label("order_last_name", 'Last Name') !!}
       {!! Form::text("order_last_name", $last_name, ['required' => 'required', 'class' => 'form-control']) !!}
      </div>
     </div>
    </div>

    <div class="row" style="display: none;">
     <div class="col-md-12">
      <div class="form-group">
       {!! Form::label("order_email", 'Email') !!}
       {!! Form::text("order_email", $email, ['required' => 'required', 'class' => 'form-control']) !!}
      </div>
     </div>
    </div>

    <!--    <div class="p20 pl0">
    <a href="javascript:void(0);" class="btn btn-primary btn-xs" id="mirror_buyer_info">

   </a>
  </div>

 -->


 <div class="row">

  <div class="col-md-12">
   <div class="ticket_holders_details" >

    <?php
    $total_attendee_increment = 0;
    ?>
    <!--@foreach($tickets as $ticket)
    @for($i=0; $i<=$ticket['qty']-1; $i++)
    <div class="panel panel-primary">

    <div class="panel-heading">
    <h3 class="panel-title">
    <b>{{$ticket['ticket']['title']}}</b>: Ticket Holder {{$i+1}} Details
   </h3>
  </div>
  <div class="panel-body">
  <div class="row">
  <div class="col-md-6">
  <div class="form-group">
  {!! Form::label("ticket_holder_first_name[{$i}][{$ticket['ticket']['id']}]", 'First Name') !!}
  {!! Form::text("ticket_holder_first_name[{$i}][{$ticket['ticket']['id']}]", null, ['required' => 'required', 'class' => "ticket_holder_first_name.$i.{$ticket['ticket']['id']} ticket_holder_first_name form-control"]) !!}
 </div>
</div>
<div class="col-md-6">
<div class="form-group">
{!! Form::label("ticket_holder_last_name[{$i}][{$ticket['ticket']['id']}]", 'Last Name') !!}
{!! Form::text("ticket_holder_last_name[{$i}][{$ticket['ticket']['id']}]", null, ['required' => 'required', 'class' => "ticket_holder_last_name.$i.{$ticket['ticket']['id']} ticket_holder_last_name form-control"]) !!}
</div>
</div>
</div>
<div class="row">
<div class="col-md-12">
<div class="form-group">
{!! Form::label("ticket_holder_email[{$i}][{$ticket['ticket']['id']}]", 'Email Address') !!}
{!! Form::text("ticket_holder_email[{$i}][{$ticket['ticket']['id']}]", null, ['required' => 'required', 'class' => "ticket_holder_email.$i.{$ticket['ticket']['id']} ticket_holder_email form-control"]) !!}
</div>
</div>
@include('Public.ViewEvent.Partials.AttendeeQuestions', ['ticket' => $ticket['ticket'],'attendee_number' => $total_attendee_increment++])

</div>

</div>


</div>
@endfor
@endforeach-->
</div>
</div>
</div>

<style>
.offline_payment_toggle {
 padding: 20px 0;
}
</style>

@if($order_requires_payment)

<h3>Payment Information</h3>

@if($event->enable_offline_payments)
<div class="offline_payment_toggle">
 <div class="custom-checkbox">
  <input data-toggle="toggle" id="pay_offline" name="pay_offline" type="checkbox" value="1">
  <label for="pay_offline">Pay using offline method</label>
 </div>
</div>
<div class="offline_payment" style="display: none;">
 <h5>Offline Payment Instructions</h5>
 <div class="well">
  {!! Markdown::parse($event->offline_payment_instructions) !!}
 </div>
</div>

@endif


<!-- Stripe -->
@if(@$payment_gateway->id==1)
<div class="row">
 <label class="col-md-12 text-center"><h3>Stripe</h3></label>


 <div class="col-md-6">
  <div class="form-group">
   {!! Form::label('first_name', 'First Name', array('class'=>'control-label required')) !!}
   {!!  Form::text('first_name', Input::old('first_name'),
   array(
   'class'=>'form-control'
   ))  !!}
  </div>
 </div>
 <div class="col-md-6">
  <div class="form-group">
   {!! Form::label('last_name', 'Last Name', array('class'=>'control-label required')) !!}
   {!!  Form::text('last_name', Input::old('last_name'),
   array(
   'class'=>'form-control'
   ))  !!}
  </div>
 </div>
</div>
<div class="row">
 <div class="col-md-6">
  <div class="form-group">
   {!! Form::label('email', 'Email', array('class'=>'control-label required')) !!}
   {!!  Form::text('email', Input::old('email'),
   array(
   'class'=>'form-control'
   ))  !!}
  </div>
 </div>
</div>


@endif




<!-- PayPal -->
@if(@$payment_gateway->id==2)
<!--div class="row">
<label class="col-md-12 text-center"><h3>PayPal</h3></label>

<div class="col-md-6">
<div class="form-group">
{!! Form::label('first_name', 'First Name', array('class'=>'control-label required')) !!}
{!!  Form::text('first_name', Input::old('first_name'),
array(
'class'=>'form-control'
))  !!}
</div>
</div>
<div class="col-md-6">
<div class="form-group">
{!! Form::label('last_name', 'Last Name', array('class'=>'control-label required')) !!}
{!!  Form::text('last_name', Input::old('last_name'),
array(
'class'=>'form-control'
))  !!}
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
{!! Form::label('payer_email', 'Email', array('class'=>'control-label required')) !!}
{!!  Form::text('payer_email', Input::old('payer_email'),
array(
'class'=>'form-control'
))  !!}
</div>
</div>
</div-->
<ul class="nav nav-tabs" id="myTab" role="tablist">
 <li class="nav-item">
  <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
  <img alt="PayPal" width="120px" height="32px" src="{{asset('assets/images/public/EventPage/paypal/paypal.svg')}}"></a> </li>
</ul>
<div class="tab-content" id="myTabContent">
 <div class="tab-pane fade show active" id="paypalcontainer" role="tabpanel" aria-labelledby="home-tab">
  <p>After payment via PayPal's secure checkout,You will be provided with tickets for your payments</p>
<br />
  <div>
   <p>Paypal Accepts</p>
   <ul>
    <li>
     <img alt="Visa" title="Visa" width="48px" height="30px" src="{{asset('assets/images/public/EventPage/paypal/visa.svg')}}">
     <img alt="MasterCard" title="MasterCard" width="48px" height="30px" src="{{asset('assets/images/public/EventPage/paypal/mastercard.svg')}}">
     <img alt="American Express" title="American Express" width="48px" height="30px" src="{{asset('assets/images/public/EventPage/paypal/amex.svg')}}">
     <img alt="Discover" title="Discover" width="48px" height="30px" src="{{asset('assets/images/public/EventPage/paypal/discover.svg')}}">
     <img alt="China UnionPay" title="China UnionPay" width="48px" height="30px" src="{{asset('assets/images/public/EventPage/paypal/unionpay.svg')}}">
    </li>
   </ul>

  </div>
 </div>
</div>

@php $itemcount=1; $fromsession=session()->get('ticket_order_'.$event->id);@endphp
{!!Form::hidden("cmd","_xclick")!!}
{!!Form::hidden("no_note","1")!!}
<!--{!!Form::hidden("bn","PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest")!!}
<!--{!!Form::hidden("bn","PP-BuyNowBF:btn_paynowCC_LG.gif:NonHostedGuest")!!}-->
{!!Form::hidden("first_name",$fromsession['first_name'])!!}
{!!Form::hidden("last_name",$fromsession['last_name'])!!}
{!!Form::hidden("payer_email",$fromsession['email'])!!}
{!! Form::submit('Checkout with Paypal', ['class' => 'btn btn-lg btn-success card-submit', 'style' => 'width:100%;']) !!}
<!--    @foreach($fromsession['tickets'] as $prioritems)
{!!Form::hidden("item_name_".$itemcount,$prioritems['ticket']['title'])!!}
{!!Form::hidden("amount_".$itemcount,$prioritems['full_price'])!!}
{!!Form::hidden("quantity_".$itemcount,$prioritems['qty'])!!}
@php ++$itemcount;@endphp
@endforeach
@if($fromsession['donation']>0)
{!!Form::hidden("item_name_".$itemcount,'Donation')!!}
{!!Form::hidden("amount_".$itemcount,$fromsession['donation'])!!}
@php ++$itemcount; @endphp
@endif-->
<!--form class="paypal" action="payments.php" method="post" id="paypal_form" target="_blank"-->
<!--    <form action="<?php //route('postCreateOrder', ['event_id' => $event->id])?>", 'class' = "<?php //($order_requires_payment && @$payment_gateway->is_on_site) ? 'ajax payment-form' : 'ajax'?>" method="post" id="paypal_form" target="_blank">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="lc" value="UK" />
<input type="hidden" name="currency_code" value="GBP" />
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />
<input type="hidden" name="first_name" value="Customer's First Name"  />
<input type="hidden" name="last_name" value="Customer's Last Name"  />
<input type="hidden" name="payer_email" value="customer@example.com"  />
<input type="hidden" name="item_number" value="123456" / >
<input type="submit" name="submit" value="Submit Payment"/>
</form-->


@endif






<!-- Coinbae -->
@if(@$payment_gateway->id==3)
<div class="row">
 <label class="col-md-12 text-center"><h3>CoinBase</h3></label>
 <div class="col-md-6">
  <div class="form-group">
   {!! Form::label('first_name', 'SECRET CODE', array('class'=>'control-label required')) !!}
   {!!  Form::text('first_name', Input::old('first_name'),
   array(
   'class'=>'form-control'
   ))  !!}
  </div>
 </div>
 <div class="col-md-6">
  <div class="form-group">
   {!! Form::label('last_name', 'ACCOUNT ID', array('class'=>'control-label required')) !!}
   {!!  Form::text('last_name', Input::old('last_name'),
   array(
   'class'=>'form-control'
   ))  !!}
  </div>
 </div>
</div>
@endif

<!-- Master Card Payment -->
@if(@$payment_gateway->id==4)
<div class="online_payment">
 <div class="row">
  <label class="col-md-12 text-center"><h3>MasterCard Payment</h3></label>
  <div class="col-md-12">
   <div class="form-group">
    {!! Form::label('card-number', 'Card Number') !!}
    <input required="required" type="text" autocomplete="off" placeholder="**** **** **** ****" class="form-control card-number" size="20" data-stripe="number">
   </div>
  </div>
 </div>
 <div class="row">
  <div class="col-xs-6">
   <div class="form-group">
    {!! Form::label('card-expiry-month', 'Expiry Month') !!}
    {!!  Form::selectRange('card-expiry-month',1,12,null, [
    'class' => 'form-control card-expiry-month',
    'data-stripe' => 'exp_month'
    ] )  !!}
   </div>
  </div>
  <div class="col-xs-6">
   <div class="form-group">
    {!! Form::label('card-expiry-year', 'Expiry Year') !!}
    {!!  Form::selectRange('card-expiry-year',date('Y'),date('Y')+10,null, [
    'class' => 'form-control card-expiry-year',
    'data-stripe' => 'exp_year'
    ] )  !!}</div>
   </div>
  </div>
  <div class="row">
   <div class="col-md-12">
    <div class="form-group">
     {!! Form::label('card-expiry-year', 'CVC Number') !!}
     <input required="required" placeholder="***" class="form-control card-cvc" data-stripe="cvc">
    </div>
   </div>
  </div>
 </div>

 @endif

 <!-- PesaPal -->
 @if(@$payment_gateway->id==5)
 <div class="row">
  <label class="col-md-12 text-center"><h3>PesaPal</h3></label>

  @include('Public.ViewEvent.Partials.OAuth')

  <?php

  $token = $params = NULL;

  /*
  PesaPal Sandbox is at http://demo.pesapal.com. Use this to test your developement and
  when you are ready to go live change to https://www.pesapal.com.
  */
  $consumer_key = env('PESAPAL_CONSUMER_KEY');//Register a merchant account on
  //demo.pesapal.com and use the merchant key for testing.
  //When you are ready to go live make sure you change the key to the live account
  //registered on www.pesapal.com!
  $consumer_secret = env('PESAPAL_CONSUMER_SECRET');// Use the secret from your test
  //account on demo.pesapal.com. When you are ready to go live make sure you
  //change the secret to the live account registered on www.pesapal.com!
  $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
  $iframelink = env('IFRAME_URL');//change to
  //https://www.pesapal.com/API/PostPesapalDirectOrderV4 when you are ready to go live!

  //get form details
  $amount = $order_total  + $donation;
  $amount = number_format($amount, 2);//format amount to 2 decimal places

  $desc = 'Tickets';
  $type = 'MERCHANT'; //default value = MERCHANT
  $reference = $event_id.$order_started.$expires;//unique order id of the transaction, generated by merchant
  $first_name = $first_name;
  $last_name = $last_name;
  $email = $email;
  $phonenumber = '';//ONE of email or phonenumber is required

  $currency = env('PESAPAL_CURRENCY_CODE');

  $callback_url = env('SERVER_ROOT').'e/'.$event_id.'/pesament/create?is_embedded=0#order_form'; //redirect url, the page that will handle the response from pesapal.

  $post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" Currency=\"".$currency."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" xmlns=\"http://www.pesapal.com\" />";
  $post_xml = htmlentities($post_xml);

  $consumer = new OAuthConsumer($consumer_key, $consumer_secret);

  //post transaction to pesapal
  $iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
  $iframe_src->set_parameter("oauth_callback", $callback_url);
  $iframe_src->set_parameter("pesapal_request_data", $post_xml);
  $iframe_src->sign_request($signature_method, $consumer, $token);



  ?>

  <iframe src="<?php echo $iframe_src;?>" width="100%" height="650px"  scrolling="no" frameBorder="0">
   <p>Browser unable to load iFrame</p>
  </iframe>

 </div>


 @endif

 @endif

 @if($event->pre_order_display_message)
 <div class="well well-small">
  {!! nl2br(e($event->pre_order_display_message)) !!}
 </div>
 @endif

 {!! Form::hidden('is_embedded', $is_embedded) !!}
 <!--
 {!! Form::submit('Checkout', ['class' => 'btn btn-lg btn-success disabled card-submit', 'style' => 'width:100%;']) !!}
-->
<br>
<br>

<!--
<center>
<p>Finalize payment before you can Check Out...</p>
</center>
-->

</div>
</div>
</div>
</section>
@if(session()->get('message'))
<script>showMessage('{{session()->get('message')}}');</script>
@endif
