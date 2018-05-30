<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Pesapal;
use Illuminate\Support\Facades\Auth;
use App\Payment;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Dotenv;
use App\Events\PaymentCompletedEvent;

class PaymentsController extends Controller
{
 /**
  * Is the checkout in an embedded Iframe?
  *
  * @var bool
  */
 protected $is_embedded;


     /**
  * EventCheckoutController constructor.
  * @param Request $request
  */
 public function __construct(Request $request)
 {
     /*
      * See if the checkout is being called from an embedded iframe.
      */
     $this->is_embedded = $request->get('is_embedded') == '1';
 }

    //
public function payment(){//initiates payment
        $payments = new Payment();
        $payments -> businessid = Auth::guard('business')->id(); //Business ID
        $payments -> transactionid = Pesapal::random_reference();
        $payments -> status = 'NEW'; //if user gets to iframe then exits, i prefer to have that as a new/lost transaction, not pending
        $payments -> amount = 10;
        $payments -> save();

        $details = array(
            'amount' => $payments -> amount,
            'description' => 'Test Transaction',
            'type' => 'MERCHANT',
            'first_name' => 'Fname',
            'last_name' => 'Lname',
            'email' => 'test@test.com',
            'phonenumber' => '254-723232323',
            'reference' => $payments -> transactionid,
            'height'=>'400px',
            //'currency' => 'USD'
        );
        $iframe=Pesapal::makePayment($details);

        return view('payments.business.pesapal', compact('iframe'));
    }

    public function paymentsuccess(Request $request)//just tells u payment has gone thru..but not confirmed
    {
        $trackingid = $request->input('tracking_id');
        $ref = $request->input('merchant_reference');

        $payments = Payment::where('transactionid',$ref)->first();
        $payments -> trackingid = $trackingid;
        $payments -> status = 'PENDING';
        $payments -> save();
        //go back home
        $payments=Payment::all();
        return view('payments.business.home', compact('payments'));
    }
    //This method just tells u that there is a change in pesapal for your transaction..
    //u need to now query status..retrieve the change...CANCELLED? CONFIRMED?
    public function paymentconfirmation(Request $request)
    {
        $trackingid = $request->input('pesapal_transaction_tracking_id');
        $merchant_reference = $request->input('pesapal_merchant_reference');
        $pesapal_notification_type= $request->input('pesapal_notification_type');

        //use the above to retrieve payment status now..
        $this->checkpaymentstatus($trackingid,$merchant_reference,$pesapal_notification_type);
    }
    //Confirm status of transaction and update the DB
    public function checkpaymentstatus($trackingid,$merchant_reference,$pesapal_notification_type){
        $status=Pesapal::getMerchantStatus($trackingid, $merchant_reference);
        //$payments = Payment::where('trackingid',$trackingid)->first();
        //$payments -> status = $status;
        //$payments -> payment_method = "PESAPAL";//use the actual method though...
        //$payments -> save();

        //return "success";

        return view('Public.ViewEvent.Partials.EventCreateOrderSection');


    }

