<?php

namespace App\Http\Middleware;

use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentOrigin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!App::environment('production')) {
            return $next($request);
        }

        $allowedOrigin = 'https://portal.buluttahsilat.com';
        $allowedReferer = 'https://portal.buluttahsilat.com/';

        $requestOrigin = $request->headers->get('origin');
        $requestReferer = $request->headers->get('referer');

        if ($requestOrigin !== $allowedOrigin || $requestReferer !== $allowedReferer) {
            LoggerService::logError(LogChannelsEnum::Application, '[UNAUTHORIZED PAYMENT ORIGIN]: Middleware rejection', ['request' => $request]);
            return response('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
