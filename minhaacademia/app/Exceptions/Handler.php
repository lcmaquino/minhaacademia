<?php

namespace App\Exceptions;

use App\Setting;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $s = Setting::where(['key' => 'app_name'])->first();
        $appName = empty($s) ? '' : $s->value;

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ) {
            $request->merge(['pagetitle' => 'Erro 404 - ' . $appName]);
        }else{
            $request->merge(['pagetitle' => 'Ops! Aconteceu algum erro - ' . $appName]);
        }

        return parent::render($request, $exception);
    }
}