    public function postPayment(Request $request, $event_id){

     /*
      * Check if the user has chosen to pay offline
      * and if they are allowed
      */
     if ($request->get('pay_offline') && $event->enable_offline_payments) {

      //Redirect to Creating Tickets
      return response()->redirectToRoute('postCreateOrder', [
          'event_id'             => $event_id,
      ]);
     }
     try {
        $ticket_order = session()->get('ticket_order_' . $event_id);
        //[TOCHECK] - is there a need to call events from here
        $event = Event::findOrFail($event_id);
         $transaction_data = [
                 'amount'      => ($ticket_order['order_total'] + $ticket_order['organiser_booking_fee']),
                 'currency'    => $event->currency->code,
                 'description' => 'Order for customer: ' . $request->get('order_email'),
             ];
//$forceway=2;
         switch ($ticket_order['payment_gateway']->id) {
         //switch($forceway){
             case config('attendize.payment_gateway_paypal'):
//------------------------paypal-------------------------------------------------------


//[TO-CHECK] -- What does this do? the if statement below?
//     if(substr(URL::previous(),-22)!=='/paypal/paymentsuccess'){
    //$ticket_order = session()->get('ticket_order_' . $event_id);
    $payment_token=substr(session()->getId(),0,10).$ticket_order['order_started'].substr(session()->getId(), 10);
    session()->set('ticket_order_' . $event_id . '.paymenttoken',$payment_token);

    // PayPal settings

    $paypal_email = env('PAYPAL_EMAIL');//'user@domain.com';
    $return_url = env('SERVER_ROOT').'e/'.$event_id.'/paypal/paymentsuccess/'.$payment_token;
    $cancel_url = env('SERVER_ROOT').'e/'.$event_id.'/checkout/create';
    $notify_url = env('SERVER_ROOT').'e/'.$event_id.'/paypal/notification';
///    $cmd = "_cart";
///    $upload = 1;


    $items=[]; $itemcount=0; //dd($ticket_order['tickets']);

    $item_name = 'FOSS4G 2018 Tickets Payment';//'Test Item';
    $item_amount = $ticket_order['donation'] + $ticket_order['order_total']; //5.00;

    // Check if paypal request or response
    if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
        $querystring = '';

        // Firstly Append paypal account to querystring
        $querystring .= "?business=".urlencode($paypal_email)."&";

        //The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
        $querystring .= "item_name=".urlencode($item_name)."&";
        $querystring .= "amount=".urlencode($item_amount)."&";


        //loop for posted values and append to querystring
        foreach($_POST as $key => $value){
            $value = urlencode(stripslashes($value));
            $querystring .= "$key=$value&";
        }

        // Append paypal return addresses
        $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
        $querystring .= "return=".urlencode(stripslashes($return_url))."&";
        $querystring .= "notify_url=".urlencode($notify_url);

        //event(new PaymentCompletedEvent(['payment_gateway'=>'paypal','event_id'=>$event_id]));

        return redirect(env('PAYPAL_HOST').$querystring);

        //header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
        //exit();
}
//------------------------------end-paypal---------------------------------------------

             case config('attendize.payment_gateway_coinbase'):
                 $transaction_data += [
                     'cancelUrl' => route('showEventCheckoutPaymentReturn', [
                         'event_id'             => $event_id,
                         'is_payment_cancelled' => 1
                     ]),
                     'returnUrl' => route('showEventCheckoutPaymentReturn', [
                         'event_id'              => $event_id,
                         'is_payment_successful' => 1
                     ]),
                     'brandName' => isset($ticket_order['account_payment_gateway']->config['brandingName'])
                         ? $ticket_order['account_payment_gateway']->config['brandingName']
                         : $event->organiser->name
                 ];
                 break;
             case config('attendize.payment_gateway_stripe'):
                 $token = $request->get('stripeToken');
                 $transaction_data += [
                     'token'         => $token,
                     'receipt_email' => $request->get('order_email'),
                 ];
                 break;
             case config('attendize.payment_gateway_migs'):
                 $transaction_data += [
                     'transactionId' => $event_id . date('YmdHis'),       // TODO: Where to generate transaction id?
                     'returnUrl' => route('showEventCheckoutPaymentReturn', [
                         'event_id'              => $event_id,
                         'is_payment_successful' => 1
                     ]),
                 ];
                 // Order description in MIGS is only 34 characters long; so we need a short description
                 $transaction_data['description'] = "Ticket sales " . $transaction_data['transactionId'];
                 break;
             case config('attendize.payment_gateway_pesapal'):
                 $transaction_data += [
                     'pesapal_transaction_tracking_id'=> session()->get('tracking_id'),
                     'pesapal_merchant_reference' => session()->get('merchant_reference'),
                 ];
                 break;
             default:
                 Log::error('No payment gateway configured.');
                 return repsonse()->json([
                     'status'  => 'error',
                     'message' => 'No payment gateway configured.'
                 ]);
                 break;
         }
         /*
         $transaction = '{';
         foreach ($transaction_data as $key => $value) {
             $transaction = $transaction.'"'.$key.'":"'.$value.'",';
         }

         $transaction = substr($transaction,0,strlen($transaction)-1).'}';
         $transaction = $gateway->purchase($transaction_data);
         $response = $transaction->send();
         if ($response->isSuccessful()) {
             session()->push('ticket_order_' . $event_id . '.transaction_id',
                 $response->getTransactionReference());
             session()->push('ticket_order_' . $event_id . '.transaction_id', session()->get('tracking_id'));

             return $this->completeOrder($event_id);

 */
   } catch (\Exeption $e) {
         Log::error($e);
         $error = 'Sorry, there was an error processing your payment. Please try again.';
     }
 //}
//     }

 }

 public function paypalSuccess(Request $request, $event_id,$payment_token){
     $event=Event::findOrFail($event_id);
     $order_session = session()->get('ticket_order_' . $event_id);

     session()->set('transaction_'.$event_id,'complete');

     /*
      * check if payment_token is the one created from current session variables
      * problem::can still be tricked for adding items to the paid order
      */
    //[TODO] - Check if the token is getting set and unset for correct security
    if($payment_token==substr(session()->getId(),0,10).$order_session['order_started'].substr(session()->getId(), 10)){
        $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);
        $data = $order_session + [
                'event'           => Event::findorFail($order_session['event_id']),
                'secondsToExpire' => $secondsToExpire,
                'is_embedded'     => $this->is_embedded,
                'previousurl' => URL::previous(),
            ];
        return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);
    }else{
         $data = [
             'event' => $event,
             'callbackurl' => null,
             'messages' => 'Sorry, the page you are looking for doesn\'t exist. If you have been redirected here after payment, please contact the organiser for a follow up.',
             'request_details' => null,
             'parameters' => null
         ];
         return view('Public.ViewEvent.EventPageErrors', $data);
    }
    if(!isset($order_session['paymenttoken'])){
         //exit('Sorry, your payment couldn\'t be verified. Contact the organiser');
         $data = [
             'event' => $event,
             'callbackurl' => null,
             'messages' => 'Sorry, your payment couldn\'t be verified. Contact the organiser.',
             'request_details' => null,
             'parameters' => null
         ];
         return view('Public.ViewEvent.EventPageErrors', $data);
    }

     if($payment_token==$order_session['paymenttoken'][0]){
     //$event=Event::findOrFail($event_id);
     $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);
     $data = $order_session + [
             'event'           => Event::findorFail($order_session['event_id']),
             'secondsToExpire' => $secondsToExpire,
             'is_embedded'     => $this->is_embedded,
             'previousurl' => URL::previous(),
         ];
     session()->unset('ticket_order'.$event_id.'paymenttoken');

     if ($this->is_embedded) {
         return view('Public.ViewEvent.Embedded.EventPageCheckoutSuccess', $data);
     }
         return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);
     }else{
         //exit('Sorry, couldn\'t verify your payment');
         $data = [
             'event' => $event,
             'callbackurl' => null,
             'messages' => 'Sorry, your payment couldn\'t be verified. Contact the organiser',
             'request_details' => null,
             'parameters' => null
         ];
         return view('Public.ViewEvent.EventPageErrors', $data);
     }
 }

