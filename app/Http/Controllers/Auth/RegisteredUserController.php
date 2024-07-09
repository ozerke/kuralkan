<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Mail\RegisteredMail;
use App\Models\User;
use App\Models\VerificationCode;
use App\Providers\RouteServiceProvider;
use App\Services\IYSService;
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
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
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
            'surname' => ['required', 'string', 'max:255'],
            'register_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'full_phone' => ['required', 'phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'pin_code' => ['required']
        ]);

        $customerPhoneExists = User::role(User::ROLES['customer'])->where('phone', $request->input('full_phone'))->first();

        if ($customerPhoneExists) {
            $parts = explode('@', $customerPhoneExists->email);

            $username = $parts[0];
            $domain = $parts[1];

            $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
            $maskedDomain = substr($domain, 0, 1) . str_repeat('*', strlen($domain) - 5) . substr($domain, -4);

            $maskedEmail = $maskedUsername . '@' . $maskedDomain;

            return back()->with('error', __('web.phone-belongs-to-user', ['email' => $maskedEmail]));
        }

        $code = $request->pin_code;

        $verification = VerificationCode::where([
            ['phone', $request->input('full_phone')],
            ['code', $code]
        ])->exists();

        if (!$verification) {
            return back()->with('error', __('web.verification-code-invalid'));
        }

        VerificationCode::removePinsForPhone($request->input('full_phone'));

        $user = User::create([
            'password' => Hash::make($request->password),
            'email' => $request->register_email,
            'site_user_name' => $request->name,
            'site_user_surname' => $request->surname,
            'district_id' => 1,
            'phone' => $request->input('full_phone'),
            'date_of_birth' => "1900-01-01",
            'fullname' => $request->name . ' ' . $request->surname
        ]);

        $user->assignRole(User::ROLES['customer']);
        $user->updateUserNo();

        IYSService::addIYS($request->register_email, $request->input('full_phone'));

        event(new Registered($user));

        dispatch(new SendEmailJob($user->email, new RegisteredMail($user)));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
