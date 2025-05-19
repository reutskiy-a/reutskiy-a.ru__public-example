<?php

use App\Http\Middleware\MiddlewareExample;
use App\Http\Middleware\User\Role;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->trustProxies(at: '*')
            ->appendToGroup('web', [])
            ->appendToGroup('api', [])
            ->append(MiddlewareExample::class)
            ->alias(['role' => Role::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
