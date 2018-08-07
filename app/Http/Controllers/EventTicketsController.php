<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use DB;
use Excel;
use Illuminate\Support\Facades\Input;

/*
  Attendize.com   - Event Management & Ticketing
 */

class EventTicketsController extends MyBaseController
{
    /**
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showTickets(Request $request, $event_id)
    {
        $allowed_sorts = [
            'created_at'    => 'Creation date',
            'title'         => 'Ticket title',
            'quantity_sold' => 'Quantity sold',
            'sales_volume'  => 'Sales volume',
            'sort_order'  => 'Custom Sort Order',
        ];

        // Getting get parameters.
        $q = $request->get('q', '');
        $sort_by = $request->get('sort_by');
        if (isset($allowed_sorts[$sort_by]) === false) {
            $sort_by = 'sort_order';
        }

        // Find event or return 404 error.
        $event = Event::scope()->find($event_id);
        if ($event === null) {
            abort(404);
        }

        // Get tickets for event.
        $tickets = empty($q) === false
            ? $event->tickets()->where('title', 'like', '%' . $q . '%')->where(['type'=>NULL])->orWhere(['type'=>'extras'])->orWhere(['type'=>'normal'])->orderBy($sort_by, 'asc')->paginate()
            : $event->tickets()->where(['type'=>NULL])->orWhere(['type'=>'normal'])->orderBy($sort_by, 'asc')->paginate();

        $discounts = Coupon::where(['event_id'=>$event->id,'state'=>'Used'])->get();
        $discount_sums=[];
        if(!$event->tickets){goto eventhasnotickets;}
        $ticketsarr = [];
        foreach ($event->tickets as $ticket) {
            $ticketsarr[$ticket->id] = $ticket->price;
        }
        if(count($discounts)==0){goto nodiscounts;}
        foreach($discounts as $discount){
            if($discount->exact_amount){
                $subtracted = $ticketsarr[$discount->ticket_id] - $discount->exact_amount;
            }elseif($discount->discount){ //discount = percentage
                $subtracted = ($discount->discount * $ticketsarr[$discount->ticket_id])/100;
            }
            if(array_key_exists($discount->ticket_id,$discount_sums)){
                $discount_sums[$discount->ticket_id] += $subtracted;
            }else{
                $discount_sums[$discount->ticket_id] = $subtracted;
            }
        }
        nodiscounts:
        eventhasnotickets:
        $discounts = $discount_sums;

        // Return view.
        return view('ManageEvent.Tickets', compact('event', 'tickets', 'sort_by', 'q', 'allowed_sorts','discounts'));
    }

    /**
     * Show the edit ticket modal
     *
     * @param $event_id
     * @param $ticket_id
     * @return mixed
     */
    public function showEditTicket($event_id, $ticket_id)
    {
        $linkables = Ticket::where('type','Extra')->get();//->pluck('title', 'id');
        $linkable_tickets[null] = 'No Association';
        foreach ($linkables as $link) {
            $linkable_tickets[$link->id] = $link->title;
        }
        $data = [
            'event'  => Event::scope()->find($event_id),
            'ticket' => Ticket::scope()->find($ticket_id),
            'linkable_tickets' => $linkable_tickets,
        ];

        return view('ManageEvent.Modals.EditTicket', $data);
    }

    public function showEditCoupon($event_id, $coupon_id)
    {
        $data = [
            'event'  => Event::scope()->find($event_id),
//            'coupon' => Coupon::scope()->find($coupon_id),
            'coupon' => Coupon::where('id','=', $coupon_id)->first(),
            'tickets' => DB::table('tickets')->where('event_id','=', $event_id)->get(['id', 'title']),
        ];

        return view('ManageEvent.Modals.EditCoupon', $data);
    }

