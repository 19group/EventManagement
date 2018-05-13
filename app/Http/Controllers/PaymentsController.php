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
        $payments = new Payment;
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
    $return_url = env('SERVER_ROOT').'e/'.$event_id.'/paypal/paymentsuccess'.$payment_token;
    $cancel_url = env('SERVER_ROOT').'e/'.$event_id.'/checkout/create';
    $notify_url = env('SERVER_ROOT').'e/'.$event_id.'/paypal/notification';
    $cmd = "_cart";
    $upload = 1;


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
        $querystring .= "return=".urlencode(stripslashes($return_url))."&";
        $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
        $querystring .= "notify_url=".urlencode($notify_url);


        return redirect('https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);

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

    //[TODO] - Check if the token is getting set
    $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);
    $data = $order_session + [
            'event'           => Event::findorFail($order_session['event_id']),
            'secondsToExpire' => $secondsToExpire,
            'is_embedded'     => $this->is_embedded,
            'previousurl' => URL::previous(),
        ];
    return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);

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
 }



}
