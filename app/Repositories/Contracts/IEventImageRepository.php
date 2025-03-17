<?php

namespace App\Repositories\Contracts;

interface IEventImageRepository extends IRepository
{
    public function getEventImages($eventId);
    public function getUserUploadedImages($userId);
    public function countEventImages($eventId);
}
