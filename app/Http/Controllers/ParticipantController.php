<?php

namespace App\Http\Controllers;

use App\Http\Requests\Participant\ParticipantUpdateStatusRequest;
use App\Services\EventService;
use App\Services\NotificationService;
use App\Services\ParticipantService;
use App\Services\UserService;

class ParticipantController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected EventService $eventService,
        protected ParticipantService $participantService,
        protected NotificationService $notificationService
        )
    {
    }

    public static function middleware()
    {
        return ['auth'];
    }

    public function index($eventId)
    {
        $event = $this->eventService->getEvent($eventId);
        $currentUser = $this->userService->getCurrentUser();

        if ($event->user_id !== $currentUser->id && !$currentUser->hasPermission('participants.view-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Sizda qatnashchilarni ko\'rishga ruxsat yo\'q.');
        }

        return view('participants.index', compact('event'));
    }

    public function updateStatus(ParticipantUpdateStatusRequest $request, $eventId, $participantId)
    {
        $event = $this->eventService->getEvent($eventId);
        $currentUser = $this->userService->getCurrentUser();

        if ($event->user_id !== $currentUser->id && !$currentUser->hasPermission('participants.manage-any')) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Sizda qatnashchilarni boshqarishga ruxsat yo\'q.');
        }

        $request->validated();
        $status = $request->input('status');

        $participant = $this->participantService->markAttendance($participantId, $status);

        if ($participant && $status === 'no_show') {
            $event = $participant->event;
            $user = $participant->user;
            $this->notificationService->sendFineNotification($user, $event);
        }

        $statusText = $status === 'attended' ? 'attended' : 'absent';

        return redirect()->route('participants.index', $eventId)
            ->with('success', "Qatnashchi {$statusText} sifatida belgilandi!");
    }

    // Additional method for creator role
    public function myEventParticipants()
    {
        $currentUser = $this->userService->getCurrentUser();

        if (!$currentUser->isCreator() && !$currentUser->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Bu sahifani ko\'rishga ruxsat yo\'q.');
        }

        $events = $this->eventService->getUserEvents($currentUser->id, ['participants.user']);


        $eventParticipants = [];
        foreach ($events as $event) {
            $participants = $event->participants;
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
