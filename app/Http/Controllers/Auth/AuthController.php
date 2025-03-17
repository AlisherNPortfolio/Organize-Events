<?php
namespace App\Http\Controllers\Auth;

use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected UserFacade $userFacade)
    {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->has('remember');

        $user = $this->userFacade->login($credentials['email'], $credentials['password'], $remember);

        if (!$user) {
            return back()->withErrors([
                'email' => 'Login yoki parol xato',
            ])->withInput($request->except('password'));
        }

        return redirect()->intended(route('events.index'));
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->userFacade->register($data);

        if (!$user) {
            return back()->withErrors([
                'email' => 'Registration failed. Please try again.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $this->userFacade->login($data['email'], $data['password']);

        return redirect()->route('events.index');
    }

    public function logout(Request $request)
    {
        $this->userFacade->logout();

        return redirect()->route('login');
    }
}