public function paypalNotification(Request $request, $event_id){
     //dd($request);
     $raw_post_data = file_get_contents('php://input');
     $raw_post_array = explode('&', $raw_post_data);
     $myPost = array();

     foreach ($raw_post_array as $keyval) {
      $keyval = explode ('=', $keyval);
      if (count($keyval) == 2)
      $myPost[$keyval[0]] = urldecode($keyval[1]);
     }
     // read the post from PayPal system and add 'cmd'
     $req = 'cmd=_notify-validate';
     if(function_exists('get_magic_quotes_gpc')) {
      $get_magic_quotes_exists = true;
     }
     foreach ($myPost as $key => $value) {
      if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
       $value = urlencode(stripslashes($value));
      } else {
       $value = urlencode($value);
      }
      $req .= "&$key=$value";
     }

    //dd($myPost);
     // STEP 2: Post IPN data back to paypal to validate

     //$ch = curl_init('https://www.paypal.com/cgi-bin/webscr'); // change to [...]sandbox.paypal[...] when using sandbox to test
     $ch = curl_init(env('PAYPAL_HOST'));
     curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
     curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

     // In wamp like environments that do not come bundled with root authority certificates,
     // please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
     // of the certificate as shown below.
     // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
     if( !($res = curl_exec($ch)) ) {
      // error_log("Got " . curl_error($ch) . " when processing IPN data");
      curl_close($ch);
      exit;
     }
     curl_close($ch);


     // STEP 3: Inspect IPN validation result and act accordingly
     $custom = json_encode($myPost);
     $item_name = $myPost['item_name'];
     $first_name = $myPost['first_name'];
     $last_name = $myPost['first_name'];
     $payment_date = $myPost['payment_date'];
     $item_number = $myPost['item_number'];
     $payment_status = $myPost['payment_status'];
     if ($myPost['mc_gross'] != NULL)
     $payment_amount = $myPost['mc_gross'];
     else
     $payment_amount = $myPost['mc_gross1'];
     $payment_currency = $myPost['mc_currency'];
     $txn_id = $myPost['txn_id'];
     $receiver_email = $myPost['receiver_email'];
     $payer_email = $myPost['payer_email'];

//      $ticket_order = session()->get("ticket_order_".$event_id);

     //dd($ticket_order);

    //[TODO] Needs to be changed, pass this information through custom field 

      $order_amount = 10000;
      $order_details["first_name"] =  "Test";
      $order_details["last_name"] =  "Test";
      $order_details["order_total"] =  0;
      $order_details["donation"] =  0;
      $order_details["email"] =  "test@test.com";
      $order_details["coupon_flag"] = "0";

      //$tickets = $ticket_order["tickets"];

      //dd($tickets);
