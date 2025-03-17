<?php

namespace App\Http\Controllers\Admin;

use App\Facades\EventFacade;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(protected EventFacade $eventFacade)
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
        $events = Event::with('creator')->orderBy('created_at', 'desc')->paginate(15);

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
        $event = $this->eventFacade->getEvent($id);
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
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,pending,completed,cancelled'
        ]);

        $status = $request->input('status');

        if ($status === 'completed') {
            $this->eventFacade->completeEvent($id);
        } elseif ($status === 'cancelled') {
            $this->eventFacade->cancelEvent($id);
        } else {
            $this->eventFacade->updateEvent($id, ['status' => $status]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event status updated successfully.');
    }

    /**
     * Remove the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->eventFacade->deleteEvent($id);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
