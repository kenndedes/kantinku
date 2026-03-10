<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the buyer registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the seller registration view.
     */
    public function createSeller(): View
    {
        return view('auth.register-seller');
    }

    /**
     * Handle seller registration.
     */
    public function storeSeller(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'stand_name'    => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'seller',
            'photo'    => null,
        ]);

        $user->sellerProfile()->create([
            'status'     => 'pending',
            'stand_name' => $request->stand_name,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('seller.pending');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'register_as' => ['nullable', 'in:user,seller'],
        ]);

        $role = $request->input('register_as', 'user');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'photo' => null,
        ]);

        if ($role === 'seller') {
            $user->sellerProfile()->create([
                'status' => 'pending',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect($role === 'seller' ? route('seller.pending') : route('dashboard', absolute: false));
    }
}
