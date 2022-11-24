<?php

namespace App\Http\Middleware;

use Closure;
use App\Locale as LocaleLang;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(session()->has('locale')){
            app()->setLocale(session()->get('locale'));
        } else {
            $locale = LocaleLang::where('default', 1)->first();
            session()->put('locale', $locale->abbr);
            app()->setLocale($locale->abbr);
        }

        return $next($request);
    }
}
