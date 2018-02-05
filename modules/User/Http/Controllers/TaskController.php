<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\User\Services\UserTaskService;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/11/29
 * Time: 下午3:05
 */
class TaskController extends ApiBaseController
{
    protected $taskService;

    public function __construct(UserTaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function myList(Request $request)
    {
        $this->validate($request, [
            "type" => 'number'
        ]);

        //todo user info
        $userId = Auth::guard()->user()->id;

        $result = [
            'first_action' => $this->taskService->getFirstActionTaskInfoList($userId),
            'perfect_identity' => $this->taskService->getPerfectIdentityTaskInfoList($userId)
        ];
        $this->result(true, $result);
    }
}