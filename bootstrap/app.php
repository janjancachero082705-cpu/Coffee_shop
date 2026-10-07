<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Custom guest redirect based sa URL
        $middleware->redirectGuestsTo(function ($request) {
            // Portal routes → redirect sa portal login
            if ($request->is('portal') || $request->is('portal/*')) {
                return route('portal.login');
            }
            // Admin routes → default admin login
            return route('login');
        });

        // Custom authenticated redirect
        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('portal') || $request->is('portal/*')) {
                return route('portal.dashboard');
            }
            return '/admin';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();