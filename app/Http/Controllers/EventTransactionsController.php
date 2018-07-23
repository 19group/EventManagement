<?php

namespace App\Http\Controllers;

use App\Events\OrderCompletedEvent;
use App\Models\Affiliate;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Order;
use Illuminate\Support\Facades\URL;
use App\Coupon;
use Session;
use App\Acccommodation;
//use App\Models\Donation;
use App\Models\OrderItem;
use App\Models\QuestionAnswer;
use App\Models\ReservedTickets;
use App\Models\Ticket;
use Carbon\Carbon;
use Cookie;
use DB;
use Illuminate\Http\Request;
use Log;
use Omnipay;
use PDF;
use PhpSpec\Exception\Exception;
use Validator;
use Utils;

class EventTransactionsController extends Controller
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

    public function handleTransactions($event_id)
    {
        $transactionsOrder=['tickets','workshops','accommodation','pdfs','payments','complete'];
        $process=session()->get('transaction_'.$event_id);
        if(in_array($process,$transactionsOrder))
        {
            $finished=array_search($process, $transactionsOrder);
            if($finished==5){$finished=2;}
            $next=$transactionsOrder[++$finished];
            switch ($next) {
                case 'workshops':
                    return redirect(route('OrderWorkshops',['event_id'=>$event_id]));
                break;
                /*
                case 'sideevents':
                    return redirect(route('OrderSideEvents',['event_id'=>$event_id]));
                break;
                */
                case 'accommodation':
                    return redirect(route('OrderAccommodation',['event_id'=>$event_id]));
                break;
                case 'pdfs':
                    session()->set('transaction_'.$event_id,'pdfs');
                    $order_session = session()->get('ticket_order_' . $event_id);
                    $event=Event::findOrFail($event_id);
                    $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);
                    $data = $order_session + [
                        'event'           => Event::findorFail($order_session['event_id']),
                        'secondsToExpire' => $secondsToExpire,
                        'is_embedded'     => $this->is_embedded,
                        'previousurl' => URL::previous(),
                    ];
                    if ($this->is_embedded) {
                        return view('Public.ViewEvent.Embedded.EventPageCheckoutSuccess', $data);
                    }
                        return view('Public.ViewEvent.EventPageCheckoutSuccess', $data);
                break;
            /*    case 'payments':
                    return redirect(route('showEventCheckout',['event_id'=>$event_id]));
                break;*/
                default:
                    # code...
                    break;
            }
        }

        //return redirect(route('showEventCheckout',['event_id'=>$event_id]));
        return redirect(route('OrderAccommodation',['event_id'=>$event_id]));
    }

    public function showDirectPay($event_id)
    {
        $data = [
            'event'           => Event::findorFail($event_id)
        ];
        return view('Public.ViewEvent.EventDirectPayPage', $data);
    }

    public function postDirectPay(Request $request, $event_id)
    {
        if(!$request->has('first_name')){
            exit ('Please enter first name');
        }

        if(!$request->has('last_name')){
            exit('Please enter last name');
        }

        if(!$request->has('amount')){
            exit('Please enter correct amount');
        }

        $first_name = $request->get('first_name');
        $last_name  = $request->get('last_name');
        $amount     = $request->get('amount');
        $token = $this->encrypt_decrypt('encrypt', $first_name.'+'.$last_name.'+'.$amount);

//        return view('Public.ViewEvent.EventMidDirectPayPage', $data);
        return redirect(route('goDirectPay',['event_id'=>$event_id, 'token'=>$token]));
    }


    public function goDirectPay($event_id, $token)
    {
        $decrypt = $this->encrypt_decrypt('decrypt',$token);
        list($first_name, $last_name, $amount) = explode('+', $decrypt);

        $data = [
            'event' => Event::findOrFail($event_id),
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'token'      => $token,
            'amount'     => $amount
        ];
        return view('Public.ViewEvent.EventMidDirectPayPage', $data);
    }

    public function completedDirectPay($event_id, $token)
    {
        $decrypt = $this->encrypt_decrypt('decrypt',$token);
        list($first_name, $last_name, $amount) = explode('+', $decrypt);

        $data = [
            'event' => Event::findOrFail($event_id),
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'token'      => $token,
            'amount'     => $amount
        ];
        return view('Public.ViewEvent.EventSuccessDirectPayPage', $data);
    }

    public function paypalDirectPay($event_id, $token)
    {
        $decrypt = $this->encrypt_decrypt('decrypt',$token);
        list($first_name, $last_name, $amount) = explode('+', $decrypt);
        // PayPal settings

        $paypal_email = env('PAYPAL_EMAIL');//'user@domain.com';
        $return_url = env('SERVER_ROOT').'e/paypal/'.$event_id.'/paidwithtoken/'.$token;//.'/paypal/paymentsuccess/'.$payment_token;
        $cancel_url = env('SERVER_ROOT').'e/direct/'.$event_id.'/paywithtoken/'.$token; //checkout/create';
        $notify_url = env('SERVER_ROOT').'e/'.$event_id.'/paypal/notification';

        $item_name = 'FOSS4G 2018 Tickets Payment';//'Test Item';
        $item_amount = $amount;

        // Check if paypal request or response
        if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
            $querystring = '';

            // Firstly Append paypal account to querystring
            $querystring .= "?business=".urlencode($paypal_email)."&";

            //The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
            $querystring .= "item_name=".urlencode($item_name)."&";
            $querystring .= "amount=".urlencode($item_amount)."&";


            //loop for posted values and append to querystring (edited for autoredirecting)
            $POSTsubstitute["cmd"] = "_xclick";
            $POSTsubstitute["no_note"] = "1";
            $POSTsubstitute["first_name"] = 'Automated';
            $POSTsubstitute["last_name"] = 'Payment';
            $POSTsubstitute["payer_email"] = 'payments@studio19.co.tz';

            foreach($POSTsubstitute as $key => $value){
                $value = urlencode(stripslashes($value));
                $querystring .= "$key=$value&";
            }

            //append custom field
            //$querystring .= "custom=$custom&";

            // Append paypal return addresses
            $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
            $querystring .= "return=".urlencode(stripslashes($return_url))."&";
            $querystring .= "notify_url=".urlencode($notify_url);

            //event(new PaymentCompletedEvent(['payment_gateway'=>'paypal','event_id'=>$event_id]));

            return redirect(env('PAYPAL_HOST').$querystring);

        }
    }


    public function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

}
