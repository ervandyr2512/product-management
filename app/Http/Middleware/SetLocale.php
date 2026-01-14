<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is in session
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }
        // Check if locale is in URL parameter
        elseif ($request->has('lang')) {
            $locale = $request->get('lang');
            $request->session()->put('locale', $locale);
        }
        // Default to config
        else {
            $locale = config('app.locale');
        }

        // Validate locale
        if (in_array($locale, config('app.available_locales'))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
