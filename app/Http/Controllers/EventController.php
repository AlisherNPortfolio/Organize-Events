<?php

namespace App\Http\Controllers;

use App\Facades\EventFacade;
use App\Facades\UserFacade;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct(protected EventFacade $eventFacade, protected UserFacade $userFacade)
    {
    }

    public static function middleware()
    {
        return ['auth'];
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 9);

        // TODO: Event facade o'chiriladi. Service klaslar to'g'ridan to'g'ri controllerda chaqiriladi.
        $activeEvents = $this->eventFacade->getActiveEventsQuery()->paginate($perPage);
        $upcomingEvents = $this->eventFacade->getUpcomingEventsQuery()->paginate($perPage);

        return view('events.index', compact('activeEvents', 'upcomingEvents'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('events.create')) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to create events.');
        }

        return view('events.create');
    }

    public function store(StoreEventRequest $request)
    {
        if (!Auth::user()->hasPermission('events.create')) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to create events.');
        }

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $event = $this->eventFacade->createEvent(
            $data,
            $request->hasFile('image') ? $request->file('image') : null,
            $request->input('event_type', 'custom')
        );

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show($id)
    {
        $event = $this->eventFacade->getEvent($id);
        $images = $this->eventFacade->getEventImages($id);
        $isCreator = $event->user_id === Auth::id();
        $currentUser = $this->userFacade->getCurrentUser();

        $isOwnEvent = $event->user_id === Auth::id();
        $isAdmin = $currentUser->isAdmin();

        if ($currentUser->isCreator() && !$isOwnEvent && !$currentUser->hasPermission('events.view-any')) {
            return redirect()->route('events.index')
                ->with('error', 'You can only view your own events.');
        }

        $userParticipation = $event->participants()->where('user_id', Auth::id())->first();

        $canUploadImages = $isCreator ||
                           $isAdmin ||
                           ($userParticipation && $event->status === 'active' &&
                            $currentUser->hasPermission('images.upload'));

        $eventImages = $this->eventFacade->getEventImages($id);

        return view('events.show', compact(
            'event',
            'images',
            'isCreator',
            'currentUser',
            'userParticipation',
            'canUploadImages',
            'eventImages'
        ));
    }

    public function edit($id)
    {
        $event = $this->eventFacade->getEvent($id);
        $currentUser = $this->userFacade->getCurrentUser();

        if ($event->user_id !== Auth::id() && !$currentUser->hasPermission('events.update-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to edit this event.');
        }

        return view('events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, $id)
    {
        $data = $request->validated();

        $event = $this->eventFacade->getEvent($id);
        $currentUser = $this->userFacade->getCurrentUser();

        if ($event->user_id !== Auth::id() && !$currentUser->hasPermission('events.update-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to edit this event.');
        }

        $this->eventFacade->updateEvent(
            $id,
            $data,
            $request->hasFile('image') ? $request->file('image') : null
        );

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $event = $this->eventFacade->getEvent($id);
        $currentUser = $this->userFacade->getCurrentUser();

        if ($event->user_id !== Auth::id() && !$currentUser->hasPermission('events.delete-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to delete this event.');
        }

        $this->eventFacade->cancelEvent($id);

        return redirect()->route('events.index')
            ->with('success', 'Event cancelled successfully!');
    }

    public function vote($id)
    {
        $currentUser = $this->userFacade->getCurrentUser();

        if (!$currentUser->hasPermission('events.participate')) {
            return redirect()->route('events.show', $id)
                ->with('error', 'You do not have permission to participate in events.');
        }

        $result = $this->eventFacade->voteForEvent($id, Auth::id());

        if ($result['status']) {
            return redirect()->route('events.show', $id)
                ->with('success', $result['message']);
        }

        return redirect()->route('events.show', $id)
            ->with('error', $result['message']);
    }

    public function cancelVote($id)
    {
        $currentUser = $this->userFacade->getCurrentUser();

        if (!$currentUser->hasPermission('events.participate')) {
            return redirect()->route('events.show', $id)
                ->with('error', 'You do not have permission to participate in events.');
        }

        $result = $this->eventFacade->cancelVote($id, Auth::id());

        if ($result['status']) {
            return redirect()->route('events.show', $id)
                ->with('success', $result['message']);
        }

        return redirect()->route('events.show', $id)
            ->with('error', $result['message']);
    }

    public function complete($id)
    {
        $event = $this->eventFacade->getEvent($id);
        $currentUser = $this->userFacade->getCurrentUser();

        if ($event->user_id !== Auth::id() && !$currentUser->hasPermission('events.complete-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to complete this event.');
        }

        $this->eventFacade->completeEvent($id);

        return redirect()->route('events.show', $id)
            ->with('success', 'Event marked as completed!');
    }

    public function myEvents()
    {
        $currentUser = $this->userFacade->getCurrentUser();

        $events = $this->eventFacade->getUserEvents(Auth::id());

        return view('events.my-events', compact('events'));
    }
}
