<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewRegistrationAlert;
use App\Mail\RegistrationReceived;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // status defaults to "pending" and is_admin to false in the database.
        // The user is NOT logged in: an admin has to approve them first.
        $user = User::create($data);

        Notify::send($user, new RegistrationReceived($user));
        Notify::send(config('gloshipa.admin_email'), new NewRegistrationAlert($user));

        return redirect()->route('login')->with(
            'status',
            'Thanks! Your registration is in. An administrator will review it and we will email you once your account is approved.'
        );
    }
}
