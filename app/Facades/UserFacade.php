<?php

namespace App\Facades;

use App\Repositories\Contracts\IUserFineRepository;
use App\Repositories\Contracts\IUserRepository;
use App\Services\AuthService;
use App\Services\FineService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserFacade
{
    public function __construct(
        protected AuthService $authService,
        protected FineService $fineService,
        protected IUserRepository $userRepository,
        protected IUserFineRepository $userFineRepository
    ) {
    }

    public function register(array $data)
    {
        return $this->authService->register($data);
    }

    public function login(string $email, string $password, bool $remember = false)
    {
        return $this->authService->login($email, $password, $remember);
    }

    public function logout()
    {
        return $this->authService->logout();
    }

    public function getCurrentUser()
    {
        return $this->authService->getCurrentUser();
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

    public function getUserFineStatus($userId)
    {
        return $this->fineService->checkUserFineStatus($userId);
    }

    public function applyFine($userId, $eventId, $reason)
    {
        return $this->fineService->applyFine($userId, $eventId, $reason);
    }

    /**
     * Apply a manual fine to a user (used by admins)
     */
    public function applyManualFine($userId, $reason, $durationDays)
    {
        $fine = $this->userFineRepository->create([
            'user_id' => $userId,
            'event_id' => null, // Manual fine doesn't need an event
            'reason' => $reason,
            'duration_days' => $durationDays,
            'fine_until' => now()->addDays($durationDays)
        ]);

        $this->userRepository->update([
            'fine_until' => $fine->fine_until
        ], $userId);

        return $fine;
    }

    /**
     * Remove a fine from a user (used by admins)
     */
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

    public function isUserFined($userId)
    {
        return $this->userRepository->checkIfUserIsFined($userId);
    }

    public function getUser($userId)
    {
        return $this->userRepository->find($userId);
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }

    /**
     * Get users paginated
     */
    public function getPaginatedUsers($perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->userRepository->findByRole($role);
    }

    /**
     * Check if user has specific permission
     */
    public function userHasPermission($userId, $permissionSlug)
    {
        $user = $this->getUser($userId);
        return $user->hasPermission($permissionSlug);
    }

    /**
     * Get all permissions for a user
     */
    public function getUserPermissions($userId)
    {
        $user = $this->getUser($userId);
        return $user->getAllPermissions();
    }
}
