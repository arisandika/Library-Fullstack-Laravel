<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('member') || $request->is('member/*')) {
                return route('member.login');
            } elseif ($request->is('admin') || $request->is('admin/*')) {
                return route('login');
            }
            return route('member.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if (\Auth::guard('member')->check() && $request->is('member/*')) {
                return route('member.dashboard');
            } elseif (\Auth::guard('admin')->check() && $request->is('admin/*')) {
                return route('admin.dashboard');
            }
            return route('member.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
