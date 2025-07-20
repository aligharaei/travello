<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('Lang', 'en');

        $supported = ['en', 'fa'];

        app()->setLocale(in_array($lang, $supported) ? $lang : 'en');
        return $next($request);
    }
}
