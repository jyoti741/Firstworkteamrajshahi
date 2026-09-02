<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSellerLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'en';

        // 1. Direct query param override e.g. ?lang=bn
        if ($request->has('lang') && in_array($request->query('lang'), ['en', 'bn'], true)) {
            $locale = (string) $request->query('lang');
            session(['seller_locale' => $locale]);

            if ($request->user()) {
                $request->user()->update(['locale' => $locale]);
            }
        } elseif ($request->user() && ! empty($request->user()->locale)) {
            // 2. Authenticated user preference
            $locale = $request->user()->locale;
            session(['seller_locale' => $locale]);
        } elseif (session()->has('seller_locale')) {
            // 3. Session preference fallback
            $locale = (string) session('seller_locale');
        }

        if (in_array($locale, ['en', 'bn'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
