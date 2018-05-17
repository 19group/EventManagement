<?php
namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Organiser;
use Illuminate\Http\Request;
use Image;
use Carbon\Carbon;
use DB;
use Log;
use DateTime;
class OrganiserWorkshopsController extends MyBaseController
{

    /**
     * added by DonaldMar2
     * Show the organiser workshops page
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
    public function showCreateWorkshop($event_id)
    {
        return view('ManageEvent.Modals.CreateWorkshop', [
            'event' => Event::scope()->find($event_id),
        ]);
    }

    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postCreateWorkshop(Request $request, $event_id)
    {
        $event = Event::scope()->findOrfail($event_id);
        
        $ticket = Ticket::createNew();
        if (!$ticket->validate($request->all())) {
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
            $data = [
                'event' => $event,
                'callbackurl' => 'showCreateWorkshop',
                'messages' => $ticket->errors(),
                'request_details' => $request,
                'parameters' => ['event_id' => $event_id]
            ];
            return view('Public.ViewEvent.EventPageErrors', $data);
        }

        $f=0; $miss=0; $esc=0; $scheduleopts=[]; 
        while($miss<3){
          if($request->has("start_schedule_$f") && $request->has("end_schedule_$f")){
            $scheduleopts[$esc]=Carbon::createFromFormat('d-m-Y H:i',
            $request->get("start_schedule_$f"))."<==>".Carbon::createFromFormat('d-m-Y H:i',
            $request->get("end_schedule_$f"));++$f;++$esc;$miss=0;
          }else{++$miss;++$f;}
        }

        $ticket->event_id = $event_id;
        $ticket->type = 'WORKSHOP';
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
        $ticket->ticket_extras = $request->get('ticket_extras');
        $ticket->ticket_offers = empty($scheduleopts) ? null : implode("+++", $scheduleopts);
        $ticket->is_hidden = 0;

        //$ticket->save(); DON'T SAVE HERE WITHOUT PHOTO PATHS
        if ($request->hasFile('workshop_image')) {
            $path = public_path() . '/' . config('attendize.workshop_images_path');
            $filename = 'workshop_image-' . md5(time() . $ticket->id) . '.' . strtolower($request->file('workshop_image')->getClientOriginalExtension());
            $file_full_path = $path . '/' . $filename;
            $request->file('workshop_image')->move($path, $filename);
            $img = Image::make($file_full_path);
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            //$img->save($file_full_path);
            /* Upload to s3 */
            \Storage::put(config('attendize.workshop_images_path') . '/' . $filename, file_get_contents($file_full_path));
            //save path to ticket table
            $ticket->ticket_main_photo = config('attendize.workshop_images_path') . '/' . $filename;
        }

        $ticket->save();    

        session()->flash('message', 'Successfully Created Workshop');

        return redirect()->route('showWorkshops', ['event_id' => $event_id]);

        /*return response()->json([
            'status'      => 'success',
            'id'          => $ticket->id,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showSideEvents', [
                'event_id' => $event_id,
            ]),
        ]);*/
    }

    /**
     * @param Request $request
     * @param $event_id
     * @param $ticket_id
     * @return mixed
     */
    public function postEditWorkshop(Request $request, $event_id,  $ticket_id)
    {
        $event = Event::scope()->findOrfail($event_id);
        $ticket = Ticket::scope()->findOrFail($ticket_id);
        if (!$ticket->validate($request->all())) {
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
            $data = [
                'event' => $event,
                'callbackurl' => 'showEditWorkshop',
                'messages' => $ticket->errors(),
                'request_details' => $request,
                'parameters' => ['event_id' => $event_id]
            ];
            return view('Public.ViewEvent.EventPageErrors', $data);
        }
        $of=0; $omiss=0; $esc=0; $scheduleopts=[]; 
        while($omiss<3){
          if($request->get("ogstart_schedule_$of") && $request->get("ogend_schedule_$of")){
            $scheduleopts[$esc]=Carbon::createFromFormat('d-m-Y H:i',
            $request->get("ogstart_schedule_$of"))."<==>".Carbon::createFromFormat('d-m-Y H:i',
            $request->get("ogend_schedule_$of"));++$of;++$esc;$omiss=0;
          }else{++$omiss;++$of;}
        }

        $f=0; $miss=0;
        while($miss<3){
          if($request->get("start_schedule_$f") && $request->get("end_schedule_$f")){
            $scheduleopts[$esc]=Carbon::createFromFormat('d-m-Y H:i',
            $request->get("start_schedule_$f"))."<==>".Carbon::createFromFormat('d-m-Y H:i',
            $request->get("end_schedule_$f"));++$f;++$esc;$miss=0;
          }else{++$miss;++$f;}
        }

        $ticket->event_id = $event_id;
        $ticket->type = 'WORKSHOP';
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
        $ticket->ticket_extras = $request->get('ticket_extras');
        $ticket->ticket_offers = empty($scheduleopts) ? null : implode("+++", $scheduleopts);
        $ticket->is_hidden = 0;

        if ($request->hasFile('workshop_image')) {
            $path = public_path() . '/' . config('attendize.workshop_images_path');
            $filename = 'workshop_image-' . md5(time() . $ticket->id) . '.' . strtolower($request->file('workshop_image')->getClientOriginalExtension());
            $file_full_path = $path . '/' . $filename;
            $request->file('workshop_image')->move($path, $filename);
            $img = Image::make($file_full_path);
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            /* Upload to s3 */
            \Storage::put(config('attendize.workshop_images_path') . '/' . $filename, file_get_contents($file_full_path));
            //save path to ticket table
            $ticket->ticket_main_photo = config('attendize.workshop_images_path') . '/' . $filename;
        }

        $ticket->save();

        session()->flash('message', 'Successfully Edited Workshop');
        return redirect()->route('showWorkshops', ['event_id' => $event_id]);

        /*return response()->json([
            'status'      => 'success',
            'id'          => $ticket->id,
            'message'     => 'Refreshing...',
            'redirectUrl' => route('showSideEvents', [
                'event_id' => $event_id,
            ]),
        ]);*/
    }

    /**
     * Show the edit sideevent modal
     *
     * @param $event_id
     * @param $sideevent_id
     * @return mixed
     */
    public function showEditWorkshop($event_id, $workshop_id)
    {
        $data = [
            'event'  => Event::scope()->find($event_id),
            'ticket' => Ticket::scope()->find($workshop_id),
        ];

        return view('ManageEvent.Modals.EditWorkshop', $data);
    }

    //end of addition DonaldMar2 DonaldMar12


    /**
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function showEventWorkshops(Request $request, $event_id)
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
        $workshops = Ticket::scope()->where(['event_id'=>$event->id, 'type'=>'WORKSHOP'])->get();
        $data = [ 
            'workshops'    => $workshops,
            'event'         => $event,
        ];
        return view('ManageEvent.EventWorkshops', $data);
    }



    /**
     * Deleted a side event
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteWorkshop(Request $request, $ticket_id)
    {
        $data['ticket_id'] = $ticket_id;
        $ticket = Ticket::where('id','=', $ticket_id)->first();

        /*
         * Don't allow deletion of tickets which have been sold already.
         */
        if ($ticket->quantity_sold > 0) {
            /*return response()->json([
                'status'  => 'error',
                'message' => 'Sorry, you can\'t delete this side event as some tickets have already been sold',
                'title'      => $ticket->title,
            ]);*/

            session()->flash('message','Sorry, you can\'t delete this workshop as some tickets have already been sold.');
            return response()->redirectToRoute('showWorkshops', [
                'event_id'      => $ticket->event_id,
            ]);
        }

        $event_id = $ticket->event_id;
        if ($ticket->delete()) {
            session()->flash('message', ' Workshop Successfully Deleted.');
            return response()->redirectToRoute('showWorkshops', [
                'event_id'      => $event_id,
            ]);
        }

        Log::error('Workshop Failed to delete', [
            'ticket' => $ticket,
        ]);

        /*return response()->json([
            'status'  => 'error',
            'title'      => $ticket->title,
            'message' => 'Whoops! Looks like something went wrong. Please try again.',
        ]);*/
        $data = [
            'event' => Event::findOrFail($event_id),
            'callbackurl' => 'showCreateWorkshop',
            'messages' => 'Whoops! Looks like something went wrong. Please try again.',
            'request_details' => $request,
            'parameters' => ['event_id' => $event_id]
        ];
        return view('Public.ViewEvent.EventPageErrors', $data);
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

    //----function for handling mass image uploads---
    public function processuploadedimages($arrayoffiles,$ticketid){
        $storagepaths=[];
        $uploadednames = $_FILES[$arrayoffiles]['name'];
        $path = public_path() . '/' . config('attendize.sideevent_images_path');
        for($filecounter=0;$filecounter<count($_FILES[$arrayoffiles]['name']); ++$filecounter){
            $extension = substr($uploadednames[$filecounter], stripos($uploadednames[$filecounter],'.') + 1);
            $filename = 'sideevent_image-' . md5(time() . $ticketid . $uploadednames[$filecounter]) . '.' . $extension;
            $file_full_path = $path . '/' . $filename;
            move_uploaded_file($_FILES[$arrayoffiles]['tmp_name'][$filecounter],$file_full_path);
            $img = Image::make($file_full_path);
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            /* Upload to s3 */
    //        \Storage::put(config('attendize.sideevent_images_path') . '/' . $filename, file_get_contents($file_full_path));
            $storagepaths[] = config('attendize.sideevent_images_path') . '/' . $filename;
        }
        return $storagepaths;
    }
}
