<?php

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'El recurso solicitado no fue encontrado.'
                ], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método HTTP no permitido para esta ruta.'
                ], 405);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los datos enviados no son válidos.',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = 500;
                $message = 'Ocurrió un error interno en el servidor.';

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                    $message = $e->getMessage();
                }

                if (config('app.debug')) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'trace' => $e->getTrace()
                    ], $status);
                }

                return response()->json([
                    'success' => false,
                    'message' => $message
                ], $status);
            }
        });
    })->create();
