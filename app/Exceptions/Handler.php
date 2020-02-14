<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

use App\Http\Resources\Json as JsonResource;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (request()->is('api/*')) {
            if($exception instanceof ValidationException) {
                $errors = [];

                foreach($exception->errors() as $key => $value) {
                    $errors[] = [
                        'field' => $key,
                        'messages' => $value
                    ];
                }
                
                return JsonResource::make($errors)
                    ->withError($exception->getMessage())
                    ->withHttpStatus(422);
            }
        }
        return parent::render($request, $exception);
    }
}
