<?php
namespace App\Http\Controllers;
use App\Models\Event;
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
        $closeevents = Event::scope()->whereBetween('start_date', array(new DateTime('2018-01-01 15:26:00'), new DateTime( '2018-12-31 15:26:00')))->get();
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
        $closeevents = Event::scope()->whereBetween('start_date', array(new DateTime('2018-01-01 15:26:00'), new DateTime( '2018-12-31 15:26:00')))->get();
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
    //end of addition DonaldMar2


    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function showSideEvents(Request $request, $event_id)
    {
        $event = Event::scope()->findOrfail($event_id);
        $closeevents = Event::scope()->whereBetween('start_date', array(new DateTime('2018-03-01 15:26:00'), new DateTime( '2018-12-31 15:26:00')))->get();
        $data = [ 
            'sideevents'    => $closeevents,
            'event'         => $event,
        ];
        return view('ManageEvent.SideEvents', $data);
    }
    //end of addition DonaldMar2

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
