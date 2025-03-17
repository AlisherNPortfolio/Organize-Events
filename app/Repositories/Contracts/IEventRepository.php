<?php

namespace App\Repositories\Contracts;

interface IEventRepository extends IRepository
{
    public function findWithParticipants($id);
    public function getActiveEvents();
    public function getUserEvents($userId);
    public function getEventsWithOpenVoting();
    public function getUpcomingEvents();
    public function getEventsByStatus(string $status);
}
