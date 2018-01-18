<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */

   public function render($request, Exception $e)
    {
  //      //check if exception is an instance of ModelNotFoundException.
  //      //or NotFoundHttpException
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\ModelNotFoundException or $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ) {
           // ajax 404 json feedback
            \Bugsnag::notifyError('404', $e);
            if ($request->ajax()) {
                return response()->json(['error' => 'Not Found'], 404);
            }
          // normal 404 view page feedback
            return response()->view('errors.missing', [], 404);
       }

///Looking to Catch token Mismatch.
       if ($e instanceof \Illuminate\Session\TokenMismatchException or $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException ) {
                   // ajax 404 json feedback
          \Bugsnag::notifyError('tokenMismatch', $e);
           if ($request->ajax()) {
               return response()->json(['error' => 'Not Found'], 500);
           }
 //          // normal 404 view page feedback
           return response()->view('errors.issue', [], 500);
      }





        return parent::render($request, $e);
    }










    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
