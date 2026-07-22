<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'microsoft'])) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, ['google', 'microsoft'])) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Your account is not registered in the system. Please contact your administrator.');
        }

        $user->update([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
        ]);

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
