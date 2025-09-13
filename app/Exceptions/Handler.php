<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // You can log errors here if you want
        });
    }

    public function render($request, Throwable $e)
    {
        // If it's an HTTP exception, check its status code
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();

            if (in_array($status, [401, 403, 404, 419, 429, 503])) {
                return response()->view("errors.$status", [], $status);
            }
        }

        // All other errors -> show 500
        return response()->view('errors.500', [], 500);
    }
}
