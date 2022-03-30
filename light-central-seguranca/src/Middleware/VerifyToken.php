<?php

namespace Celepar\Light\CentralSeguranca\Middleware;


use Auth;
use Redirect;
use Celepar\Light\CentralSeguranca\CentralSeguranca;
use Closure;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( Auth::check() ){

            if(!CentralSeguranca::isTokenValid()){
                $url = CentralSeguranca::logout();
                return Redirect::to($url);
            }
        }

        return $next($request);
    }
}