    public function showEditAccommodation($event_id, $ticket_id)
    {
        $linkables = Ticket::where('type','Extra')->get();//->pluck('title', 'id');
        $linkable_tickets[null] = 'No Association';
        foreach ($linkables as $link) {
            $linkable_tickets[$link->id] = $link->title;
        }
        $data = [
            'event'  => Event::scope()->find($event_id),
            'ticket' => Ticket::scope()->find($ticket_id),
            'linkable_tickets' => $linkable_tickets,
        ];

        return view('ManageEvent.Modals.EditAccommodation', $data);
    }


    /**
     * Show the create ticket modal
     *
     * @param $event_id
     * @return \Illuminate\Contracts\View\View
     */
    public function showCreateTicket($event_id)
    {

        return view('ManageEvent.Modals.CreateTicket', [
            'event' => Event::scope()->find($event_id),
        ]);
    }



    public function showCreateCoupon($event_id)
    {



      $tickets = DB::table('tickets')->where('event_id','=', $event_id)->get(['id', 'title']);

      //dd($tickets);

       return view('ManageEvent.Modals.CreateCoupon', [
            'event' => Event::scope()->find($event_id),
            'tickets' => $tickets,
        ]);
    }


    public function showExportCoupons($event_id, $export_as = 'xls')
    {

        Excel::create('attendees-as-of-' . date('d-m-Y-g.i.a'), function ($excel) use ($event_id) {

            $excel->setTitle('Coupons List');

            // Chain the setters
            $excel->setCreator(config('attendize.app_name'))
                ->setCompany(config('attendize.app_name'));

            $excel->sheet('attendees_sheet_1', function ($sheet) use ($event_id) {

                DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
                $data = DB::table('coupons')
                    ->where('coupons.event_id', '=', $event_id)
                    ->where('coupons.state', '=', 'Valid')
                    ->join('events', 'events.id', '=', 'coupons.event_id')
                    ->join('tickets', 'tickets.id', '=', 'coupons.ticket_id')
                    ->join('orders', 'orders.id', '=', 'coupons.user')
                    ->select([
                        'coupons.coupon_code',
                        'coupons.discount',
                        'coupons.exact_amount',
                        'tickets.title',
                        'orders.order_reference',
                        'coupons.group',
                        'coupons.state',
                    ])->get();

                $sheet->fromArray($data);
                $sheet->row(1, [
                    'Coupon Code',
                    'Discount',
                    'Exact Amount',
                    'Ticket Type',
                    'User',
                    'Group',
                    'State',
                ]);

                // Set gray background on first row
                $sheet->row(1, function ($row) {
                    $row->setBackground('#f5f5f5');
                });
            });
        })->export($export_as);
    }



    public function showBookingModal($event_id)
    {
      //dd($Event::scope()->find($event_id));
      return view('Public.ViewEvent.Modals.CreateBooking', [
            'event' => Event::scope()->find($event_id),
        ]);
    }

