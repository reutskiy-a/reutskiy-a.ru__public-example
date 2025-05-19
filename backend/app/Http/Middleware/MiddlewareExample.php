<?php

namespace App\Http\Middleware;

//use App\Utils\Tgbot;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MiddlewareExample
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        Tgbot::msg([
//            'origin' => $request->header('Origin'),
//            'method' => $request->method(),
//            'url' => $request->fullUrl(),
//        ]);

        return $next($request);
    }
}
