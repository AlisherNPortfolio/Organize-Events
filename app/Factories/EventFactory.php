<?php

namespace App\Factories;

use App\Repositories\Contracts\IEventRepository;

class EventFactory
{
    public function __construct(protected IEventRepository $eventRepository)
    {
    }

    public function createSportEvent(array $data)
    {
        $eventData = array_merge([
            'min_participants' => 6,
            'max_participants' => 22, // For team sports like football
        ], $data);

        return $this->eventRepository->create($eventData);
    }

    public function createMeetupEvent(array $data)
    {
        $eventData = array_merge([
            'min_participants' => 2,
            'max_participants' => 30, // For casual meetups
        ], $data);

        return $this->eventRepository->create($eventData);
    }

    public function createTravelEvent(array $data)
    {
        $eventData = array_merge([
            'min_participants' => 3,
            'max_participants' => 15,
        ], $data);

        return $this->eventRepository->create($eventData);
    }

    public function createCustomEvent(array $data)
    {
        return $this->eventRepository->create($data);
    }
}
