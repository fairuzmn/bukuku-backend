<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Utils\ResponseUtils;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, Request $request) {

            // 1. Determine Status Code
            // Default to 500 (Server Error)
            $statusCode = 500;

            // If it is a known HTTP error (404, 403, 401), use that code
            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
            }

            // 2. Return JSON
            // If debug mode is ON, you might want $e->getMessage(), 
            // otherwise just "Server Error" for security.
            // For this assessment, showing the message is fine.
            return ResponseUtils::errorResponse($e->getMessage(), $statusCode);
        });
    })->create();
