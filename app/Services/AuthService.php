<?php

namespace App\Services;

use App\Repositories\Contracts\IUserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthService
{
    public function __construct(protected IUserRepository $userRepository)
    {
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    public function login(string $email, string $password, bool $remember = false)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        if ($user->status !== 'active') {
            return false;
        }

        Auth::login($user, $remember);
        Session::regenerate();

        return $user;
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return true;
    }

    public function resetPassword(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        $user->password = Hash::make($password);
        $user->save();

        return true;
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function isAuthenticated()
    {
        return Auth::check();
    }
}
