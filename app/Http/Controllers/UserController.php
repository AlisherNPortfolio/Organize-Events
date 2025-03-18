<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Services\FineService;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService, protected FineService $fineService)
    {
    }

    public static function middleware()
    {
        return [
            'auth',
        ];
    }

    public function profile()
    {
        $user = $this->userService->getCurrentUser();
        $fineStatus = $this->fineService->checkUserFineStatus($user->id);
        $userWithEvents = $this->userService->getUserEvents($user->id);

        return view('users.profile', [
            'user' => $user,
            'fineStatus' => $fineStatus,
            'userEvents' => $userWithEvents->events,
        ]);
    }

    public function edit()
    {
        $user = $this->userService->getCurrentUser();

        return view('users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request)
    {
        $user = $this->userService->getCurrentUser();

        $data = $request->validated();

        $this->userService->updateProfile(
            $user->id,
            $data,
            $request->hasFile('avatar') ? $request->file('avatar') : null
        );

        return redirect()->route('profile')
            ->with('success', 'Profil tahrirlandi!');
    }
}
