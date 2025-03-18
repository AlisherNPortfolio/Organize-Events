<?php

namespace App\Services;

use App\Factories\EventFactory;
use App\Repositories\Contracts\IEventParticipantRepository;
use App\Repositories\Contracts\IEventRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EventService
{
    public function __construct(
        protected IEventRepository $eventRepository,
        protected IEventParticipantRepository $participantRepository,
        protected EventFactory $eventFactory
    ) {
    }

    public function createEvent(array $data, ?UploadedFile $image = null)
    {
        if ($image) {
            $imagePath = $this->uploadEventImage($image);
            $data['image_path'] = $imagePath;
        }

        return $this->eventRepository->create($data);
    }

    public function createEventByType($eventType, array $data, ?UploadedFile $image = null)
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

        if ($image) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }

            $imagePath = $this->uploadEventImage($image);

            $event->image_path = $imagePath;
            $event->save();
        }

        return $event;
    }

    private function uploadEventImage(UploadedFile $image)
    {
        return $image->store('events', 'public');
    }

    public function updateEvent($id, array $data, ?UploadedFile $image = null)
    {
        $event = $this->eventRepository->find($id);

        if ($image) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }

            $imagePath = $this->uploadEventImage($image);
            $data['image_path'] = $imagePath;
        }

        $event->update($data);

        return $event;
    }

    public function deleteEvent($id)
    {
        $event = $this->eventRepository->find($id);

        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        return $this->eventRepository->delete($id);
    }

    public function getEvent($id)
    {
        return $this->eventRepository->findWithParticipants($id);
    }

    public function getUserEvents($userId, array $relations = [])
    {
        return $this->eventRepository->getUserEvents($userId, $relations);
    }

    public function getActiveEvents()
    {
        return $this->eventRepository->getActiveEvents();
    }

    public function getActiveEventsQuery()
    {
        return $this->eventRepository->getActiveEventsQuery();
    }

    public function getUpcomingEvents()
    {
        return $this->eventRepository->getUpcomingEvents();
    }

    public function getUpcomingEventsQuery()
    {
        return $this->eventRepository->getUpcomingEventsQuery();
    }

    public function getEventsWithOpenVoting()
    {
        return $this->eventRepository->getEventsWithOpenVoting();
    }

    public function completeEvent($id)
    {
        return $this->eventRepository->update(['status' => 'completed'], $id);
    }

    public function cancelEvent($id)
    {
        return $this->eventRepository->update(['status' => 'cancelled'], $id);
    }

    public function isEventCreator($eventId, $userId)
    {
        $event = $this->eventRepository->find($eventId);
        return $event->user_id === $userId;
    }
}
