<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use App\Coupon;
use Carbon\Carbon;

class OrganiserDashboardController extends MyBaseController
{
    /**
     * Show the organiser dashboard
     *
     * @param $organiser_id
     * @return mixed
     */
    public function showDashboard($organiser_id)
    {
        $organiser = Organiser::scope()->findOrFail($organiser_id);
        $upcoming_events = $organiser->events()->where('end_date', '>=', Carbon::now())->get();
        $calendar_events = [];
        $all_discounts = [];

        /* Prepare JSON array for events for use in the dashboard calendar */
        foreach ($organiser->events as $event) {
            $calendar_events[] = [
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end'   => $event->end_date->toIso8601String(),
                'url'   => route('showEventDashboard', [
                    'event_id' => $event->id
                ]),
                'color' => '#4E558F'
            ];
            if(!$event->tickets){goto eventhasnotickets;}
            $tickets = [];
            foreach ($event->tickets as $ticket) {
                $tickets[$ticket->id] = $ticket->price;
            }
            $discounts = Coupon::where(['event_id'=>$event->id,'state'=>'Used'])->get();
            if(count($discounts)==0){goto nodiscounts;}
            $discount_sums=[];
            foreach($discounts as $discount){
                if(!array_key_exists($discount->ticket_id, $tickets)){goto correspondticketdeleted;}
                if($discount->exact_amount){
                    $subtracted = $tickets[$discount->ticket_id] - $discount->exact_amount;
                }elseif($discount->discount){ //discount = percentage
                    $subtracted = ($discount->discount * $tickets[$discount->ticket_id])/100;
                }
                if(array_key_exists($discount->ticket_id,$discount_sums)){
                    $discount_sums[$discount->ticket_id] += $subtracted;
                }else{
                    $discount_sums[$discount->ticket_id] = $subtracted;
                }
                correspondticketdeleted:
            }
            $all_discounts[$event->id] = $discount_sums;
            nodiscounts:
            eventhasnotickets:
        }

        $data = [
            'organiser'       => $organiser,
            'upcoming_events' => $upcoming_events,
            'calendar_events' => json_encode($calendar_events),
            'event_discounts' => $all_discounts,
        ];

        return view('ManageOrganiser.Dashboard', $data);
    }
}