/*
       $i = 0;
       foreach ($tickets as $ticket) {
          $bought_tickets[$i]["ticket_title"]= $ticket["ticket"]['title'];
          $bought_tickets[$i]["ticket_price"]= $ticket["full_price"];
          $bought_tickets[$i]["ticket_quantity"]= $ticket["qty"];
          if(isset($ticket["dates"])){
          $bought_tickets[$i]["dates"]= $ticket["dates"];
          }

          ++$i;
       }
*/
       $bought_tickets = "tickets with paypal";
       $order_details = json_encode($order_details);

     // Verifies that the IPN is from paypal
     if (strcmp ($res, "VERIFIED") == 0) {
      //sendSuccessEmail();
       $paypal_verified = 1;
       $transaction_approved = 0;
       //$this->paymentconfirmed($event_id,$payment_success_status);

      //Payment Is Successful, do something here

      //[TODO] check whether the payment_status is Completed
      //[TODO] check that txn_id has not been previously processed
      //[TODO] check that receiver_email is your Primary PayPal email
      //[TODO] check that payment_amount/payment_currency are correct
      //[TODO] process payment
      //[TODO] Save Txn-id in the session

      $payment = new Payment();
      $payment->full_name = $first_name;
      $payment->payer_email = $payer_email;
      $payment->receiver_email = $receiver_email;
      $payment->payment_status = $payment_status;
      $payment->amount = $payment_amount;
      $payment->currency = $payment_currency;
      $payment->payment_date = $payment_date;
      $payment->txn_id = $txn_id;
      $payment->custom = $custom;
      $payment->bought_tickets = $bought_tickets;
      $payment->order_details = $order_details;
      $payment->paypal_verified = $paypal_verified;
      $payment->transaction_approved =  $transaction_approved;
      $payment->save();

      // Insert your actions here
      //dd("Payment is from paypal");

     } else if (strcmp ($res, "INVALID") == 0) {
      // log for manual investigation
      $paypal_verified = 0;
      $transaction_approved = 0;


      $payment = new Payment();
      $payment->full_name = $first_name;
      $payment->payer_email = $payer_email;
      $payment->receiver_email = $receiver_email;
      $payment->payment_status = $payment_status;
      $payment->amount = $payment_amount;
      $payment->currency = $payment_currency;
      $payment->payment_date = $payment_date;
      $payment->txn_id = $txn_id;
      $payment->custom = $custom;
      $payment->bought_tickets = $bought_tickets;
      $payment->order_details = $order_details;
      $payment->paypal_verified = $paypal_verified;
      $payment->transaction_approved =  $transaction_approved;
      $payment->save();


      //dd("Payment is not from Paypal");
      $payment_success_status = 0;
      //$this->paymentconfirmed($event_id,$payment_success_status);
     }else{

       // log for manual investigation
       $paypal_verified = 3;
       $transaction_approved = 0;


       $payment = new Payment();
       $payment->full_name = $first_name;
       $payment->payer_email = $payer_email;
       $payment->receiver_email = $receiver_email;
       $payment->payment_status = $payment_status;
       $payment->amount = $payment_amount;
       $payment->currency = $payment_currency;
       $payment->payment_date = $payment_date;
       $payment->txn_id = $txn_id;
       $payment->custom = $custom;
       $payment->bought_tickets = $bought_tickets;
       $payment->order_details = $order_details;
       $payment->paypal_verified = $paypal_verified;
       $payment->transaction_approved =  $transaction_approved;
       $payment->save();


       //dd("Payment is not from Paypal");
       $payment_success_status = 0;
       //$this->paymentconfirmed($event_id,$payment_success_status);

     }

 }

 public function paymentconfirmed($event_id,$payment_success_status){

  $order_session = session()->get('ticket_order_' . $event_id);
  $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);
  $data = $order_session + [
   //'event'           => Event::findorFail($order_session['event_id']),
   'secondsToExpire' => $secondsToExpire,
   'is_embedded'     => $this->is_embedded,
   //'previousurl' => URL::previous(),
  ];


  if($payment_success_status == 1){
   //dd("I am here successfully");
  return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);
  }
  else{
   //dd("I am here unsuccessful");
   session()->flash('message', 'Payment has failed, please make sure that all details are correct, and try again');
   // dd("I am here");
   //dd(" i am not successful at payment");
   return view ('Public.ViewEvent.EventPageCheckout',$data);
   }
  //return view()
 }



}
