<?php

namespace App\Http\Controllers;

use App\Facades\UserFacade;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    public function __construct(protected UserFacade $userFacade)
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
        $user = $this->userFacade->getCurrentUser();
        $fineStatus = $this->userFacade->getUserFineStatus($user->id);

        return view('users.profile', compact('user', 'fineStatus'));
    }

    public function edit()
    {
        $user = $this->userFacade->getCurrentUser();

        return view('users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request)
    {
        $user = $this->userFacade->getCurrentUser();

        $data = $request->validated();

        $this->userFacade->updateProfile(
            $user->id,
            $data,
            $request->hasFile('avatar') ? $request->file('avatar') : null
        );

        return redirect()->route('profile')
            ->with('success', 'Profil tahrirlandi!');
    }
}
