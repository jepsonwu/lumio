<?php

namespace Nwidart\Modules\Routing;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\Response\ApiResponse;
use Jiuyan\Common\Component\Response\BusinessException;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function response($data, $msg = '', $code = 0, $succ = true){
        return new ApiResponse($data, $msg, $code, $succ);
    }
    public function error($msg, $code){
        throw new BusinessException($msg, $code);
    }
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        return $validator->attributes();
    }
}
