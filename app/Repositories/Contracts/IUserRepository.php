<?php

namespace App\Repositories\Contracts;

interface IUserRepository extends IRepository
{
    public function findByEmail(string $email);
    public function findWithFines($id);
    public function getUserWithActiveEvents($id);
    public function getUserWithEvents($id);
    public function checkIfUserIsFined($id);
}
