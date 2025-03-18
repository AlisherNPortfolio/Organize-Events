<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findWithFines($id)
    {
        return $this->model->with('fines')->findOrFail($id);
    }

    public function getUserWithActiveEvents($id)
    {
        return $this->model->with(['events' => function($query) {
            $query->where('status', 'active');
        }])->findOrFail($id);
    }

    public function checkIfUserIsFined($id)
    {
        $user = $this->find($id);
        return $user->isFined();
    }

    public function getUserWithEvents($id)
    {
        return $this->model->with('events')->findOrFail($id);
    }
}
