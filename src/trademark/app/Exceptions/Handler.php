<?php

namespace App\Exceptions;

use App\Helpers\CommonHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
        //     if ($request->ajax()) {
        //         return response()->json([
        //             'message' => __('messages.permission_denied'),
        //         ], CODE_ERROR_500);
        //     } else {
        //         CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.permission_denied'));
        //         return redirect()->back();
        //     }
        // });

        return parent::render($request, $exception);
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

        $guard = \Arr::get($exception->guards(), 0);

        switch ($guard) {
            case 'admin':
                return redirect()->guest(route('admin.login'));
                break;

            default:
                return redirect()->guest(route('login'));
                break;
        }
    }
}
