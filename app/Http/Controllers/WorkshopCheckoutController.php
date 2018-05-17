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

class WorkshopCheckoutController extends Controller
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

    public function handleTransaction($event_id){
         return redirect(route('showOrderWorkshops', $event_id));
    }

    public function startTransaction($event_id){
         return redirect(route('showOrderWorkshops', $event_id));
    }

    public function showOrderWorkshops($event_id)
    {
        $order_session = session()->get('ticket_order_' . $event_id);

        if (!$order_session || $order_session['expires'] < Carbon::now()) {
            $route_name = $this->is_embedded ? 'showEmbeddedEventPage' : 'showEventPage';
            return redirect()->route($route_name, ['event_id' => $event_id]);
        }

        $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);
        $workshops   = Ticket::where(['type'=>'WORKSHOP','event_id'=>$event_id])->get();
        $event = Event::findOrFail($event_id);

        if(!$workshops->count()){
            $data = $order_session + [
                    'event'           => $event,
                    'secondsToExpire' => $secondsToExpire,
                /*    'coupon_flag'           => $order_session['coupon_flag'],
                    'discount'              => $order_session['discount'],
                    'discount_ticket_title' => $order_session['discount_ticket_title'],
                    'exact_amount'          => $order_session['exact_amount'],
                    'amount_ticket_title'   => $order_session['amount_ticket_title'],
                */
                    'is_embedded'     => $this->is_embedded,
                ];
        /*
         * If there're no workshops,
         */
        return redirect()->route('showEventCheckout', ['event_id' => $event_id]);

        /*
         * Maybe display something prettier than this?
         */
        //exit('Please enable Javascript in your browser.');
        return $this->javascriptError($event_id);
        }

        $data = $order_session + [
                'event'           => $event,
                'sideeventsar'   => $workshops,
                'secondsToExpire' => $secondsToExpire,
            /*    'coupon_flag'           => $order_session['coupon_flag'],
                'discount'              => $order_session['discount'],
                'discount_ticket_title' => $order_session['discount_ticket_title'],
                'exact_amount'          => $order_session['exact_amount'],
                'amount_ticket_title'   => $order_session['amount_ticket_title'],
            */
                'is_embedded'     => $this->is_embedded,
            ];

            //dd($data);

        if ($this->is_embedded) {
            return view('Public.ViewEvent.Embedded.EventWorkshop', $data);
        }

        return view('Public.ViewEvent.EventWorkshop', $data);
    }


    public function postOrderWorkshops(Request $request, $event_id)
    {

        $event = Event::findOrFail($event_id);

        $order_session['order_total'] = $request->get('old_total') + $request->get('days') * $request->get('price');

        //Get the values from the fields
        $fullname = $request->get('first_name'). $request->get('last_name');
        $email = $request->get('email');
        $hotel_status = $request->get('hotel_status');
        $title = $request->get('title');
        $amount =  $order_session['order_total'];
        $price = $request->get('price');
        $ticket_id = $request->get('ticket_id');
        $accommodation_dates =  $request->get('mydates');

        //Retrieve information from the form
          $ticket_id = $request->get('ticket_id');
          $ticket_quantity = $request->get('ticket_'.$ticket_id);
          $ticket_price = $request->get('price');
          $ticket_dates = $request->get('mydates');


        //Retrieve the old Total
        $old_total = $request->get('old_total');

        //Make calculations of the new total
        $new_total = $old_total + ($ticket_quantity*$price);

        //dd("Old total was ". $old_total . " and the New Total is ". $new_total);
       /*
         * Remove any tickets the user has reserved
         */
    //    ReservedTickets::where('session_id', '=', session()->getId())->delete();

        /*
         * Go through the selected tickets and check if they're available
         * , tot up the price and reserve them to prevent over selling.
         */

        $availables              =    session()->get('ticket_order_' . $event_id);
        $tickets                 =    $availables['tickets'];
        $order_total             =    $availables['order_total'];
        $total_ticket_quantity   =    $availables['total_ticket_quantity'];
        $booking_fee             =    $availables['booking_fee'];
        $organiser_booking_fee   =    $availables['organiser_booking_fee'];
        $discount                =    $availables['discount'];
        $discount_ticket_title   =    $availables['discount_ticket_title'];
        $exact_amount            =    $availables['exact_amount'];
        $amount_ticket_title     =    $availables['amount_ticket_title'];
        $quantity_available_validation_rules = [];

        //dd("Order total from session is " .$order_total . "Order total from form is " . $old_total);
        //dd($tickets);

        //Checks if there are any tickets selected
        //TODO make sure the check works
       // if(!empty($ticket_ids)){
           // foreach ($ticket_ids as $ticket_id) {
               //Gets the Ticket Quantity
                //$current_ticket_quantity = (int)$request->get('ticket_' . $ticket_id);
                $current_ticket_quantity = $ticket_quantity;

                /*
                if ($current_ticket_quantity < 1) {
                    continue;
                }
                */

               // dd($availables);
                //Updates the ticket quantity
                $total_ticket_quantity = $total_ticket_quantity + $current_ticket_quantity;

                //Retrieves ticket information from the database
                //dd($ticket_id);
                $ticket = Ticket::find($ticket_id);

                //
                $ticket_quantity_remaining = $ticket->quantity_remaining;

                //
                $max_per_person = min($ticket_quantity_remaining, $ticket->max_per_person);

                //
                $quantity_available_validation_rules['ticket_' . $ticket_id] = [
                    'numeric',
                    'min:' . $ticket->min_per_person,
                    'max:' . $max_per_person
                ];
               // dd($order_total);

                //
                /*
                $quantity_available_validation_messages = [
                    'ticket_' . $ticket_id . '.max' => 'The maximum number of tickets you can register is ' . $ticket_quantity_remaining,
                    'ticket_' . $ticket_id . '.min' => 'You must select at least ' . $ticket->min_per_person . ' tickets.',
                ];
                */
                /*

                $validator = Validator::make(['ticket_' . $ticket_id => (int)$request->get('ticket_' . $ticket_id)],
                    $quantity_available_validation_rules, $quantity_available_validation_messages);

                if ($validator->fails()) {
                    return response()->json([
                        'status'   => 'error',
                        'messages' => $validator->messages()->toArray(),
                    ]);
                }
                */

                $order_total = $order_total + ($current_ticket_quantity * $ticket->price);
                //dd($order_total);
                $booking_fee = $booking_fee + ($current_ticket_quantity * $ticket->booking_fee);
                $organiser_booking_fee = $organiser_booking_fee + ($current_ticket_quantity * $ticket->organiser_booking_fee);

                //Appends Ticket information to the Ticket Variable that will be stored in the session
                $tickets[count($tickets)] = [
                    'ticket'                => $ticket,
                    'qty'                   => $current_ticket_quantity,
                    'price'                 => ($current_ticket_quantity * $ticket->price),
                    'booking_fee'           => ($current_ticket_quantity * $ticket->booking_fee),
                    'organiser_booking_fee' => ($current_ticket_quantity * $ticket->organiser_booking_fee),
                    'full_price'            => $ticket->price + $ticket->total_booking_fee,
                    'dates'                => $ticket_dates,
                ];

                //dd($tickets);
                /*
                 * To escape undefined offset errors due to accessing arrays that associate with tickets but shorter, in
                 * EventCreateOrderSection.blade, we have to nullify all extra elements... null is set to empty string
                 * denoted by ''
                 */
                $discount[count($discount)]  = '';
                $discount_ticket_title[count($discount_ticket_title)] = '';
                $exact_amount[count($exact_amount)]  = '';
                $amount_ticket_title[count($amount_ticket_title)] = '';

                /*
                 * Reserve the tickets for X amount of minutes
                 */
                $reservedTickets = new ReservedTickets();
                $reservedTickets->ticket_id = $ticket_id;
                $reservedTickets->event_id = $event_id;
                $reservedTickets->quantity_reserved = $current_ticket_quantity;
                $reservedTickets->expires = $availables['expires'];
                $reservedTickets->session_id = session()->getId();
                $reservedTickets->save();

            //} //end-foreach($ticket_ids)
        //} //end-if-!empty($ticket_ids)

        /*
         * We have to update the tickets to be reserved
         */
 //not        $reservedTickets = $availables['reserved_tickets_id'] + $reservedTickets->id;

        if (empty($tickets)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No tickets selected.',
            ]);
        }

        /*
         * The 'ticket_order_{event_id}' session stores everything we need to complete the transaction. We have to update
         * the variables we had set earlier but are now modified
         */

        $availables['tickets'] = $tickets;
        $availables['total_ticket_quantity'] = $total_ticket_quantity;
 //        $availables['reserved_tickets_id'] = $reservedTickets;
        $availables['order_total'] = $order_total;
        $availables['organiser_booking_fee'] = $organiser_booking_fee;
        $availables['total_booking_fee'] = $booking_fee + $organiser_booking_fee;
        $availables['booking_fee'] = $booking_fee;
        $availables['discount'] = $discount;
        $availables['discount_ticket_title'] = $discount_ticket_title;
        $availables['exact_amount'] = $exact_amount;
        $availables['amount_ticket_title'] = $amount_ticket_title;

        session()->forget('ticket_order_' . $event->id);
        session()->set('ticket_order_' . $event->id,
            $availables
        );
       // dd($tickets);

        /*
         * If we're this far assume everything is OK and redirect them
         * to the the checkout page.
         */
    //     return response()->redirectToRoute('OrderSideEvents', [
    //         'event_id'          => $event_id
    //     ]);

        //$printer = session()->get('ticket_order_' . $event->id);
    //    dd($printer);

        /*
         * If we're this far assume everything is OK and redirect them
         * to the the checkout page.
         */
        if ($request->ajax()) {
        //    return redirect()->route('OrderSideEvents', ['event_id' => $event_id,'is_embedded' => $this->is_embedded,]). '#order_form';
            return response()->json([
                'status'      => 'success',
                'redirectUrl' => route('OrderWorkshops', [
                        'event_id'    => $event_id,
                        'is_embedded' => $this->is_embedded,
                    ]) . '#order_form',
            ]);
        }

        return redirect(route('OrderWorkshops',['event_id'=>$event_id]));

        /*
         * Maybe display something prettier than this?
         */
        //exit('Please enable Javascript in your browser.');
        return $this->javascriptError($event_id);
    }

    public function completeOrderWorkshops($event_id)
    {
        if(session()->get('transaction_'.$event_id)){
            $tempo = session()->get('transaction_'.$event_id);
            ++$tempo;
            session()->forget('transaction_'.$event_id);
            session()->set('transaction_'.$event_id,$tempo);
        }else{
            session()->set('transaction_'.$event_id,1);
        }
        return redirect(route('handleTransactions',['event_id'=>$event_id]));
    }

}
