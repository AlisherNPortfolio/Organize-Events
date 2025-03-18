<?php

namespace App\Services;

use App\Repositories\Contracts\IUserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(protected IUserRepository $userRepository)
    {
    }

    public function getUserEvents()
    {
        return $this->userRepository->getUserWithEvents(Auth::id());
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function updateProfile($userId, array $data, ?UploadedFile $avatar = null)
    {
        if ($avatar) {
            $user = $this->userRepository->find($userId);

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $avatar->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        return $this->userRepository->update($data, $userId);
    }
}
