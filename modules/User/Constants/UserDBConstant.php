<?php

namespace Modules\User\Constants;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/22
 * Time: 上午10:45
 */
class UserDBConstant
{
    /***************************************user gold log***********************************************/
    const USER_GOLD_OPERATION_UPGRADE = 1;// 用户升级
    const USER_GOLD_OPERATION_BUY_PASTER = 2;// 用户购买贴纸使用次数
    const USER_GOLD_OPERATION_SIGNIN = 3;// 用户签到
    const USER_GOLD_OPERATION_GETREWARD = 4; // 用户被打赏
    const USER_GOLD_OPERATION_EXCHANGE = 5;// in币兑换
    const USER_GOLD_OPERATION_LOTTERY = 6; // 抽奖
    const USER_GOLD_OPERATION_EXCHANGE_DUIBA = 7;// 兑吧in币兑换
    const USER_GOLD_OPERATION_EDIT_NUMBER = 10;//修改IN号
    const USER_GOLD_OPERATION_INVITE = 13;// 邀请好友
    const USER_GOLD_OPERATION_UPLOAD_CONTACT = 14;// 上传通讯录
    const USER_GOLD_OPERATION_AUTH = 15;// 手机认证
    const USER_GOLD_OPERATION_BIND_WEIXIN = 16;  // 绑定微信
    const USER_GOLD_OPERATION_BIND_WEIBO = 17;// 绑定微博
    const USER_GOLD_OPERATION_BIND_QQ = 18;// 绑定QQ
    const USER_GOLD_OPERATION_BIND_FACEBOOK = 19; // 绑定非死不可
    const USER_GOLD_OPERATION_PROMO_WATCH = 20; //清明节活动关注通讯录好友
    const USER_GOLD_OPERATION_ADMIN = 21;//后台手动加in币
    const USER_GOLD_OPERATION_NUMBER_EDIT_AWARD = 22; //in币重复 修改in号奖励
    const USER_GOLD_OPERATION_PROMO_INCGOLD = 23; // 活动加金币
    const USER_GOLD_OPERATION_FINISH_DAILY_TASK = 24;// 完成每日任务
    const USER_GOLD_OPERATION_FINISH_ROOKIE_TASK = 25;// 完成新手任务
    const USER_GOLD_OPERATION_THIRD_PARTY_REGISTER = 26; // 第三方注册
    const USER_GOLD_OPERATION_SYS_PUNISH = 27;  // 系统惩罚
    const USER_GOLD_OPERATION_INVITE_CONTACT_FRIEND = 28; // 邀请通讯录好友
    const USER_GOLD_OPERATION_EDIT_SCHOOL = 29;    //编辑学校
    const USER_GOLD_OPERATION_EDIT_BIRTHDAY = 30;   //编辑生日
    const USER_GOLD_OPERATION_EDIT_PERSONAL_TAG = 31;    //个性标签
    const USER_GOLD_OPERATION_CHALLENGE_TASK = 32;
    const USER_GOLD_OPERATION_TAG_PHOTO_VOTE = 33; // 标签图片投票
    const USER_GOLD_OPERATION_DUIBA_GET_COIN = 34; // 兑吧虚拟兑换获得in币
    const USER_GOLD_OPERATION_VISITOR_BUY_HIDE = 35; //访客 - 购买隐身次数
    const USER_GOLD_OPERATION_FIRST_PUBLISH = 36;   //用户首次发图
    const USER_GOLD_OPERATION_DOUDOU_WELFARE = 37;  // 兜兜特卖-福利in币
    const USER_GOLD_OPERATION_FU_LI_SHE = 38;  // 福利社in活动
    const USER_GOLD_OPERATION_SKY_FLY = 39;    //in3.0 上空界面：我要上天
    const USER_GOLD_OPERATION_SKY_FLY_ADD = 40;    //in3.0 上空界面：加油
    const USER_GOLD_OPERATION_SKY_ZAN = 41;     //上空点赞
    const USER_GOLD_OPERATION_SKY_COMMAND = 42;    //上空评论
    const USER_GOLD_OPERATION_TIZHU_REWARD = 43;     //题主管理每日奖励
    const USER_GOLD_OPERATION_TIZHU_REWARD_RANK = 44;     //题主管理每日排行奖励
    const USER_GOLD_OPERATION_TIZHU_REWARD_NEW_FIRST = 45;  //题主管理新手任务一的奖励
    const USER_GOLD_OPERATION_TIZHU_REWARD_NEW_THREE = 46;  //题主管理新手任务二的奖励
    const USER_GOLD_OPERATION_IOS_GIVE_ALL_PERMIT = 47; //iOS开放所有权限
    const USER_GOLD_OPERATION_EDIT_AVATAR = 48;    //编辑头像
    const USER_GOLD_OPERATION_EDIT_NAME = 49;    //编辑昵称
    const USER_GOLD_OPERATION_EDIT_ADDRESS = 50;    //编辑地区
    const USER_GOLD_OPERATION_FIRST_SIGN = 51;   //用户首次打卡 todo 上面重复了
    const USER_GOLD_OPERATION_FIRST_WATCH = 52;   //用户首次关注
    const USER_GOLD_OPERATION_FIRST_ZAN = 53;   //用户首次点赞

    const USER_GOLD_OPERATION_RECOMMEND = 100;// 编辑精选

    const USER_GOLD_OPERATION_PROMO_MIDAUTUMN = 200; // 中秋活动消耗in币抽第三个福袋
    const USER_GOLD_OPERATION_PROMO_MIDAUTUMN_PRIZE = 201;
    const USER_GOLD_OPERATION_PROMO_MIDAUTUMN_RECOMPENSE = 202; // 活动贴纸头像未中用户补偿
    const USER_GOLD_OPERATION_PROMO_HEADLINE = 203; //上头条活动的消耗
    const USER_GOLD_OPERATION_PROMO_XMAS_GIFT = 204; // 圣诞送礼花in币
    const USER_GOLD_OPERATION_PROMO_SPRING_2016 = 205; //2016年春节福袋抽奖送IN币
    const USER_GOLD_OPERATION_PROMO_INDREAM = 206; //indream付费通道
    const USER_GOLD_OPERATION_PROMO_INDREAM_FAILED = 207; //indream付费通道失败补偿
    const USER_GOLD_OPERATION_PROMO_INDREAM_MATERIAL = 208; //indream付费购买自定义滤片次数
    const USER_GOLD_OPERATION_PROMO_TAG_TIZHU = 209; //indream付费购买自定义滤片次数
    const USER_GOLD_OPERATION_PROMO_DOUDOU_COUPON = 210;//兜兜特卖的活动，消耗in币，兑换优惠券
    const USER_GOLD_OPERATION_USER_VERIFY_DAREN_LVLUP_BONUS = 211; // 新版用户认证升级奖励100金币

    const USER_GOLD_OPERATION_SYS_CORRECT = 400;// 隐藏的修复数据用的
}