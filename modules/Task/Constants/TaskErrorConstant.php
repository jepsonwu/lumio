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
    const ERR_TASK_DISALLOW_APPLY = '23106|不允许申请';


    const ERR_TASK_ORDER_APPLY_FAILED = '23201|任务订单申请失败';
    const ERR_TASK_ORDER_DISALLOW_ASSIGN_USER = '23201|不允许给该用户分配任务';
    const ERR_TASK_ORDER_ASSIGN_FAILED = '23201|任务订单指定失败';
    const ERR_TASK_ORDER_INVALID = '23201|无效的任务订单';
    const ERR_TASK_ORDER_CONFIRM_FAILED = '23201|任务订单信息不正确';
    const ERR_TASK_ORDER_DISALLOW_DOING = '23201|不允许给该用户分配任务';
    const ERR_TASK_ORDER_DOING_FAILED = '23201|任务订单执行失败';
    const ERR_TASK_ORDER_DISALLOW_DONE = '23201|不允许给该用户分配任务';
    const ERR_TASK_ORDER_DONE_FAILED = '23201|任务订单完成失败';
    const ERR_TASK_ORDER_DISALLOW_CLOSE = '23201|不允许给该用户分配任务';
    const ERR_TASK_ORDER_CLOSE_FAILED = '23201|任务订单关闭失败';
    const ERR_TASK_ORDER_OPERATE_ILLEGAL = '23201|非法操作';
}