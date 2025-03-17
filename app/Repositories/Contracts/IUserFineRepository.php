<?php

namespace App\Repositories\Contracts;

interface IUserFineRepository extends IRepository
{
    public function getUserActiveFines($userId);
    public function createFineForNoShow($userId, $eventId);
    public function checkActiveFineDuration($userId);
}
