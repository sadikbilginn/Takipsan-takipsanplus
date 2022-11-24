<?php

namespace App\Http\Middleware;

use Closure;

class Station
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

        if (auth()->check() && auth()->user()->company_id != 0) {
            if(session()->has('device')){
                return $next($request);
            }else{
                return redirect()->route('station.device');
            }
        }

        auth()->logout();
        return redirect()->route('station.login');
    }
}
