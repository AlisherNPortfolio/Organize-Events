<?php

namespace App\Facades;

use App\Factories\EventFactory;
use App\Services\EventService;
use App\Services\ParticipantService;
use App\Services\ImageUploadService;
use App\Services\NotificationService;
use Illuminate\Http\UploadedFile;

class EventFacade
{
    public function __construct(
        protected EventService $eventService,
        protected ParticipantService $participantService,
        protected ImageUploadService $imageUploadService,
        protected NotificationService $notificationService,
        protected EventFactory $eventFactory
    ) {
    }

    public function createEvent(array $data, ?UploadedFile $image = null, string $eventType = 'custom')
    {
        $event = null;
// TODO: Eventlarni interface va klas yordamida yaratadigan qilish
        switch ($eventType) {
            case 'sport':
                $event = $this->eventFactory->createSportEvent($data);
                break;
            case 'meetup':
                $event = $this->eventFactory->createMeetupEvent($data);
                break;
            case 'travel':
                $event = $this->eventFactory->createTravelEvent($data);
                break;
            case 'custom':
            default:
                $event = $this->eventFactory->createCustomEvent($data);
                break;
        }

        if ($image && $event) {
            $event = $this->eventService->updateEvent($event->id, [], $image);
        }

        $this->notificationService->sendEventCreationNotification($event);

        return $event;
    }

    public function updateEvent($id, array $data, ?UploadedFile $image = null)
    {
        $event = $this->eventService->updateEvent($id, $data, $image);
        return $event;
    }

    public function cancelEvent($id)
    {
        return $this->eventService->cancelEvent($id);
    }

    public function completeEvent($id)
    {
        return $this->eventService->completeEvent($id);
    }

    public function voteForEvent($eventId, $userId)
    {
        $result = $this->participantService->voteForEvent($eventId, $userId);

        if ($result['status']) {
            $event = $this->eventService->getEvent($eventId);
            $user = $event->participants()->where('user_id', $userId)->first()->user;
            $this->notificationService->sendEventParticipationNotification($event, $user);
        }

        return $result;
    }

    public function cancelVote($eventId, $userId)
    {
        return $this->participantService->cancelVote($eventId, $userId);
    }

    public function markAttendance($participantId, $status)
    {
        $participant = $this->participantService->markAttendance($participantId, $status);

        if ($participant && $status === 'no_show') {
            $event = $participant->event;
            $user = $participant->user;
            $this->notificationService->sendFineNotification($user, $event);
        }

        return $participant;
    }

    public function uploadEventImage($eventId, $userId, UploadedFile $image)
    {
        return $this->imageUploadService->uploadEventImage($eventId, $userId, $image);
    }

    public function getEventImages($eventId)
    {
        return $this->imageUploadService->getEventImages($eventId);
    }

    public function getEvent($id)
    {
        return $this->eventService->getEvent($id);
    }

    public function getActiveEvents()
    {
        return $this->eventService->getActiveEvents();
    }

    public function getUpcomingEvents()
    {
        return $this->eventService->getUpcomingEvents();
    }

    public function getEventsWithOpenVoting()
    {
        return $this->eventService->getEventsWithOpenVoting();
    }
}
