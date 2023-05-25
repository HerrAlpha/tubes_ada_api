<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (HttpException $e) {
            $statusCode = $e->getStatusCode();

            switch ($statusCode) {
                case 404:
                    $message = 'Not Found!';
                    break;
                case 413:
                    $message = 'Content Too Large!';
                    break;

                default:
                    $message = 'Gateway Timeout!';
                    break;
            }

            return response()->json([
                'success'   => false,
                'message'   => config('app.env') == 'production' ? $message : $e->getMessage()
            ], $statusCode);
        });
    }
}
