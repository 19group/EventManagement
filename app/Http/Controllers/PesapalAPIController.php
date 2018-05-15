<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;
use Pesapal;
use Studio19\Pesapal\Exceptions\PesapalException;
use App\Http\Requests;
use Carbon\Carbon;


class PesapalAPIController extends Controller
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
     function handleCallback()
    {
        $merchant_reference = Input::get('pesapal_merchant_reference');
        $tracking_id = Input::get('pesapal_transaction_tracking_id');
    //Donald Feb9    $route = config('pesapal.callback_route');
        $route = 'confirm';
        session()->push('ticket_order_' . $event_id . '.transaction_id',
                        $tracking_id);
        return redirect()->route($route,
            array('tracking_id' => $tracking_id, 'merchant_reference' => $merchant_reference));
    }

    function handleIPN(Request $request, $event_id)
    {
        if (/*Input::has('pesapal_notification_type') && */Input::has('pesapal_merchant_reference') && Input::has('pesapal_transaction_tracking_id')) {
            //$notification_type = Input::get('pesapal_notification_type');
												$notification_type = "CHANGE";
            $merchant_reference = Input::get('pesapal_merchant_reference');
            $tracking_id = Input::get('pesapal_transaction_tracking_id');

           $response =  Pesapal::getTransactionStatus($notification_type, $merchant_reference, $tracking_id);

											$status = $response['status'];

        } else {
            //uncomment this (next two lines) for testing without pesapal
            /*$tracking_id = 'DHNC5849NDJ19'; $status = 'FAILED';
            goto skip;*/
            throw new PesapalException("incorrect parameters in request");
            skip:
        }

        session()->push('ticket_order_' . $event_id . '.transaction_id',
                        $tracking_id);
        //return view('Public.ViewEvent.Partials.EventCreateOrderSection2');
        //dd($merchant_reference,$tracking_id);

        $order_session = session()->get('ticket_order_' . $event_id);

        $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);

        $data = $order_session + [
                'event'           => Event::findorFail($order_session['event_id']),
                'secondsToExpire' => 120,
                'is_embedded'     => $this->is_embedded,
            ];

        if ($this->is_embedded) {
            return view('Public.ViewEvent.Embedded.EventPageCheckout', $data);
        }

								if($status == "COMPLETED"){
								return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);
								}                                
                                elseif ($status == "CANCELLED") {
                                    session()->flash('message', 'Payment has been cancelled, make sure card details are correct, and please try the payment again');
                              return view ('Public.ViewEvent.EventPageCheckout',$data);
                                }
                                elseif ($status == "PENDING"){
                                    session()->flash('message', 'Payment is still Pending Verification, please refresh the page in a short while');
                                    //return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);
                                }
								elseif ($status == "FAILED") {
							//		session()->flash('message', 'Payment has failed, please check your email first, before continuing with another payment, most cases your bank has rejected the payment, thus a confirmation with your bank is sometimes required.');
									//alert("Payment has failed, please retry the payment again");
							//  return view ('Public.ViewEvent.EventPageCheckout',$data);

                $customerdetails=session()->get('ticket_order_'.$event_id);
                $body = "Hello Technical Team at Studio19<br/>";
              //$body = "Dear Brian Paul <br/><br/>";
                $body .= "We are sadly presenting a FAILED payment transaction by one of FOSS4G 2018 registration candidate<br/>";
              $body .= "Firstname : ".$customerdetails['first_name']."<br/>";
              $body .= "Lastname : ".$customerdetails['last_name']."<br/>";
              $body .= "Email : ".$customerdetails['email']."<br/>";
              if($customerdetails['donation']>0){
                $body .= "Donated amount : ".$customerdetails['donation']."<br/>";
              }
              $body .= "Transaction Amount : ".$customerdetails['order_total']."<br/><br />";
              $body .= "Please contact the customer for correct guidance.<br/>";

              $body .= "FOSS4G 2018 Dar es Salaam Local Organising Committee (DLOC)<br />";
              $body .= "registration@foss4g.or.tz<br /> <br />";
            include "PHPMailer.php"; // include the class name
             $mail = new PHPMailer(); // create a new object
             $mail->IsSMTP(); // enable SMTP

             $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
             $mail->SMTPAuth = true; // authentication enabled
             $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
             $mail->Host = env('MAIL_HOST');
             $mail->Port = env('MAIL_PORT');
             $mail->IsHTML(true);
             $mail->Username = env('MAIL_FROM_ADDRESS');
             $mail->Password = env('MAIL_PASSWORD');
             $mail->SetFrom(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
             $mail->Subject = "FAILED Pesapal Transaction During FOSS4G 2018 Registration";
             $mail->Body = $body;
             $mail->AddAddress("events@studio19.co.tz");
            $mail->Send();

            $errormessage[] = "We are sorry that your transaction has failed. This is a normal unfortunate situation and we understand you will be back on track if you review our highlights of common causes below:-";
            $errormessage[] = "Decline of the transaction by the bank of card holder due to suspicion on the transaction.";
            $errormessage[] = "Expiration of the card.";
            $errormessage[] = "Insufficient funds in the account.";
            $errormessage[] = "We thus recommend you contact your bank for further details.";
            $errordata = [
                'event' => Event::findOrFail($event_id),
                'callbackurl' => 'createorder',
                'messages' => $errormessage,
                'request_details' => null,
                'parameters' => ['event_id' => $event_id]
            ];
            return view('Public.ViewEvent.EventPageErrors', $errordata);


								}



    }
}