    /**
     * Creates a ticket
     *
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCreateTicket(Request $request, $event_id)
    {
        $ticket = Ticket::createNew();
        $f=0; $miss=0; $toc=0; $ticketoffers=[];
        while($miss<3){
          if($request->get("ticket_offer_$f")){
            $ticketoffers[$toc]=$request->get("ticket_offer_$f");++$f;++$toc;$miss=0;
          }else{++$miss;++$f;}
        }
        $g=0; $mix=0; $oxc=0; $ticketextras=[];
        while($mix<3){
          if($request->has("ticket_extra_$g")){
            $ticketextras[$oxc]=$request->get("ticket_extra_$g");
            if($request->has("ticket_extra_option_$g") && $request->get("$ticket_extra_amt_$g")){
              $ticketextras[$oxc]=$ticketextras[$oxc].'@*#'.$request->get("ticket_extra_amt_$g").'@*#'.$request->get("$ticket_extra_option_$g");++$g;++$oxc;$mix=0;
            }elseif($request->has("$ticket_extra_amt_$g")){
              $ticketextras[$oxc]=$ticketextras[$oxc].'@*#'.$request->get("ticket_extra_amt_$g").'@*#';++$g;++$oxc;$mix=0;
            }else{
              return response()->json([
                  'status'   => 'error',
                  'messages' => 'it appears one ticket extra was not correctly filled. Make sure for each ticket extra, there is corresponding extra offer amount',
              ]);
            }
          }else{++$mix;++$g;}
        }
        if (!$ticket->validate($request->all())) {          
        $data = [
            'event' => Event::findOrFail($ticket->event_id),
            'callbackurl' => null,
            'messages' =>  $ticket->errors(),
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
        }

        $ticket->event_id = $event_id;
        $ticket->title = $request->get('title');
        $ticket->quantity_available = !$request->get('quantity_available') ? null : $request->get('quantity_available');
        $ticket->start_sale_date = $request->get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('start_sale_date')) : null;
        $ticket->end_sale_date = $request->get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('end_sale_date')) : null;
        $ticket->price = $request->get('price');
        $ticket->min_per_person = $request->get('min_per_person');
        $ticket->max_per_person = $request->get('max_per_person');
        $ticket->description = $request->get('description');
        $ticket->type = $request->get('type');
        $ticket->ticket_offers = empty($ticketoffers) ? null : implode('#@#',$ticketoffers);
        $ticket->ticket_extras = empty($ticketextras) ? null : implode('{+}',$ticketextras);
        $ticket->is_hidden = $request->get('is_hidden') ? 1 : 0;

        $ticket->save();

        session()->flash('message', 'Successfully Created Ticket');
        
        return response()->json([
            'status'      => 'success',
            'id'          => $ticket->id,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showEventTickets', [
                'event_id' => $event_id,
            ]),
        ]); 
        return redirect()->route('showEventTickets', ['event_id' => $event_id]);
    }


    public function postCreateAccommodation(Request $request, $event_id)
    {
        //dd($request->all());
        $ticket = Ticket::createNew();
        $f=0; $miss=0; $toc=0; $ticketoffers=[];
        while($miss<3){
          if($request->get("ticket_offer_$f")){
            $ticketoffers[$toc]=$request->get("ticket_offer_$f");++$f;++$toc;$miss=0;
          }else{++$miss;++$f;}
        }
        $g=0; $mix=0; $oxc=0; $ticketextras=[];
        while($mix<3){
          if($request->has("ticket_extra_$g")){
            $ticketextras[$oxc]=$request->get("ticket_extra_$g");
            if($request->has("ticket_extra_option_$g") && $request->get("$ticket_extra_amt_$g")){
              $ticketextras[$oxc]=$ticketextras[$oxc].'@*#'.$request->get("ticket_extra_amt_$g").'@*#'.$request->get("$ticket_extra_option_$g");++$g;++$oxc;$mix=0;
            }elseif($request->has("$ticket_extra_amt_$g")){
              $ticketextras[$oxc]=$ticketextras[$oxc].'@*#'.$request->get("ticket_extra_amt_$g").'@*#';++$g;++$oxc;$mix=0;
            }else{
              return response()->json([
                  'status'   => 'error',
                  'messages' => 'it appears one ticket extra was not correctly filled. Make sure for each ticket extra, there is corresponding extra offer amount',
              ]);
            }
          }else{++$mix;++$g;}
        }
        if (!$ticket->validate($request->all())) {          
        $data = [
            'event' => Event::findOrFail($ticket->event_id),
            'callbackurl' => null,
            'messages' =>  $ticket->errors(),
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
        }

        $ticket->event_id = $event_id;
        $ticket->title = $request->get('title');
        $ticket->status = $request->get('status');
        $ticket->quantity_available = !$request->get('quantity_available') ? null : $request->get('quantity_available');
        $ticket->start_sale_date = $request->get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('start_sale_date')) : null;
        $ticket->end_sale_date = $request->get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('end_sale_date')) : null;
        $ticket->price = $request->get('price');
        $ticket->min_per_person = $request->get('min_per_person');
        $ticket->max_per_person = $request->get('max_per_person');
        $ticket->description = $request->get('description');
        $ticket->type = $request->get('type');
        $ticket->ticket_offers = empty($ticketoffers) ? null : implode('#@#',$ticketoffers);
        $ticket->ticket_extras = empty($ticketextras) ? null : implode('{+}',$ticketextras);
        $ticket->is_hidden = $request->get('is_hidden') ? 1 : 0;

        $ticket->save();

        session()->flash('message', 'Successfully Created Ticket');

        /*return response()->json([
            'status'      => 'success',
            'id'          => $ticket->id,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showEventTickets', [
                'event_id' => $event_id,
            ]),
        ]);*/

