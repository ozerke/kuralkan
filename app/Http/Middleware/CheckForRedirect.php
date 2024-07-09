<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckForRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = trim($request->path(), '/');
        $slug = ltrim($uri, '/');

        if (empty($slug)) {
            return $next($request);
        }

        $redirect = Redirect::where('source_url', $slug)->first();

        if (!$redirect) {
            return $next($request);
        }

        return redirect($redirect->target_url, 301);
    }
}
