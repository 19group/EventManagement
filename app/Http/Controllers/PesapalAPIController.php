<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;
use Pesapal;
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
            /*$tracking_id = 'DHNC5849NDJ19'; $status = 'COMPLETED';
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
                'secondsToExpire' => $secondsToExpire,
                'is_embedded'     => $this->is_embedded,
            ];

        if ($this->is_embedded) {
            return view('Public.ViewEvent.Embedded.EventPageCheckout', $data);
        }

		if($status == "COMPLETED"){
		return view('Public.ViewEvent.EventPageCheckout2', $data);
		}
		else {
		return view ('Public.ViewEvent.EventPageCheckout');
		}



    }
}
