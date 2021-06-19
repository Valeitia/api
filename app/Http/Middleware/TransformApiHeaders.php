<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class TransformApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cookie_name = 'XSRF-TOKEN';
        $token_cookie = $request->cookie($cookie_name);

        if ($token_cookie !== null) {
            $request->headers->add(["X-$cookie_name" => $token_cookie]);
        }

        return $next($request);
    }
}
