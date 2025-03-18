<?php

namespace App\Services;

use App\Repositories\Contracts\IUserRepository;
use App\Repositories\UserFineRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(protected IUserRepository $userRepository, protected UserFineRepository $userFineRepository)
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

    public function getUser($userId)
    {
        return $this->userRepository->find($userId);
    }

    public function applyManualFine($userId, $reason, $durationDays)
    {
        $fine = $this->userFineRepository->create([
            'user_id' => $userId,
            'event_id' => null,
            'reason' => $reason,
            'duration_days' => $durationDays,
            'fine_until' => now()->addDays($durationDays)
        ]);

        $this->userRepository->update([
            'fine_until' => $fine->fine_until
        ], $userId);

        return $fine;
    }

    public function removeFine($userId)
    {
        $this->userRepository->update([
            'fine_until' => null
        ], $userId);

        $activeFines = $this->userFineRepository->getUserActiveFines($userId);

        foreach ($activeFines as $fine) {
            $fine->fine_until = now();
            $fine->save();
        }

        return true;
    }
}
