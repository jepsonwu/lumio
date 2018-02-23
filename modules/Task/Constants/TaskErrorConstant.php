<?php

namespace Modules\Task\Constants;


class TaskErrorConstant
{
    const ERR_TASK_CREATE_FAILED = '23101|任务添加失败';
    const ERR_TASK_INVALID = '23102|无效的任务';
    const ERR_TASK_UPDATE_FAILED = '23103|任务修改失败';
    const ERR_TASK_DISALLOW_UPDATE = '23103|修改失败，有未完成的任务';
    const ERR_TASK_CLOSE_FAILED = '23104|任务关闭失败';
    const ERR_TASK_DISALLOW_CLOSE = '23104|关闭失败，有未完成的任务';
    const ERR_TASK_OPERATE_ILLEGAL = '23105|非法操作';
}