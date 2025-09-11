<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Si la requête attend du JSON, renvoyer une réponse JSON
        if ($request->expectsJson()) {
            $status = 500;
            $message = $exception->getMessage();

            if (method_exists($exception, 'getStatusCode')) {
                $status = $exception->getStatusCode();
            }

            return response()->json([
                'error' => $message ?: 'Une erreur est survenue.',
            ], $status);
        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
