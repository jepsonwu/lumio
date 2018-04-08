<?php

namespace Modules\Common\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Common\Services\CommonService;

class CommonController extends ApiBaseController
{
    protected $_commonService;

    public function __construct(CommonService $commonService)
    {
        $this->_commonService = $commonService;
    }

    /**
     *
     *
     * @api {GET} /api/common/v1/sms-captcha 发送短信验证码
     * @apiSampleRequest /api/common/v1/sms-captcha
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup common
     * @apiName sms-captcha
     **
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSmsCaptcha(Request $request)
    {
        $this->_commonService->sendAccountCaptcha(AuthHelper::user()->mobile);

        return $this->success([]);
    }
}