<?php

namespace App\Http\Middleware;

use Closure;

class LauncherMiddleware {
    public function handle($request, Closure $next)
    {
        $launcher = app(\App\Launcher::class);

        try {
            $launcher->check();
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 406);
        }

        return $next($request);
    }
}