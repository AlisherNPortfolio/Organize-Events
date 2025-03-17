<?php

namespace App\Repositories\Contracts;

interface IEventParticipantRepository extends IRepository
{
    public function getUserParticipations($userId);
    public function getEventParticipants($eventId);
    public function getParticipant($eventId, $userId);
    public function markAsAttended($id);
    public function markAsNoShow($id);
    public function deleteParticipation($eventId, $userId);
}
