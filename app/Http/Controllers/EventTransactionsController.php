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
        $transactionsOrder=['tickets','workshops','sideevents','accommodation'];
        $process=session()->get('transaction_'.$event_id);
        if(in_array($process,$transactionsOrder))
        {
            $finished=array_search($process, $transactionsOrder);
            $next=$transactionsOrder[++$finished];
            switch ($next) {
                case 'workshops':
                    return redirect(route('OrderWorkshops',['event_id'=>$event_id]));
                break;    
                case 'sideevents':
                    return redirect(route('OrderSideEvents',['event_id'=>$event_id]));
                break;   
                case 'accommodation':
                    return redirect(route('OrderAccommodation',['event_id'=>$event_id]));
                break;               
                default:
                    # code...
                    break;
            }
        }

        //return redirect(route('showEventCheckout',['event_id'=>$event_id]));
        return redirect(route('OrderSideEvents',['event_id'=>$event_id]));
    }

}
