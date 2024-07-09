<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jackiedo\Cart\Facades\Cart;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login-register')->with([
            'authTitle' => 'Giriş Yap | Ekuralkan.com',
            'authDesc' => 'Ekuralkan.com hesabınıza güvenli giriş yapın. Kişiselleştirilmiş deneyim ve özel avantajlar için hemen oturum açın.'
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $cart = Cart::name('current');

        $redirectTo = count($cart->getItems()) > 0 ? route('cart') : RouteServiceProvider::HOME;

        return redirect()->intended($redirectTo);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $locale =  $request->session()->get('locale');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->session()->put('locale', $locale);

        return redirect('/');
    }
}
