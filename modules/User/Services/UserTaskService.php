<?php

namespace Modules\User\Services;

use Modules\User\Components\Task\TaskCollection\Task;
use Modules\User\Components\Task\TaskFactory;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\User\Constants\UserBanyanDBConstant;
use Modules\User\Constants\UserBusinessConstants;
use Modules\User\Repositories\UserTaskRepository;

class UserTaskService extends BaseService
{
    /**
     * @var UserTaskRepository
     */
    protected $repository;

    public function __construct(UserTaskRepository $repository)
    {
        $this->repository = $repository;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function finishBindWeixinTask($userId)
    {
        return $this->repository->finishTask(UserBusinessConstants::COMMON_USER_TASK_TYPE_BIND_WEIXIN, $userId);
    }

    public function finishEditAvatar($userId)
    {
        return TaskFactory::editAvatar($userId)->finish();
    }

    public function finishEditName($userId)
    {
        return TaskFactory::editName($userId)->finish();
    }

    public function finishEditBirthday($userId)
    {
        return TaskFactory::editBirthday($userId)->finish();
    }

    public function finishEditSchool($userId)
    {
        return TaskFactory::editSchool($userId)->finish();
    }

    public function finishEditPersonalTag($userId)
    {
        return TaskFactory::editPersonalTag($userId)->finish(); 
    }

    public function finishEditNumber($userId)
    {
        return TaskFactory::editNumber($userId)->finish();
    }

    public function finishEditAddress($userId)
    {
        return TaskFactory::editAddress($userId)->finish();
    }

    public function finishBindWeibo($userId)
    {
        return TaskFactory::bindWeibo($userId)->finish();
    }

    public function finishBindQq($userId)
    {
        return TaskFactory::bindQq($userId)->finish();
    }

    public function finishBindWeixin($userId)
    {
        return TaskFactory::bindWeixin($userId)->finish();
    }

    public function finishFirstPublishPhoto($userId)
    {
        return TaskFactory::firstPublishPhoto($userId)->finish();
    }

    public function finishFirstSign($userId)
    {
        return TaskFactory::firstSign($userId)->finish();
    }

    public function finishFirstWatch($userId)
    {
        return TaskFactory::firstWatch($userId)->finish();
    }

    public function finishFirstZan($userId)
    {
        return TaskFactory::firstZan($userId)->finish();
    }

    public function finishAuth($userId)
    {
        return TaskFactory::auth($userId)->finish();
    }

    public function finishUploadContact($userId)
    {
        return TaskFactory::uploadContact($userId)->finish();
    }

    public function getFirstActionTaskInfoList($userId)
    {
        return $this->formatTaskInfo($this->getFirstActionTaskList($userId), $userId);
    }

    public function getPerfectIdentityTaskInfoList($userId)
    {
        return $this->formatTaskInfo($this->getPerfectIdentityTaskList($userId), $userId);
    }

    public function finishNewUserGuide($userId)
    {
        UserBanyanDBConstant::newUserPool()->set(
            $userId,
            json_encode([
                'userId' => $userId,
                'version' => $this->_requestParamsComponent->version,
                'time' => time(),
                'bubble' => false
            ])
        );
        if (!UserBanyanDBConstant::newUserGuide()->exists($userId)) {
            UserBanyanDBConstant::newUserGuide()->set(
                $userId,
                json_encode([
                    'register' => time(),
                    'pushImgTimes' => 0,
                    'day' => [
                        'day3' => 0,
                        'day5' => 0,
                        'day7' => 0,
                    ],
                    //是否促发电话号码
                    'mobileNumber' => 0,
                    //气泡
                    'bubble' => [
                        'N1' => 0,
                        'N2' => 0,
                        'N3' => 0,
                        'N4' => 0,
                        'N5' => 0,
                        'N6' => 0,
                        'N7' => 0
                    ]
                ])
            );
        }
    }

    protected function formatTaskInfo($taskList, $userId)
    {
        $result = [];

        array_walk($taskList, function (Task $task, $key) use (&$result, $userId) {
            $taskInfo = $task->getInfo();
            $newKey = $task->isFinished() ? ($key + time()) : $key;
            $taskInfo && $result[$newKey] = $taskInfo;
        });

        ksort($result);
        return array_values($result);
    }

    public function getTotalCoinForUnfinished($userId)
    {
        $total = 0;

        $taskList = array_merge($this->getFirstActionTaskList($userId), $this->getPerfectIdentityTaskList($userId));
        array_walk($taskList, function (Task $task) use (&$total, $userId) {
            $task->isFinished() || $total += $task->getCoinNumber();
        });

        return $total;
    }

    public function isAllFinished($userId)
    {
        $finished = true;

        $taskList = array_merge($this->getFirstActionTaskList($userId), $this->getPerfectIdentityTaskList($userId));
        array_walk($taskList, function (Task $task) use (&$finished, $userId) {
            if (!$task->isFinished()) {
                $finished = false;
                return;
            }
        });

        return $finished;
    }

    protected function getFirstActionTaskList($userId)
    {
        return [
            TaskFactory::firstSign($userId),
            TaskFactory::firstWatch($userId),
            TaskFactory::firstZan($userId),
            TaskFactory::firstPublishPhoto($userId),
        ];
    }

    protected function getPerfectIdentityTaskList($userId)
    {
        return [
            TaskFactory::editAvatar($userId),
            TaskFactory::editName($userId),
            TaskFactory::editBirthday($userId),
            TaskFactory::editSchool($userId),
            TaskFactory::bindWeibo($userId),
            TaskFactory::bindWeixin($userId),
            TaskFactory::editAddress($userId),
        ];
    }

    /**
     * @param $userId
     * @return InterfaceModel
     */
    public function getLogStorage($userId)
    {
        return NBanyanFactory::userTaskLog($userId);
    }
}
