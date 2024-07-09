<?php

namespace App\Http\Controllers;

use App\Jobs\SendSMSJob;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Services\SMSTemplateParser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function requestVerificationForPhone(Request $request)
    {
        try {
            $phone = str_replace(" ", "", $request->input('phone'));
            $fullName = $request->input('fullName');

            if (empty($phone)) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            $code = VerificationCode::generateUniquePin();

            $customerPhoneExists = User::role(User::ROLES['customer'])->where('phone', $phone)->first();

            if ($customerPhoneExists) {
                $parts = explode('@', $customerPhoneExists->email);

                $username = $parts[0];
                $domain = $parts[1];

                $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
                $maskedDomain = substr($domain, 0, 1) . str_repeat('*', strlen($domain) - 5) . substr($domain, -4);

                $maskedEmail = $maskedUsername . '@' . $maskedDomain;

                return response()->json([
                    'status' => false,
                    'message' =>  __('web.phone-belongs-to-user', ['email' => $maskedEmail])
                ], 400);
            }

            $message = SMSTemplateParser::quickRegister($fullName, $code);

            dispatch(new SendSMSJob($phone, $message));

            VerificationCode::removePinsForPhone($phone);

            $verification = VerificationCode::create([
                'phone' => $phone,
                'code' => $code
            ]);

            if ($verification) {
                return response()->json([
                    'status' => true
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'requestVerificationForPhone', ['e' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        }
    }

    public function verifyCode(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $code = $request->input('code');

            if (empty($phone) || empty($code)) {
                return response()->json([
                    'status' => false,
                    'message' => __('app.phone-code-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            $verification = VerificationCode::where([
                ['phone', $phone],
                ['code', $code]
            ])->exists();

            if ($verification) {
                return response()->json([
                    'status' => true
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'verifyCode', ['e' => $e]);
        }
    }

    public function checkUniqueEmail(Request $request)
    {
        try {
            $email = str_replace(" ", "", $request->input('email'));
            $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

            if (empty($email) || !$isEmail) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.email-required')
                ], 400);
            }

            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                $maskedPhone = substr($existingUser->phone, 0, 2) . str_repeat('*', (strlen($existingUser->phone) - 6)) . substr($existingUser->phone, -4);

                return response()->json([
                    'status' => true,
                    'phone' => $maskedPhone
                ], 200);
            }

            return response()->json([
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'checkUniqueEmail', ['e' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        }
    }

    public function checkUniquePhone(Request $request)
    {
        try {
            $phone = str_replace(" ", "", $request->input('phone'));

            if (empty($phone)) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            $existingUser = User::where('phone', $phone)->first();

            if ($existingUser) {
                $parts = explode('@', $existingUser->email);

                $username = $parts[0];
                $domain = $parts[1];

                $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
                $maskedDomain = substr($domain, 0, 1) . str_repeat('*', strlen($domain) - 5) . substr($domain, -4);

                $maskedEmail = $maskedUsername . '@' . $maskedDomain;

                return response()->json([
                    'status' => false,
                    'message' => __('web.email-phone-belongs-to-user', ['email' => $maskedEmail])
                ], 200);
            }

            return response()->json([
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'checkUniquePhone', ['e' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        }
    }

    public function requestVerificationFromShop(Request $request)
    {
        try {
            $phone = str_replace(" ", "", $request->input('phone'));
            $email = str_replace(" ", "", $request->input('email'));
            $fullName = $request->input('fullName');
            $shop = auth()->user();

            if (empty($phone) || empty($email)) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-email-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            $code = VerificationCode::generateUniquePin();

            $existingUser = User::role(User::ROLES['customer'])->where('email', $email)->first();

            if ($existingUser) {
                $phone = $existingUser->phone;

                VerificationCode::removePinsForPhone($phone);

                $verification = VerificationCode::create([
                    'phone' => $phone,
                    'code' => $code
                ]);

                if (!$verification) {
                    return response()->json([
                        'status' => false,
                        'message' => __('app.error-occured')
                    ], 400);
                }

                $message = SMSTemplateParser::existingUserVerification($existingUser->full_name, $shop->full_name, $code);

                dispatch(new SendSMSJob($phone, $message));

                return response()->json([
                    'status' => true,
                ], 200);
            } else {
                VerificationCode::removePinsForPhone($phone);

                $verification = VerificationCode::create([
                    'phone' => $phone,
                    'code' => $code
                ]);

                if (!$verification) {
                    return response()->json([
                        'status' => false,
                        'message' => __('app.error-occured')
                    ], 400);
                }

                $message = SMSTemplateParser::userRegisterByShop($fullName, $shop->full_name, $code);

                dispatch(new SendSMSJob($phone, $message));

                return response()->json([
                    'status' => true,
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'requestVerificationFromShop', ['e' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        }
    }

    public function verifyCodeFromShop(Request $request)
    {
        try {
            $phone = str_replace(" ", "", $request->input('phone'));
            $email = $request->input('email');
            $code = $request->input('code');

            if (empty($phone) || empty($code) || empty($email)) {
                return response()->json([
                    'status' => false,
                    'message' => __('app.phone-code-email-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            $existingUser = User::role(User::ROLES['customer'])->where('email', $email)->first();

            if ($existingUser) {
                $phone = $existingUser->phone;
            }

            $verification = VerificationCode::where([
                ['phone', $phone],
                ['code', $code]
            ])->exists();

            if ($verification) {
                VerificationCode::removePinsForPhone($phone);

                if ($existingUser) {
                    return response()->json([
                        'status' => true,
                        'userData' => $existingUser->getInvoiceInformation(true)
                    ], 200);
                }

                return response()->json([
                    'status' => true
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'verifyCodeFromShop', ['e' => $e]);
        }
    }

    public function requestProfileVerification(Request $request)
    {
        try {
            $phone = str_replace(" ", "", $request->input('phone'));

            if (empty($phone)) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            VerificationCode::removePinsForPhone($phone);

            $code = VerificationCode::generateUniquePin();

            $user = auth()->user();

            $customerPhoneExists = User::role(User::ROLES['customer'])->where([
                ['phone', $phone],
                ['id', '!=', $user->id]
            ])->exists();

            if ($customerPhoneExists) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-is-used')
                ], 400);
            }

            $message = SMSTemplateParser::defaultVerificationCode($user->full_name, $code);

            dispatch(new SendSMSJob($phone, $message));

            $verification = VerificationCode::create([
                'phone' => $phone,
                'code' => $code
            ]);

            if ($verification) {
                return response()->json([
                    'status' => true
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'requestProfileVerification', ['e' => $e]);

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        }
    }

    public function verifyProfileCode(Request $request)
    {
        try {
            $phone = str_replace(" ", "", $request->input('phone'));
            $code = $request->input('code');

            if (empty($phone) || empty($code)) {
                return response()->json([
                    'status' => false,
                    'message' => __('app.phone-code-required')
                ], 400);
            }

            if (!str_contains($phone, "+")) {
                return response()->json([
                    'status' => false,
                    'message' => __('web.phone-number-note')
                ], 400);
            }

            $verification = VerificationCode::where([
                ['phone', $phone],
                ['code', $code]
            ])->exists();

            if ($verification) {
                return response()->json([
                    'status' => true
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => __('app.error-occured')
            ], 400);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Verification, 'verifyProfileCode', ['e' => $e]);
        }
    }

    public function passwordUpdate(Request $request)
    {
        try {
            $user = auth()->user();

            $password = $request->input('password');
            $passwordConfirmation = $request->input('password_confirmation');
            $currentPassword = $request->input('current_password');

            if ($password != $passwordConfirmation) {
                return back()->with('error', __('app.passwords-dont-match'));
            }

            if (!Hash::check($currentPassword, $user->password)) {
                return back()->with('error', __('app.wrong-password'));
            }


            auth()->user()->update([
                'password' => Hash::make($password)
            ]);

            return back()->with('success', __('app.password-updated'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Password update', ['e' => $e]);

            return back()->with('error', __('app.error-occured'));
        }
    }
}
