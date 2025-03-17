<?php

namespace App\Http\Controllers;

use App\Facades\EventFacade;
use App\Facades\UserFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    public function __construct(protected EventFacade $eventFacade, protected UserFacade $userFacade)
    {
    }

    public static function middleware()
    {
        return ['auth'];
    }

    public function index($eventId)
    {
        $event = $this->eventFacade->getEvent($eventId);
        $currentUser = $this->userFacade->getCurrentUser();

        if ($event->user_id !== Auth::id() && !$currentUser->hasPermission('participants.view-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to view participants.');
        }

        return view('participants.index', compact('event'));
    }

    public function updateStatus(Request $request, $eventId, $participantId)
    {
        $event = $this->eventFacade->getEvent($eventId);
        $currentUser = $this->userFacade->getCurrentUser();

        if ($event->user_id !== Auth::id() && !$currentUser->hasPermission('participants.manage-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to manage participants.');
        }

        $status = $request->input('status');

        if (!in_array($status, ['attended', 'no_show'])) {
            return redirect()->route('participants.index', $eventId)
                ->with('error', 'Invalid participant status.');
        }

        $this->eventFacade->markAttendance($participantId, $status);

        $statusText = $status === 'attended' ? 'attended' : 'absent';

        return redirect()->route('participants.index', $eventId)
            ->with('success', "Participant marked as {$statusText}!");
    }

    // Additional method for creator role
    public function myEventParticipants()
    {
        $currentUser = $this->userFacade->getCurrentUser();

        if (!$currentUser->isCreator() && !$currentUser->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        $events = $this->eventFacade->getUserEvents(Auth::id());

        $eventParticipants = [];
        foreach ($events as $event) {
            $participants = $event->participants()->with('user')->get();
            $eventParticipants[$event->id] = [
                'event' => $event,
                'participants' => $participants,
                'total' => $participants->count(),
                'attended' => $participants->where('status', 'attended')->count(),
                'no_show' => $participants->where('status', 'no_show')->count(),
            ];
        }

        return view('participants.my-event-participants', compact('eventParticipants'));
    }
}
