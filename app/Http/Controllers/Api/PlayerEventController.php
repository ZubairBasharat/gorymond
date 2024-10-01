<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\RecurringEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlayerEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Player $player
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Player $player)
    {
        $events = $player->events()->with('parent');
        if($request->date) {
            $date = Carbon::parse($request->date);

            $events->whereDate('start', $date);
        }
        if($request->month) {
            $month_start = Carbon::parse($request->month)->startOfMonth();
            $month_end = Carbon::parse($request->month)->endOfMonth()->addDays(7);

            $events->whereBetween('start', [$month_start, $month_end]);
        }

        $events->orderBy('start', 'asc');

        if($request->limit) {
            $events->limit($request->limit);
        }
        return [
           'events' => EventResource::collection($events->get()),
           'recurring' => RecurringEvent::with('events')->get()
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventStoreRequest $request
     * @param Player $player
     * @return EventResource
     */
    public function store(EventStoreRequest $request, Player $player)
    {
        $data = $request->toArray();

        $data['start'] = Carbon::parse($data['start']);
        $data['end'] = Carbon::parse($data['end']);

        $event =  $player->events()->create($data);

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     *
     * @param Player $player
     * @param Event $event
     * @return EventResource
     */
    public function show(Player $player, Event $event)
    {
        return  new EventResource($player->events()->find($event->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EventUpdateRequest $request
     * @param Player $player
     * @param Event $event
     * @return EventResource
     */
    public function update(EventUpdateRequest $request, Player $player, Event $event)
    {
        $mainEventId = $event->id;
        if($request->type == 'all') {
            if($event->parent_id) {
                $data = $request->except(['recurring_active', 'type', 'reminders', 'end_date_options', 'end_date', 'repeat_options']);
                $parentData = $request->only(['recurring_active', 'reminders', 'end_date_options', 'end_date', 'repeat_options']);
                $event->parent->update($parentData);
                return response()->json();
            } else {
                $data = $request->toArray();
            }
            $data['start'] = Carbon::parse($data['start']);
            $data['end'] = Carbon::parse($data['end']);

            $event->update($data);

            return new EventResource($event->refresh());
        }
        else {
            $data = $request->except(['created_at', 'updated_at', 'oldDate']);
            $data['start'] = Carbon::parse($data['start']);
            $data['end'] = Carbon::parse($data['end']);
            $rec = RecurringEvent::where('event_id', $mainEventId)->first();
            if($rec) {
                $event->update($data);
                // RecurringEvent::where('event_id', $mainEventId)->update([
                //     'date' => Carbon::parse($request->oldDate)->format('Y-m-d'),
                // ]);
            }
            else {
                $event = $player->events()->create($data);
                RecurringEvent::create([
                    'event_id' => $event->id,
                    'date' => Carbon::parse($request->oldDate)->format('Y-m-d'),
                    'parent_event' => $mainEventId
                ]);
            }
           
            return new EventResource($event);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Event $event
     * @return Response
     * @throws \Exception
     */
    public function destroy(Request $request, Player $player, Event $event)
    {
        if ($request->removeAll) {
            Event::where('parent_id', $event->parent_id)->orWhere('id', $event->parent_id)->delete();
            return response()->json('Success', 200);
        }
        // if($event->delete()) {
        //     RecurringEvent::where('parent_event', $event->id)->delete();
        //     return response()->json('Success', 200);
        // } else {
        //     return response()->json('Error', 500);
        // }
        if(request('type') == 'all') {
            $event->delete();
            $rec = RecurringEvent::where('parent_event', $event->id)->first();
            if($rec) {
                RecurringEvent::where('parent_event', $event->id)->delete();
            }
            return response()->json('Success', 200);
        }
        else if(request('type') == 'spec') {
            RecurringEvent::create([
                'event_id' => 0,
                'date' => Carbon::parse(request('date'))->format('Y-m-d'),
                'parent_event' => $event->id
            ]);
            return response()->json('Success', 200);
        }
    }
}
