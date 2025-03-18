<?php

namespace App\Http\Controllers;

use App\Factories\EventFactory;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Services\EventService;
use App\Services\ImageUploadService;
use App\Services\NotificationService;
use App\Services\ParticipantService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService,
        protected NotificationService $notificationService,
        protected EventFactory $eventFactory,
        protected ImageUploadService $imageUploadService,
        protected UserService $userService,
        protected ParticipantService $participantService
        )
    {
    }

    public static function middleware()
    {
        return ['auth'];
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 9);

        // TODO: Eventdagi va boshqa barcha paginationlar vue bilan moslashtiriladi
        $activeEvents = $this->eventService->getActiveEventsQuery()->paginate($perPage);
        $upcomingEvents = $this->eventService->getUpcomingEventsQuery()->paginate($perPage);

        return view('events.index', compact('activeEvents', 'upcomingEvents'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('events.create')) {
            return redirect()->route('events.index')
                ->with('error', 'Sizda tadbir yaratish uchun ruxsat yoq');
        }

        return view('events.create');
    }

    public function store(StoreEventRequest $request)
    {
        if (!Auth::user()->hasPermission('events.create')) {
            return redirect()->route('events.index')
                ->with('error', 'Sizda tadbir yaratish uchun ruxsat yoq');
        }

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $image = $request->hasFile('image') ? $request->file('image') : null;
        $eventType = $request->input('event_type', 'custom');

        $event = $this->eventService->createEventByType($eventType, $data, $image);

        $this->notificationService->sendEventCreationNotification($event);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show($id)
    {
        $event = $this->eventService->getEvent($id);
        $images = $this->imageUploadService->getEventImages($id);
        $currentUser = $this->userService->getCurrentUser();
        $isCreator = $event->user_id === $currentUser->id;


        $isOwnEvent = $event->user_id === $currentUser->id;
        $isAdmin = $currentUser->isAdmin();

        if ($currentUser->isCreator() && !$isOwnEvent && !$currentUser->hasPermission('events.view-any')) {
            return redirect()->route('events.index')
                ->with('error', 'Siz faqat o\'zingiz yaratgan tadbirni ko\'rishingiz mumkin.');
        }

        $userParticipation = $event->participants()->where('user_id', $currentUser->id)->first();

        $canUploadImages = $isCreator ||
                           $isAdmin ||
                           ($userParticipation && $event->status === 'active' &&
                            $currentUser->hasPermission('images.upload'));

        $eventImages = $this->imageUploadService->getEventImages($id);

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
        $event = $this->eventService->getEvent($id);
        $currentUser = $this->userService->getCurrentUser();

        if ($event->user_id !== $currentUser->id && !$currentUser->hasPermission('events.update-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Sizda bu tadbirni tahrirlash uchun ruxsat yoq');
        }

        return view('events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, $id)
    {
        $data = $request->validated();

        $event = $this->eventService->getEvent($id);
        $currentUser = $this->userService->getCurrentUser();

        if ($event->user_id !== $currentUser->id && !$currentUser->hasPermission('events.update-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Sizda bu tadbirni tahrirlash uchun ruxsat yoq');
        }

        $this->eventService->updateEvent(
            $id,
            $data,
            $request->hasFile('image') ? $request->file('image') : null
        );

        return redirect()->route('events.show', $event)
            ->with('success', 'Tadbir tahrirlandi!');
    }

    public function destroy($id)
    {
        $event = $this->eventService->getEvent($id);
        $currentUser = $this->userService->getCurrentUser();

        if ($event->user_id !== $currentUser->id && !$currentUser->hasPermission('events.delete-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Sizda bu tadbirni o\'chirish uchun ruxsat yoq');
        }

        $this->eventService->cancelEvent($id);

        return redirect()->route('events.index')
            ->with('success', 'Tadbir bekor qilindi!');
    }

    public function vote($id)
    {
        $currentUser = $this->userService->getCurrentUser();

        if (!$currentUser->hasPermission('events.participate')) {
            return redirect()->route('events.show', $id)
                ->with('error', 'Sizda bu tadbirga qatnashishga ruxsat yo\'q.');
        }

        $result = $this->participantService->voteForEvent($id, $currentUser->id);

        if ($result['status']) {
            $event = $this->eventService->getEvent($id);
            $user = $event->participants()->where('user_id', $currentUser->id)->first()->user;
            $this->notificationService->sendEventParticipationNotification($event, $user);
        }

        if ($result['status']) {
            return redirect()->route('events.show', $id)
                ->with('success', $result['message']);
        }

        return redirect()->route('events.show', $id)
            ->with('error', $result['message']);
    }

    public function cancelVote($id)
    {
        $currentUser = $this->userService->getCurrentUser();

        if (!$currentUser->hasPermission('events.participate')) {
            return redirect()->route('events.show', $id)
                ->with('error', 'Sizda tadbirlarga qatnashishga ruxsat yo\'q.');
        }

        $result = $this->participantService->cancelVote($id, Auth::id());

        if ($result['status']) {
            return redirect()->route('events.show', $id)
                ->with('success', $result['message']);
        }

        return redirect()->route('events.show', $id)
            ->with('error', $result['message']);
    }

    public function complete($id)
    {
        $event = $this->eventService->getEvent($id);
        $currentUser = $this->userService->getCurrentUser();

        if ($event->user_id !== $currentUser->id && !$currentUser->hasPermission('events.complete-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Sizda tadbirni yakunlash uchun ruxsat yo\'q.');
        }

        $this->eventService->completeEvent($id);

        return redirect()->route('events.show', $id)
            ->with('success', 'Tadbir yakunlandi!');
    }

    public function myEvents()
    {
        $currentUser = $this->userService->getCurrentUser();

        $events = $this->eventService->getUserEvents($currentUser->id);

        return view('events.my-events', compact('events'));
    }
}
