<?php

namespace App\Services;

use App\Repositories\Contracts\IEventParticipantRepository;
use App\Repositories\Contracts\IEventRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EventService
{
    public function __construct(
        protected IEventRepository $eventRepository,
        protected IEventParticipantRepository $participantRepository
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

    private function uploadEventImage(UploadedFile $image)
    {
        $path = $image->store('events', 'public');
        return $path;
    }

    public function updateEvent($id, array $data, ?UploadedFile $image = null)
    {
        $event = $this->eventRepository->find($id);

        if ($image) {
            // Delete old image if exists
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }

            $imagePath = $this->uploadEventImage($image);
            $data['image_path'] = $imagePath;
        }

        $this->eventRepository->update($data, $id);

        return $this->eventRepository->find($id);
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

    public function getUserEvents($userId)
    {
        return $this->eventRepository->getUserEvents($userId);
    }

    public function getActiveEvents()
    {
        return $this->eventRepository->getActiveEvents();
    }

    public function getUpcomingEvents()
    {
        return $this->eventRepository->getUpcomingEvents();
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
