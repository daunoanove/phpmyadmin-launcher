<?php

namespace App\Http\Middleware;

use Closure;

class AllowedIpAddressesMiddleware {
    public function handle($request, Closure $next)
    {
    	if ( true === config('app.useRestrictIpAddress') )
    	{
    		if ( !is_array(config('app.allowedIpAddresses')) )
    		{
    			$error = true;
    		}

            if ( !isset($error) AND !in_array($request->ip(), config('app.allowedIpAddresses')) )
            {
                $error = true;
            }

            if ( isset($error) )
            {
                return response()->json(['message' => \Symfony\Component\HttpFoundation\Response::$statusTexts[401]], 401);
            }
        }

        return $next($request);
    }
}