<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrustHostingScheme;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Vercel terminates TLS before forwarding HTTP to the private PHP container.
        // Trust only its scheme header; do not trust forwarded host, port or client IP.
        $middleware->replace(TrustProxies::class, TrustHostingScheme::class);
        $middleware->web(append: [SetLocale::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidSignatureException $exception, Request $request) {
            if (! $request->routeIs('payments.scan', 'payments.scan-pay')) {
                return null;
            }

            return response()->view('rentals.scan-invalid', [], 403)->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer')->header('X-Robots-Tag', 'noindex, nofollow');
        });
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
