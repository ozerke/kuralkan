<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AllowToLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->hasRole(User::ROLES['service']))) {
            $locale =  $request->session()->get('locale');

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            $request->session()->put('locale', $locale);

            return redirect()->route('login')->with('error', __('web.not-authorized-to-login'));
        }

        if (auth()->check() && (auth()->user()->user_active === 'N')) {
            $locale =  $request->session()->get('locale');

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            $request->session()->put('locale', $locale);

            return redirect()->route('login')->with('error', __('web.not-authorized-to-login'));
        }

        return $next($request);
    }
}
