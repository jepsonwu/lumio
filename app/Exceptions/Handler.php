<?php

namespace App\Exceptions;

use App\Constants\GlobalErrorConstant;
use Exception;
use Illuminate\Validation\ValidationException;
use Jiuyan\Common\Component\InFramework\Exceptions\ApiExceptions;
use Jiuyan\Common\Component\InFramework\Exceptions\BusinessException;
use Jiuyan\Tools\Business\JsonTool;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

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

    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        //todo package
//        if (app()->environment() == 'local') {
//            return parent::render($request, $e);
//        }

        $errorInfo = explode('|', GlobalErrorConstant::ERR_SYSTEM);
        $message = $errorInfo[1];
        $code = $errorInfo[0];
        if ($e instanceof ValidationException && $e->getResponse()) {
            $validation = $e->getResponse()->getData(true);
            $code = GlobalErrorConstant::ERR_VALIDATION;
            $message = ($validation ? key($validation) : "field") . " invalid";
        } else if ($e instanceof BusinessException || $e instanceof ApiExceptions) {
            $message = $e->getMessage();
            $code = $e->getCode();
        } else {
            \Log::error($e->getMessage());
        }

        return response()->make(
            JsonTool::encode([
                'succ' => false,
                'data' => [],
                'code' => $code,
                'msg' => $message,
                'time' => time()
            ]),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
