# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.21)
# Database: demo
# Generation Time: 2018-03-04 04:36:11 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table store
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store`;

CREATE TABLE `store` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `store_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '店铺类型：1-淘宝，2-京东',
  `store_url` varchar(500) NOT NULL DEFAULT '' COMMENT '店铺url',
  `store_name` varchar(200) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `store_account` varchar(100) NOT NULL DEFAULT '' COMMENT '店铺账号',
  `verify_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核类型：0-待审核，1-审核通过，2-审核失败',
  `store_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-失效，1-有效',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_verify_status` (`verify_status`),
  KEY `idx_store_status` (`store_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺';

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;

INSERT INTO `store` (`id`, `user_id`, `store_type`, `store_url`, `store_name`, `store_account`, `verify_status`, `store_status`, `created_at`, `updated_at`)
VALUES
	(1,10,2,'11','jd','22',1,1,1519653780,'2018-02-27 21:43:43');

/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table store_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store_goods`;

CREATE TABLE `store_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `goods_name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_url` varchar(500) NOT NULL DEFAULT '' COMMENT '商品URL',
  `goods_image` varchar(150) NOT NULL DEFAULT '' COMMENT '商品图片',
  `goods_price` int(11) NOT NULL DEFAULT '0' COMMENT '商品价格',
  `goods_keywords` varchar(500) NOT NULL DEFAULT '' COMMENT '商品关键字，|分割',
  `goods_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-失效，1-有效',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_goods_status` (`goods_status`),
  KEY `idx_goods_name` (`goods_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺商品';

LOCK TABLES `store_goods` WRITE;
/*!40000 ALTER TABLE `store_goods` DISABLE KEYS */;

INSERT INTO `store_goods` (`id`, `user_id`, `store_id`, `goods_name`, `goods_url`, `goods_image`, `goods_price`, `goods_keywords`, `goods_status`, `created_at`, `updated_at`)
VALUES
	(1,10,1,'demo','ddd','',1,'dd|aa',1,1519739026,'2018-02-28 20:40:32'),
	(2,10,2,'fff','ddd','',0,'dd|aa',1,1519739067,'2018-02-27 23:09:06'),
	(3,11,1,'fff','dddf','',0,'afsd',1,1519739224,'2018-02-27 22:34:33');

/*!40000 ALTER TABLE `store_goods` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table task
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task`;

CREATE TABLE `task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goods_name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品价格',
  `goods_image` varchar(150) NOT NULL DEFAULT '' COMMENT '商品图片',
  `goods_keyword` varchar(100) NOT NULL DEFAULT '' COMMENT '商品关键字',
  `total_order_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总订单数',
  `finished_order_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成订单数',
  `waiting_order_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '正在等待订单数',
  `doing_order_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '正在执行订单数',
  `platform` tinyint(1) NOT NULL DEFAULT '1' COMMENT '平台：1-pc，2-mobile',
  `task_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '任务状态：1-waiting，2-doing，3-done，4-close',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_task_status` (`task_status`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_goods_id` (`goods_id`),
  KEY `idx_goods_name` (`goods_name`),
  KEY `idx_platform` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务';

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;

INSERT INTO `task` (`id`, `user_id`, `store_id`, `goods_id`, `goods_name`, `goods_price`, `goods_image`, `goods_keyword`, `total_order_number`, `finished_order_number`, `waiting_order_number`, `doing_order_number`, `platform`, `task_status`, `created_at`, `updated_at`)
VALUES
	(1,10,1,1,'demo',1,'','cc',10,0,0,0,1,4,1519822522,'2018-02-28 21:26:00'),
	(2,10,1,1,'demo',1,'','dd',11,1,1,0,1,2,1519824397,'2018-02-28 22:20:43');

/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table task_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task_order`;

CREATE TABLE `task_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `task_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `task_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务用户ID',
  `order_id` varchar(100) NOT NULL DEFAULT '' COMMENT '订单ID',
  `order_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单状态：1-waiting，2-doing，3-done，4-close',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_order_status` (`order_status`),
  KEY `idx_task_user_id` (`task_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务订单';

LOCK TABLES `task_order` WRITE;
/*!40000 ALTER TABLE `task_order` DISABLE KEYS */;

INSERT INTO `task_order` (`id`, `user_id`, `task_id`, `task_user_id`, `order_id`, `order_status`, `created_at`, `updated_at`)
VALUES
	(1,10,2,10,'111',3,1519827187,'2018-02-28 22:20:43'),
	(2,10,2,10,'',1,1519827457,'2018-02-28 22:17:37');

/*!40000 ALTER TABLE `task_order` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `avatar` varchar(150) NOT NULL DEFAULT '' COMMENT '头像',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `gender` tinyint(1) NOT NULL DEFAULT '2' COMMENT '性别：0-女，1-男，2-未知',
  `qq` varchar(50) NOT NULL DEFAULT '' COMMENT 'qq',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT 'email',
  `password` char(12) NOT NULL DEFAULT '' COMMENT '密码',
  `invited_user_id` int(11) NOT NULL COMMENT '邀请人',
  `invite_code` varchar(50) NOT NULL DEFAULT '' COMMENT '邀请码',
  `role` tinyint(1) NOT NULL DEFAULT '0' COMMENT '角色：0-普通，1-买家，2-卖家',
  `level` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '等级',
  `open_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '任务开启状态：0-否，1-是',
  `taobao_account` varchar(100) NOT NULL DEFAULT '' COMMENT '淘宝账户',
  `jd_account` varchar(100) NOT NULL DEFAULT '' COMMENT '京东账户',
  `token` char(32) NOT NULL DEFAULT '' COMMENT 'token',
  `token_expires` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mobile` (`mobile`),
  UNIQUE KEY `idx_token` (`token`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `username`, `avatar`, `mobile`, `gender`, `qq`, `email`, `password`, `invited_user_id`, `invite_code`, `role`, `level`, `open_status`, `taobao_account`, `jd_account`, `token`, `token_expires`, `created_at`, `updated_at`)
VALUES
	(10,'jepson','','18258438129',1,'11','11','Wjp123456',0,'1QKXVDDaAb',2,1,1,'dd','dd','adcc5807b4e6e130bfc4d8bb545a6fc8',1520327672,1518423978,'2018-02-28 22:06:37');

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_fund
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_fund`;

CREATE TABLE `user_fund` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总金额',
  `locked` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '锁住金额',
  `total_earn` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总收入',
  `total_pay` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总支出',
  `total_withdraw` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总提现',
  `total_recharge` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总充值',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资金';

LOCK TABLES `user_fund` WRITE;
/*!40000 ALTER TABLE `user_fund` DISABLE KEYS */;

INSERT INTO `user_fund` (`user_id`, `amount`, `locked`, `total_earn`, `total_pay`, `total_withdraw`, `total_recharge`, `created_at`, `updated_at`)
VALUES
	(10,890,110,1,1,0,0,1519821575,'2018-03-03 11:14:57');

/*!40000 ALTER TABLE `user_fund` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_fund_account
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_fund_account`;

CREATE TABLE `user_fund_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card` varchar(50) NOT NULL DEFAULT '' COMMENT '身份证号',
  `bank_card` varchar(50) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `bank` varchar(50) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bankfiliale` varchar(50) NOT NULL DEFAULT '' COMMENT '支行名称',
  `account_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-否，1-是',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_bank_card` (`bank_card`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资金账号';

LOCK TABLES `user_fund_account` WRITE;
/*!40000 ALTER TABLE `user_fund_account` DISABLE KEYS */;

INSERT INTO `user_fund_account` (`id`, `user_id`, `real_name`, `id_card`, `bank_card`, `bank`, `bankfiliale`, `account_status`, `created_at`, `updated_at`)
VALUES
	(4,10,'吴健平','3602221991078362','234234343413134','中国银行','杭州九堡支行',0,1518760738,'2018-02-16 14:04:15');

/*!40000 ALTER TABLE `user_fund_account` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_fund_recharge
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_fund_recharge`;

CREATE TABLE `user_fund_recharge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `fund_record_id` int(11) NOT NULL DEFAULT '0' COMMENT '资金记录ID',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '金额',
  `source_account_type` tinyint(11) NOT NULL DEFAULT '1' COMMENT '来源账号类型：1-bank，2-alipay，3-wechat',
  `source_account_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源账号ID',
  `destination_account_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标账号ID',
  `destination_account_type` tinyint(11) NOT NULL DEFAULT '1' COMMENT '目标账号类型：1-bank，2-alipay，3-wechat',
  `recharge_time` int(11) NOT NULL DEFAULT '0' COMMENT '充值时间',
  `recharge_status` tinyint(11) NOT NULL DEFAULT '0' COMMENT '充值状态：0-waiting，1-passed，2-failed，3-close',
  `verify_time` int(11) NOT NULL DEFAULT '1' COMMENT '审核时间',
  `verify_remark` varchar(100) NOT NULL DEFAULT '' COMMENT '审核备注',
  `verify_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核人',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_recharge_status` (`recharge_status`),
  KEY `idx_fund_record_id` (`fund_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户充值';

LOCK TABLES `user_fund_recharge` WRITE;
/*!40000 ALTER TABLE `user_fund_recharge` DISABLE KEYS */;

INSERT INTO `user_fund_recharge` (`id`, `user_id`, `fund_record_id`, `amount`, `source_account_type`, `source_account_id`, `destination_account_id`, `destination_account_type`, `recharge_time`, `recharge_status`, `verify_time`, `verify_remark`, `verify_user_id`, `created_at`, `updated_at`)
VALUES
	(1,10,3,100,1,4,1,1,1520045510,3,1,'',0,1520045510,'2018-03-03 11:14:01');

/*!40000 ALTER TABLE `user_fund_recharge` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_fund_record
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_fund_record`;

CREATE TABLE `user_fund_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '总金额',
  `actual_amount` int(11) NOT NULL DEFAULT '0' COMMENT '真实金额',
  `commission` int(11) NOT NULL DEFAULT '0' COMMENT '佣金',
  `record_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型：1-withdraw，2-recharge，3-pay，4-earn',
  `record_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0-verifying，1-done，2-failed，3-close',
  `remarks` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_record_type` (`record_type`),
  KEY `idx_record_status` (`record_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资金记录';

LOCK TABLES `user_fund_record` WRITE;
/*!40000 ALTER TABLE `user_fund_record` DISABLE KEYS */;

INSERT INTO `user_fund_record` (`id`, `user_id`, `amount`, `actual_amount`, `commission`, `record_type`, `record_status`, `remarks`, `created_at`, `updated_at`)
VALUES
	(1,10,1,1,0,3,1,'',1519827643,'2018-02-28 22:20:43'),
	(2,10,1,1,0,4,1,'',1519827643,'2018-02-28 22:20:43'),
	(3,10,100,100,0,2,3,'提现',1520045510,'2018-03-03 11:14:01'),
	(4,10,100,100,0,1,0,'充值',1520046897,'2018-03-03 11:14:57');

/*!40000 ALTER TABLE `user_fund_record` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_fund_withdraw
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_fund_withdraw`;

CREATE TABLE `user_fund_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `fund_record_id` int(11) NOT NULL DEFAULT '0' COMMENT '资金记录ID',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '金额',
  `account_id` int(11) NOT NULL DEFAULT '0' COMMENT '账号ID',
  `account_type` tinyint(11) NOT NULL DEFAULT '1' COMMENT '账号类型：1-bank，2-alipay，3-wechat',
  `withdraw_status` tinyint(11) NOT NULL DEFAULT '0' COMMENT '提现状态：0-waiting，1-passed，2-failed，3-close',
  `withdraw_time` int(11) NOT NULL DEFAULT '0' COMMENT '提现时间',
  `verify_remark` varchar(100) NOT NULL DEFAULT '' COMMENT '审核备注',
  `verify_time` int(11) NOT NULL DEFAULT '0' COMMENT '审核时间',
  `verify_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '审核用户',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_withdraw_status` (`withdraw_status`),
  KEY `idx_fund_record_id` (`fund_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_fund_withdraw` WRITE;
/*!40000 ALTER TABLE `user_fund_withdraw` DISABLE KEYS */;

INSERT INTO `user_fund_withdraw` (`id`, `user_id`, `fund_record_id`, `amount`, `account_id`, `account_type`, `withdraw_status`, `withdraw_time`, `verify_remark`, `verify_time`, `verify_user_id`, `created_at`, `updated_at`)
VALUES
	(1,10,4,100,4,1,0,1520046897,'',0,0,1520046897,'2018-03-03 11:14:57');

/*!40000 ALTER TABLE `user_fund_withdraw` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
