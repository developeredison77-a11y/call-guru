<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request): RedirectResponse
    {
        if (
            ! Schema::hasColumn('users', 'email')
            || ! Schema::hasColumn('users', 'password')
            || ! Schema::hasColumn('users', 'type')
        ) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Dashboard login is not ready. Please run the latest database migrations.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'type' => 1], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match a superadmin account.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
