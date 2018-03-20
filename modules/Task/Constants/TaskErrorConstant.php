<?php

namespace Modules\Task\Constants;


class TaskErrorConstant
{
    const ERR_TASK_CREATE_FAILED = '23101|任务添加失败';
    const ERR_TASK_INVALID = '23102|无效的任务';
    const ERR_TASK_UPDATE_FAILED = '23103|任务修改失败';
    const ERR_TASK_DISALLOW_UPDATE = '23104|修改失败，有未完成的任务';
    const ERR_TASK_CLOSE_FAILED = '23105|任务关闭失败';
    const ERR_TASK_DISALLOW_CLOSE = '23106|关闭失败，有未完成的任务';
    const ERR_TASK_OPERATE_ILLEGAL = '23107|非法操作';
    const ERR_TASK_DISALLOW_APPLY = '23108|不允许申请';


    const ERR_TASK_ORDER_APPLY_FAILED = '23201|任务订单申请失败';
    const ERR_TASK_ORDER_DISALLOW_ASSIGN_USER = '23202|不允许给该用户分配任务';
    const ERR_TASK_ORDER_ASSIGN_FAILED = '23203|任务订单指定失败';
    const ERR_TASK_ORDER_INVALID = '23204|无效的任务订单';
    const ERR_TASK_ORDER_CONFIRM_FAILED = '23205|任务订单信息不正确';
    const ERR_TASK_ORDER_DISALLOW_DOING = '23206|不允许做任务';
    const ERR_TASK_ORDER_DOING_FAILED = '23207|任务订单执行失败';
    const ERR_TASK_ORDER_DISALLOW_DONE = '23208|不允许完成任务';
    const ERR_TASK_ORDER_DONE_FAILED = '23209|任务订单完成失败';
    const ERR_TASK_ORDER_DISALLOW_CLOSE = '23210|不允许删除任务';
    const ERR_TASK_ORDER_CLOSE_FAILED = '23211|任务订单关闭失败';
    const ERR_TASK_ORDER_OPERATE_ILLEGAL = '23212|非法操作';
    const ERR_TASK_ORDER_MORE_APPLY = '23213|十天内不能申请同一家店铺的任务';
    const ERR_TASK_ORDER_VERIFY_FAILED = '23214|任务订单审核失败';
    const ERR_TASK_ORDER_DISALLOW_VERIFY = '23215|不允许审核任务';
    const ERR_TASK_ORDER_SELLER_CONFIRM_FAILED = '23216|任务订单卖家确认失败';
    const ERR_TASK_ORDER_DISALLOW_SELLER_CONFIRM = '23217|不允许确认任务';
    const ERR_TASK_ORDER_BUYER_CONFIRM_FAILED = '23218|任务订单买家确认失败';
    const ERR_TASK_ORDER_DISALLOW_BUYER_CONFIRM = '23219|不允许确认任务';
    const ERR_TASK_ORDER_FREEZE_FAILED = '23220|任务订单冻结失败';
    const ERR_TASK_ORDER_DISALLOW_FREEZE = '23221|不允许冻结任务';
    const ERR_TASK_ORDER_UNFREEZE_FAILED = '23222|任务订单解冻失败';
    const ERR_TASK_ORDER_DISALLOW_UNFREEZE = '23223|不允许解冻任务';
}