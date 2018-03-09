<?php
namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Organiser;
use Illuminate\Http\Request;
use DB;
use Log;
use DateTime;
class OrganiserEventsController extends MyBaseController
{

    /**
     * added by DonaldMar2
     * Show the organiser events page
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function chooseSideEvents(Request $request, $event_id)
    {
        $event = Event::scope()->findOrfail($event_id);
        $closeevents = Event::scope()->whereBetween('start_date', array(new DateTime($event->start_date), new DateTime($event->end_date)))->get();
        $data = [ 
            'sideevents'    => $closeevents,
            'event'         => $event,
        ];
        return view('ManageEvent.Modals.SideEventsChoices', $data);
    }


    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postChooseSideEvents(Request $request, $event_id)
    {
        $event = Event::scope()->findOrfail($event_id);
        $side_events=[]; $sc=0;
        $closeevents = Event::scope()->whereBetween('start_date', array(new DateTime($event->start_date), new DateTime($event->end_date)))->get();
        for($i=0;$i<count($closeevents);++$i){
            $name = 'side_event_'.$i;
            if($request->has($name)){
                $side_events[$sc]=Event::where(['id'=>$request->get($name)])->first(); ++$sc;
            }
        }

        $data = [ 
            'sideevents'    => $side_events,
            'event'         => $event,
        ];
        return view('ManageEvent.SideEvents', $data);
    }


    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function showReCreateSideEvent($event_id)
    {
        return view('ManageEvent.Modals.ReCreateSideEvent', [
            'event' => Event::scope()->find($event_id),
        ]);
    }

    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postReCreateSideEvent(Request $request, $event_id)
    {
        $event = Event::scope()->findOrfail($event_id);
        
        $ticket = Ticket::createNew();
        if (!$ticket->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);
        }

        $ticket->event_id = $event_id;
        $ticket->type = 'SIDEEVENT';
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
        $ticket->is_hidden = 0;

        $ticket->save();

        session()->flash('message', 'Successfully Created SideEvent');

        return response()->json([
            'status'      => 'success',
            'id'          => $ticket->id,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showEventTickets', [
                'event_id' => $event_id,
            ]),
        ]);
    }

    //end of addition DonaldMar2


    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function showSideEvents(Request $request, $event_id)
    {
        /** commented by Donald Mar9 due to change of approach

        $event = Event::scope()->findOrfail($event_id);
        $closeevents = Event::scope()->whereBetween('start_date', array(new DateTime('2018-03-01 15:26:00'), new DateTime( '2018-12-31 15:26:00')))->get();
        $data = [ 
            'sideevents'    => $closeevents,
            'event'         => $event,
        ];
        return view('ManageEvent.SideEvents', $data);
        */
        $event = Event::scope()->findOrfail($event_id);
        $closeevents = Ticket::scope()->where(['event_id'=>$event->id, 'type'=>'SIDEEVENT'])->get();
        $data = [ 
            'sideevents'    => $closeevents,
            'event'         => $event,
        ];
        return view('ManageEvent.ReSideEvents', $data);
    }
    //end of addition DonaldMar2 DonaldMar9

    /**
     * Show the organiser events page
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function showEvents(Request $request, $organiser_id)
    {
        $organiser = Organiser::scope()->findOrfail($organiser_id);
        $allowed_sorts = ['created_at', 'start_date', 'end_date', 'title'];
        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'start_date');
        $events = $searchQuery
            ? Event::scope()->where('title', 'like', '%' . $searchQuery . '%')->orderBy($sort_by,
                'desc')->where('organiser_id', '=', $organiser_id)->paginate(12)
            : Event::scope()->where('organiser_id', '=', $organiser_id)->orderBy($sort_by, 'desc')->paginate(12);
        $data = [
            'events'    => $events,
            'organiser' => $organiser,
            'search'    => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => $request->get('sort_by') ? $request->get('sort_by') : '',
                'showPast' => $request->get('past'),
            ],
        ];
        return view('ManageOrganiser.Events', $data);
    }

    //added by DonaldFeb22
    /**
     * Deleted an event
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteEvent(Request $request, $event_id)
    {
        $data['event_id'] = $event_id;
        $event = Event::where('id','=', $event_id)->first();
        /*
         * Don't allow deletion of events with some tickets having been sold already.
         */
        if ($event->sales_volume > 0 || $event->organiser_fees_volume > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sorry, you can\'t delete this event as some income has already been generated',
                'event'      => $event->title,
            ]);
        }
        $organiser_id = $event->organiser_id;
        if ($event->delete()) {
            session()->flash('message', $event->title.' Event Successfully Deleted.');
            return response()->redirectToRoute('showOrganiserEvents', [
                'organiser_id'      => $organiser_id,
            ]);
        }
        Log::error('Event Failed to delete', [
            'event' => $event,
        ]);
        return response()->json([
            'status'  => 'error',
            'id'      => $event->id,
            'message' => 'Whoops! Looks like something went wrong. Please try again.',
        ]);
    }
    //end of additions
}
