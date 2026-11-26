<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $exception, Request $request) {
            if (! ($exception->getPrevious() instanceof TokenMismatchException)
                || ! $request->routeIs('logout')
                || $request->expectsJson()) {
                return null;
            }

            if (! $request->user()) {
                return redirect()->route('login', status: 303)->with('session_notice', 'Sesi sudah berakhir. Silakan masuk kembali.');
            }

            return redirect()->route('home-page', status: 303)->with('session_notice', 'Halaman sudah diperbarui. Klik Keluar sekali lagi.');
        });
    })->create();
