<?php

use App\Contracts\Services\LoggerServiceInterface;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Illuminate\Http\Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->renderable(function (Throwable $e, Illuminate\Http\Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = 500;
            $message = 'Internal server error';

            switch (true) {
                case $e instanceof Illuminate\Validation\ValidationException:
                    $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                    $message = $e->validator->errors()->toArray();
                    break;

                case $e instanceof Illuminate\Auth\AuthenticationException:
                    $status = Response::HTTP_UNAUTHORIZED;
                    $message = 'Unauthenticated';
                    break;

                case $e instanceof Illuminate\Auth\Access\AuthorizationException:
                    $status = Response::HTTP_FORBIDDEN;
                    $message = $e->getMessage() ?: 'Forbidden';
                    break;

                case $e instanceof Symfony\Component\HttpKernel\Exception\HttpExceptionInterface:
                    $status = $e->getStatusCode();
                    $message = $e->getMessage() ?: Response::$statusTexts[$status];
                    break;

                case $e instanceof Illuminate\Database\Eloquent\ModelNotFoundException:
                    $status = Response::HTTP_NOT_FOUND;
                    $message = 'Resource not found';
                    break;

                default:
                    break;
            }

            return response()->json([
                'error' => $message,
                'code'  => $status,
            ], $status);
        });

        $exceptions->reportable(function (Throwable $e) {
            Log::error('Exception reported: '.$e->getMessage(), [
                'exception' => $e,
            ]);
        });
    })->create();
