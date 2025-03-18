<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventUpdateStatusRequest;
use App\Services\EventService;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
    }

    public static function middleware()
    {
        return [
            'role:admin'
        ];
    }

    /**
     * Display a listing of all events.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $events = $this->eventService->getActiveEventsQuery()->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $event = $this->eventService->getEvent($id);
        $eventImages = $event->images()->with('user')->get();

        return view('admin.events.show', compact('event', 'eventImages'));
    }

    /**
     * Update the event status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(EventUpdateStatusRequest $request, $id)
    {
        $request->validated();

        $status = $request->input('status');

        if ($status === 'completed') {
            $this->eventService->completeEvent($id);
        } elseif ($status === 'cancelled') {
            $this->eventService->cancelEvent($id);
        } else {
            $this->eventService->updateEvent($id, ['status' => $status]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Tadbir holati yangilandi.');
    }

    /**
     * Remove the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->eventService->deleteEvent($id);

        return redirect()->route('admin.events.index')
            ->with('success', 'Tadbir o\'chirildi.');
    }
}