        return redirect()->back();
    }


    public function postEditAccommodation(Request $request, $event_id, $ticket_id)
    {
        //dd($request->all());
        $ticket = Ticket::scope()->findOrFail($ticket_id);
        $f=0; $ticketoffers=[]; $toc=0; $mis=0;
        while($mis<3){
          if($request->get("ticket_offer_$f")){
            $ticketoffers[$toc]=$request->get("ticket_offer_$f");++$f;++$toc;$mis=0;
          }else{++$mis;++$f;}
        }
        $fad=0; $misss=0; $tocc=0; $ticketofferss=[];
        while($misss<3){
          if($request->get("ticket_offerad_$fad")){
            $ticketofferss[$tocc]=$request->get("ticket_offerad_$fad");++$tocc;++$fad;$misss=0;
          }else{++$misss;++$fad;}
        }
        $ticketoffers = array_merge($ticketoffers,$ticketofferss);


        $validation_rules['quantity_available'] = [
            'integer',
            'min:' . ($ticket->quantity_sold + $ticket->quantity_reserved)
        ];
        $validation_messages['quantity_available.min'] = 'Quantity available can\'t be less the amount sold or reserved.';

        $ticket->rules = $validation_rules + $ticket->rules;
        $ticket->messages = $validation_messages + $ticket->messages;

        if (!$ticket->validate($request->all())) {          
        $data = [
            'event' => Event::findOrFail($ticket->event_id),
            'callbackurl' => null,
            'messages' =>  $ticket->errors(),
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
        }

        $ticket->title = $request->get('title');
        $ticket->status = $request->get('status');
        $ticket->quantity_available = !$request->get('quantity_available') ? null : $request->get('quantity_available');
        $ticket->start_sale_date = $request->get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('start_sale_date')) : null;
        $ticket->end_sale_date = $request->get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('end_sale_date')) : null;
        $ticket->price = $request->get('price');
        $ticket->min_per_person = $request->get('min_per_person');
        $ticket->max_per_person = $request->get('max_per_person');
        $ticket->description = $request->get('description');
        $ticket->type = $request->get('type');
        $ticket->ticket_offers = empty($ticketoffers) ? null : implode('#@#',$ticketoffers);
        $ticket->ticket_extras = empty($ticketextras) ? null : implode('{+}',$ticketextras);
        $ticket->is_hidden = $request->get('is_hidden') ? 1 : 0;

        $ticket->save();

        session()->flash('message', 'Successfully Edited Accommodation Ticket');

        /*return response()->json([
            'status'      => 'success',
            'id'          => $ticket->id,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showEventTickets', [
                'event_id' => $event_id,
            ]),
        ]);*/

        return redirect()->back();
    }








    public function postCreateCoupon(Request $request, $event_id)
    {

        $id = $request->get('id'); 

        $title = DB::table('tickets')->select('title')->where('id', '=', $id)->value('title');

            for ($i = 0; $i < $request->get('max_coupons'); $i++) {
              Coupon::create([
                'coupon_code' => str_random(10),
                'discount' => $request->get('discount'),
                'exact_amount' => $request->get('exact_amt'),
                'state' => 'Valid',
                'group' =>  $request->get('group'),
                'ticket_id' =>  $request->get('id'),
                'ticket' =>  $title,
                'event_id' =>  $event_id,
              ]);
            }

            return redirect()->back();
    }

    /**
     * Pause ticket / take it off sale
     *
     * @param Request $request
     * @return mixed
     */
    public function postPauseTicket(Request $request)
    {
        $ticket_id = $request->get('ticket_id');

        $ticket = Ticket::scope()->find($ticket_id);

        $ticket->is_paused = ($ticket->is_paused == 1) ? 0 : 1;

        if ($ticket->save()) {

            return redirect()->back();
            session()->flash('Ticket Successfully Updated.');
            /*return response()->json([
                'status'  => 'success',
                'message' => 'Ticket Successfully Updated',
                'id'      => $ticket->id,
            ]); */
        }

        Log::error('Ticket Failed to pause/resume', [
            'ticket' => $ticket,
        ]);

        $data = [
            'event' => Event::findOrFail($ticket->event_id),
            'callbackurl' => null,
            'messages' => 'Sorry, something beyond our realization has gone wrong. Please try again',
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
        /*return response()->json([
            'status'  => 'error',
            'id'      => $ticket->id,
            'message' => 'Whoops! Looks like something went wrong. Please try again.',
        ]); */
    }

    /**
     * Deleted a ticket
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteTicket(Request $request, $ticket_id)
    {
        $data['ticket_id'] = $ticket_id;
        $ticket = Ticket::where('id','=', $ticket_id)->first();

        /*
         * Don't allow deletion of tickets which have been sold already.
         */
        if ($ticket->quantity_sold > 0) {

            $data = [
                'event' => Event::findOrFail($ticket->event_id),
                'callbackurl' => null,
                'messages' => 'Sorry, you can\'t delete this ticket as some have already been sold',
                'request_details' => null,
                'parameters' => null
            ];
            return view('Public.ViewEvent.EventPageErrors', $data);
        }

        $event_id = $ticket->event_id;
        if ($ticket->delete()) {
            session()->flash('message', $ticket->title.' Ticket Successfully Deleted.');
            return response()->redirectToRoute('showEventTickets', [
                'event_id'      => $event_id,
            ]);
        }

        Log::error('Ticket Failed to delete', [
            'ticket' => $ticket,
        ]);


        $data = [
            'event' => Event::findOrFail($ticket->event_id),
            'callbackurl' => null,
            'messages' => 'Sorry, something beyond our realization has gone wrong. Please try again',
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
        /*return response()->json([
            'status'  => 'error',
            'id'      => $ticket->id,
            'message' => 'Whoops! Looks like something went wrong. Please try again.',
        ]);*/
    }



    public function postDeleteCoupon(Request $request, $coupon_id)
    {
        $coupon = Coupon::where('id','=', $coupon_id)->first();

        /*
         * Don't allow deletion of tickets which have been sold already.
         */
        if ($coupon->state == 'Used') {

            $data = [
                'event' => Event::findOrFail($coupon->event_id),
                'callbackurl' => null,
                'messages' => 'Sorry, you can\'t delete this coupon as it has already been used',
                'request_details' => null,
                'parameters' => null
            ];
            return view('Public.ViewEvent.EventPageErrors', $data);
        }

        $event_id = $coupon->event_id;
        if ($coupon->delete()) {
            session()->flash('message', $coupon->coupon_code.' coupon Successfully Deleted.');
            return response()->redirectToRoute('showEventCoupons', [
                'event_id'      => $event_id,
            ]);
        }

        Log::error('Coupon Failed to delete', [
            'coupon' => $coupon,
        ]);


        $data = [
            'event' => Event::findOrFail($coupon->event_id),
            'callbackurl' => null,
            'messages' => 'Sorry, something beyond our realization has gone wrong. Please try again',
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
        /*return response()->json([
            'status'  => 'error',
            'id'      => $ticket->id,
            'message' => 'Whoops! Looks like something went wrong. Please try again.',
        ]);*/
    }


    /**
     * Edit a ticket
     *
     * @param Request $request
     * @param $event_id
     * @param $ticket_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditTicket(Request $request, $event_id, $ticket_id)
    {
        $ticket = Ticket::scope()->findOrFail($ticket_id);
        $f=0; $ticketoffers=[]; $toc=0; $mis=0;
        while($mis<3){
          if($request->get("ticket_offer_$f")){
            $ticketoffers[$toc]=$request->get("ticket_offer_$f");++$f;++$toc;$mis=0;
          }else{++$mis;++$f;}
        }
        $fad=0; $misss=0; $tocc=0; $ticketofferss=[];
        while($misss<3){
          if($request->get("ticket_offerad_$fad")){
            $ticketofferss[$tocc]=$request->get("ticket_offerad_$fad");++$tocc;++$fad;$misss=0;
          }else{++$misss;++$fad;}
        }
        $ticketoffers = array_merge($ticketoffers,$ticketofferss);
        /*
         * Override some validation rules
         */
        $validation_rules['quantity_available'] = [
            'integer',
            'min:' . ($ticket->quantity_sold + $ticket->quantity_reserved)
        ];
        $validation_messages['quantity_available.min'] = 'Quantity available can\'t be less the amount sold or reserved.';

        $ticket->rules = $validation_rules + $ticket->rules;
        $ticket->messages = $validation_messages + $ticket->messages;

        if (!$ticket->validate($request->all())) {

        $data = [
            'event' => Event::findOrFail($ticket->event_id),
            'callbackurl' => null,
            'messages' =>  $ticket->errors(),
            'request_details' => null,
            'parameters' => null
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
        }

        $ticket->title = $request->get('title');
        $ticket->quantity_available = !$request->get('quantity_available') ? null : $request->get('quantity_available');
        $ticket->price = $request->get('price');
        $ticket->start_sale_date = $request->get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('start_sale_date')) : null;
        $ticket->end_sale_date = $request->get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i',
            $request->get('end_sale_date')) : null;
        $ticket->description = $request->get('description');
        $ticket->type = $request->get('type');
        $ticket->ticket_links = $request->get('ticket_links');
        $ticket->ticket_offers = empty($ticketoffers) ? null : implode('#@#',$ticketoffers);
        $ticket->min_per_person = $request->get('min_per_person');
        $ticket->max_per_person = $request->get('max_per_person');
        $ticket->is_hidden = $request->get('is_hidden') ? 1 : 0;

        $ticket->save();

        session()->flash('message', 'Successfully Edited Ticket');

        return response()->json([
            'status'      => 'success',
            'title'          => $ticket->title,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showEventTickets', [
                'event_id' => $event_id,
            ]),
        ]);
        return redirect()->route('showEventTickets', ['event_id' => $event_id]);

    }

    public function postEditCoupon(Request $request, $event_id)
    {
        $coupon_id = $request->get('coupon_id');
        $coupon = Coupon::where('id','=', $coupon_id)->first();
        if(!$coupon){
            exit ('Coupon does not exist');
        }
        $id = $request->get('id');
        $title = DB::table('tickets')->select('title')->where('id', '=', $id)->value('title');
        $coupon->ticket_id = $id;
        $coupon->ticket = $title;
        $coupon->state = $request->get('state');
        $coupon->discount = $request->get('discount');
        $coupon->exact_amount = $request->get('exact_amt');
        $coupon->group =  $request->get('group');
        $coupon->save();
        return redirect()->route('showEventCoupons', ['event_id' => $event_id]);
    }

    /**
     * Updates the sort order of tickets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateTicketsOrder(Request $request)
    {
        $ticket_ids = $request->get('ticket_ids');
        $sort = 1;

        foreach ($ticket_ids as $ticket_id) {
            $ticket = Ticket::scope()->find($ticket_id);
            $ticket->sort_order = $sort;
            $ticket->save();
            $sort++;
        }


        session()->flash('Ticket Order Successfully Updated.');
        return redirect()->back();
        /*return response()->json([
            'status'  => 'success',
            'message' => 'Ticket Order Successfully Updated',
        ]); */
    }
}
