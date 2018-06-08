<?php
namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Organiser;
use App\Coupon;
use Illuminate\Http\Request;
use Image;
use Carbon\Carbon;
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
            /*return response()->json([
                'status'   => 'error',
                'messages' => $ticket->errors(),
            ]);*/
            $data = [
                'event' => $event,
                'callbackurl' => 'showReCreateSideEvent',
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
        $ticket->ticket_offers = empty($scheduleopts) ? null : implode("+++", $scheduleopts);
        $ticket->is_hidden = 0;

        //$ticket->save(); DON'T SAVE HERE WITHOUT PHOTO PATHS
        if ($request->hasFile('sideevent_image')) {
            $path = public_path() . '/' . config('attendize.sideevent_images_path');
            $filename = 'sideevent_image-' . md5(time() . $ticket->id) . '.' . strtolower($request->file('sideevent_image')->getClientOriginalExtension());
            $file_full_path = $path . '/' . $filename;
            $request->file('sideevent_image')->move($path, $filename);
            $img = Image::make($file_full_path);
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            //$img->save($file_full_path);
            /* Upload to s3 */
            \Storage::put(config('attendize.sideevent_images_path') . '/' . $filename, file_get_contents($file_full_path));
            //save path to ticket table
            $ticket->ticket_main_photo = config('attendize.sideevent_images_path') . '/' . $filename;
        }

    //    $countfiles = count($_FILES['files']['name']);

        if ($request->hasFile('files')) { //dd($request);//exit('got here');
        /*    $ticket_photos = [];
            $uploadednames = $_FILES['files']['name'];
            $path = public_path() . '/' . config('attendize.sideevent_images_path');
            for($filecounter=0;$filecounter<count($_FILES['files']['name']); ++$filecounter){
                $extension = substr($uploadednames[$filecounter], stripos($uploadednames[$filecounter],'.') + 1); //dd($extension);
                $filename = 'sideevent_image-' . md5(time() . $ticket->id . $uploadednames[$filecounter]) . '.' . $extension;
                $file_full_path = $path . '/' . $filename;
                move_uploaded_file($_FILES['files']['tmp_name'][$filecounter],$file_full_path);
                $img = Image::make($file_full_path);
                $img->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            //    $img->save($file_full_path);
                /* Upload to s3 *|/
                \Storage::put(config('attendize.sideevent_images_path') . '/' . $filename, file_get_contents($file_full_path));
                $ticket_photos[] = config('attendize.sideevent_images_path') . '/' . $filename; 
            }
        */
            $ticket_photos = $this->processuploadedimages('files',$ticket->id);
            if(!empty($ticket_photos)){$ticket->ticket_photos = implode(config('attendize.sideevent_photos_eximploders'),$ticket_photos);}
        }

    //------------working for content pages-------//
        $contentpagesinfos = [];
        if($request->has('content_pages')){
            foreach ($request->get('content_pages') as $contentnumber) {
                $pagetitle=null; $pagediscript=null;
                //check if title is available
                if($request->has('more_title_'.$contentnumber)){
                    $pagetitle=$request->get('more_title_'.$contentnumber);
                }else{
                    continue;
                }
                //check if description is available
                if($request->has('more_discription_'.$contentnumber)){
                    $pagediscript=$request->get('more_discription_'.$contentnumber);
                }else{
                    continue;
                }
                //check if photos for the page are uploaded
                $pageimages=null;
                if($request->hasFile('content_'.$contentnumber.'_files')){
                    $pageimagesarray=$this->processuploadedimages('content_'.$contentnumber.'_files',$ticket->id);
                    $pageimages=implode(config('attendize.sideevent_photos_eximploders'),$pageimagesarray);
                }
                $contentpagesinfos[] = implode(config('attendize.sideevent_singlepage_eximploders'), [$pagetitle, $pagediscript, $pageimages]);
            }
        }
        $sideevent_pages = empty($contentpagesinfos) ? null : implode(config('attendize.sideevent_pages_eximploders'),$contentpagesinfos);
    //--------------end of working for content pages----DonaldApril19-----// 

        //save sideevent pages information in ticket_extras
        $ticket->ticket_extras = $sideevent_pages;
        $ticket->save();    

        session()->flash('message', 'Successfully Created SideEvent');

        return redirect()->route('showSideEvents', ['event_id' => $event_id]);

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
    public function postEditSideEvent(Request $request, $event_id,  $ticket_id)
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
                'callbackurl' => 'showEditSideEvent',
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
        $ticket->ticket_offers = empty($scheduleopts) ? null : implode("+++", $scheduleopts);
        $ticket->is_hidden = 0;
        $ticket_photos = [];
        if($request->has('photos')){
            $eventphotos=$request->get('photos');
            for($photocounter=0;$photocounter<count($eventphotos);++$photocounter){
                if(!$request->has("remove_photo_$photocounter")){
                    $ticket_photos[]=$eventphotos[$photocounter];
                }
            }
        }

        if ($request->hasFile('sideevent_image')) {
            $path = public_path() . '/' . config('attendize.sideevent_images_path');
            $filename = 'sideevent_image-' . md5(time() . $ticket->id) . '.' . strtolower($request->file('sideevent_image')->getClientOriginalExtension());
            $file_full_path = $path . '/' . $filename;
            $request->file('sideevent_image')->move($path, $filename);
            $img = Image::make($file_full_path);
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            /* Upload to s3 */
            \Storage::put(config('attendize.sideevent_images_path') . '/' . $filename, file_get_contents($file_full_path));
            //save path to ticket table
            $ticket->ticket_main_photo = config('attendize.sideevent_images_path') . '/' . $filename;
        }
        if ($request->hasFile('files')) {
            $uploadednames = $_FILES['files']['name'];
            $path = public_path() . '/' . config('attendize.sideevent_images_path');
            for($filecounter=0;$filecounter<count($_FILES['files']['name']); ++$filecounter){
                $extension = substr($uploadednames[$filecounter], stripos($uploadednames[$filecounter],'.') + 1);
                $filename = 'sideevent_image-' . md5(time() . $ticket->id . $uploadednames[$filecounter]) . '.' . $extension;
                $file_full_path = $path . '/' . $filename;
                move_uploaded_file($_FILES['files']['tmp_name'][$filecounter],$file_full_path);
                $img = Image::make($file_full_path);
                $img->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                /* Upload to s3 */
                \Storage::put(config('attendize.sideevent_images_path') . '/' . $filename, file_get_contents($file_full_path));
                $ticket_photos[] = config('attendize.sideevent_images_path') . '/' . $filename; 
            }
        }

        if(!empty($ticket_photos)){$ticket->ticket_photos = implode(config('attendize.sideevent_photos_eximploders'),$ticket_photos);}

    $contentpagesinfos = [];
//---------working for existing content pages, deletion of a page, edits, photos remove and new photo uploads-------DonaldApril23-----
    if($request->has('existing_content_pages')){
        $existingpages=$request->get('existing_content_pages');
        foreach($existingpages as $existingpage){
            if(!$request->has('remove_page_'.$existingpage)){
                if($request->has('more_discription_'.$existingpage) && $request->has('more_title_'.$existingpage)){
                $pagetitle=$request->get('more_title_'.$existingpage);
                $pagediscript=$request->get('more_discription_'.$existingpage);
            $pageimages=null; $pageimagesarray=[];
            if($request->has('page_'.$existingpage.'_photos')){
                $existingpagephotos=$request->get('page_'.$existingpage.'_photos');
                for($photoct=0;$photoct<count($existingpagephotos);++$photoct){
                    if(!$request->has($existingpage.'_remove_photo_'.$photoct)){
                        $pageimagesarray[]=$existingpagephotos[$photoct];
                    }
                }
            }
            if($request->hasFile('content_'.$existingpage.'_files')){
                $pageimagesarray=array_merge($pageimagesarray, $this->processuploadedimages('content_'.$existingpage.'_files',$ticket_id));
            }
            if(!empty($pageimagesarray)){
                $pageimages=implode(config('attendize.sideevent_photos_eximploders'),$pageimagesarray);}
            $contentpagesinfos[] = implode(config('attendize.sideevent_singlepage_eximploders'), [$pagetitle, $pagediscript, $pageimages]);
                }
            }
        }
    }
//-----------end of working for existing content pages, saved in $contentpagesinfos[]

    //------------working for new content pages-------//
        if($request->has('content_pages')){
            foreach ($request->get('content_pages') as $contentnumber) {
                $pagetitle=null; $pagediscript=null;
                //check if title is available
                if($request->has('more_title_'.$contentnumber)){
                    $pagetitle=$request->get('more_title_'.$contentnumber);
                }else{
                    continue;
                }
                //check if description is available
                if($request->has('more_discription_'.$contentnumber)){
                    $pagediscript=$request->get('more_discription_'.$contentnumber);
                }else{
                    continue;
                }
                //check if photos for the page are uploaded
                $pageimages=null;
                if($request->hasFile('content_'.$contentnumber.'_files')){
                    $pageimagesarray=$this->processuploadedimages('content_'.$contentnumber.'_files',$ticket->id);
                    $pageimages=implode(config('attendize.sideevent_photos_eximploders'),$pageimagesarray);
                }
                $contentpagesinfos[] = implode(config('attendize.sideevent_singlepage_eximploders'), [$pagetitle, $pagediscript, $pageimages]);
            }
        }
    //--------------end of working for content pages----DonaldApril23-----// 
        $sideevent_pages = empty($contentpagesinfos) ? null : implode(config('attendize.sideevent_pages_eximploders'),$contentpagesinfos);
        $ticket->ticket_extras = $sideevent_pages;
        //dd($ticket);
        $ticket->save();

        session()->flash('message', 'Successfully Edited Side Event');
        return redirect()->route('showSideEvents', ['event_id' => $event_id]);

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
    public function showEditSideEvent($event_id, $sideevent_id)
    {
        $data = [
            'event'  => Event::scope()->find($event_id),
            'ticket' => Ticket::scope()->find($sideevent_id),
        ];

        return view('ManageEvent.Modals.EditSideEvent', $data);
    }

    //end of addition DonaldMar2 DonaldMar12


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



    /**
     * Deleted a side event
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteSideEvent(Request $request, $ticket_id)
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

            session()->flash('message','Sorry, you can\'t delete this side event as some tickets have already been sold.');
            return response()->redirectToRoute('showSideEvents', [
                'event_id'      => $ticket->event_id,
            ]);
        }

        $event_id = $ticket->event_id;
        if ($ticket->delete()) {
            session()->flash('message', $ticket->title.' Side Event Successfully Deleted.');
            return response()->redirectToRoute('showSideEvents', [
                'event_id'      => $event_id,
            ]);
        }

        Log::error('Ticket Failed to delete', [
            'ticket' => $ticket,
        ]);

        return response()->json([
            'status'  => 'error',
            'title'      => $ticket->title,
            'message' => 'Whoops! Looks like something went wrong. Please try again.',
        ]);
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

        $all_discounts = [];
        if(count($events)==0){goto nosuchevent;}
        foreach ($events as $event) {
            if(!$event->tickets){goto eventhasnotickets;}
            $tickets = [];
            foreach ($event->tickets as $ticket) {
                $tickets[$ticket->id] = $ticket->price;
            }
            $discounts = Coupon::where(['event_id'=>$event->id,'state'=>'Used'])->get();
            if(count($discounts)==0){goto nodiscounts;}
            $discount_sums=[];
            foreach($discounts as $discount){
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
            }
            $all_discounts[$event->id] = $discount_sums;
            nodiscounts:
            eventhasnotickets:
        }
        nosuchevent:

        $data = [
            'events'    => $events,
            'organiser' => $organiser,
            'search'    => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => $request->get('sort_by') ? $request->get('sort_by') : '',
                'showPast' => $request->get('past'),
            ],
            'event_discounts' => $all_discounts,
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

    public function showPayments(Request $request, $event_id){
        $event = Event::scope()->findOrfail($event_id);
        $allowed_sorts = ['full_name', 'txn_id', 'payment_date','amount'];
        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'payment_date');
        if($searchQuery){
            $payments = Payment::where('full_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('txn_id', 'like', '%' . $searchQuery . '%')
            ->orderBy($sort_by,
                'desc')->where('event_id', '=', $event_id)->paginate(12);
        }else{
            $payments = Payment::where('event_id', '=', $event_id)->orderBy($sort_by, 'desc')->paginate(12);
        }
        $data = [ 
            'payments'    => $payments,
            'event'         => $event,
            'search'    => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => $request->get('sort_by') ? $request->get('sort_by') : '',
            ],
        ];
        return view('ManageEvent.Payments', $data);
    }
}
