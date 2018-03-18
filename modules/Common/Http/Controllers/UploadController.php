<?php

namespace Modules\Common\Http\Controllers;

use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Common\Services\UploadService;

class UploadController extends ApiBaseController
{
    protected $_uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->_uploadService = $uploadService;
    }

    /**
     *
     * @api {GET} /api/common/v1/upload-token 获取上传token
     * @apiSampleRequest /api/common/v1/upload-token
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup common-upload
     * @apiName token
     *
     * @apiParam {int} [extension] 文件后缀 默认jpg
     * @apiParam {int} [encode] 是否encode 0-否，1-是
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"token":"I5m6XV-FOs1aUPQnu7v4eF_yLgJjbgoZrrWT3Ghk:F8wBpeHGmnV-vZF1YkQLy9ybDdU=:eyJzY29wZSI6InZvb2hhIiwiZGVhZGxpbmUiOjE1MjAyNTczOTd9","filename":"upload\/2018\/03\/05\/3fbd6328793fa28c181b230101d55cad.jpg","key":"upload\/2018\/03\/05\/3fbd6328793fa28c181b230101d55cad.jpg","expires":"1520340197","host":"http:\/\/u1.jiuyan.info"},"code":"0","msg":"","time":"1520253797"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function token(Request $request)
    {
        $this->validate($request, [
            "extension" => ["string"],
            "encode" => ["in:0,1"]
        ]);

        $params = $this->requestParams->getRegularParams();
        $encode = (bool)array_get($params, "encode", 0);
        $extension = strtolower(array_get($params, "extension", 'jpg'));

        return $this->success($this->_uploadService->getUploadInfo($encode, $extension));
    }
}