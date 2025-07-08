<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function index(): View
    {
        return view('auth.login');
    }

    public function registration(): View
    {
        return view('auth.registration');
    }

    public function postLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:254'],
            'password' => ['required', 'string', 'min:4', 'max:254'],
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return to_route('dashboard')->with('success', 'You have successfully logged in.');
        }

        return to_route('login')->with('error', 'Invalid credentials.');
    }

    public function postRegistration(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:254'],
            'email'    => ['required', 'string', 'email', 'unique:users', 'max:254'],
            'password' => ['required', 'string', 'min:4', 'max:254'],
        ]);

        $payload = $request->only('name', 'email', 'password');
        $user = User::query()->create([
            'name'     => $payload['name'],
            'email'    => $payload['email'],
            'password' => Hash::make($payload['password']),
        ]);

        Auth::login($user);

        return to_route('dashboard')->with('success', 'You have successfully registered.');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        Session::flush();

        return to_route('login')->with('success', 'You have successfully logged out.');
    }

    public function dashboard(): RedirectResponse|View
    {
        return Auth::check()
            ? view('dashboard')
            : to_route('login');
    }

    public function settings(): RedirectResponse|View
    {
        return Auth::check()
            ? view('settings')
            : to_route('login');
    }
}
