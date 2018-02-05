<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Jiuyan\Common\Component\InFramework\Exceptions\BusinessException;
use Jiuyan\Tools\Business\JsonTool;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        // HttpException::class,
        // ModelNotFoundException::class,
        // ValidationException::class,
        BusinessException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (app()->environment() == 'local') {
            return parent::render($request, $e);
        }
        $responseData = [];
        if ($e instanceof ValidationException && $e->getResponse()) {
            $responseData = $e->getResponse()->getData(true);
        }
        return response()->make(
            JsonTool::encode([
                'succ' => false,
                'data' => $responseData,
                'code' => $e->getCode(),
                'msg' => $e->getMessage() ?? '',
                'time' => time()
            ]),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
