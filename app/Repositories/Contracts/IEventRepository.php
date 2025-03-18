<?php

namespace App\Repositories\Contracts;

interface IEventRepository extends IRepository
{
    public function findWithParticipants($id);
    public function getActiveEvents();
    public function getUserEvents($userId, array $relations = []);
    public function getEventsWithOpenVoting();
    public function getUpcomingEvents();
    public function getUpcomingEventsQuery();
    public function getEventsByStatus(string $status);
    public function getActiveEventsQuery();
}
