<?php

namespace App\Repositories\Contracts;

interface ICommonRepository
{
    public function getUsersCount();
    public function getEventsCount();
    public function getCreatorsCount();
    public function getActiveEventsCount();
    public function getUpcomingEventsCount();
    public function getCompletedEventsCount();
    public function getCancelledEventsCount();
    public function getFinesCount();
    public function getActiveFinesCount();
    public function getLatestUsers();
    public function getLatestEvents();
    public function getAllUsers();
}
