## 表结构
### 用户 user
- id username gender qq email mobile password token invite_code invited_user_id
  role[normal|seller|buyer] level[1|2] open_status taobao_account jd_account created_at updated_at

### 用户账户 user_fund_account
- id user_id real_name id_card bank_card bank bankfiliale created_at updated_at

### 用户资金 user_fund
- user_id amount locked total_pay withdraw

### 用户交易记录 user_fund_record
- id user_id amount record_type[withdraw|recharge] record_status created_at updated_at

### 店铺 user_store
- id user_id store_type store_url store_name store_account verify_status[0|1|2] created_at updated_at

### 商品 goods
- id goods_url goods_image goods_price store_id goods_keywords[多个] created_at updated_at

### 任务 task
- id user_id goods_id goods_price goods_image goods_keyword total platform created_at updated_at

### 任务记录 user_task
- id task_id user_id task_status order_id money created_at updated_at

## API
### 账号
- 注册 [mobile|password|confirm_password|invite_code|captcha]
- 登录 [mobile|password]
- 登出
- 修改密码 [password|new_password|new_confirm_password]
- 重置密码 [mobile|password|confirm_password|captcha]
- 发送验证码 [mobile]

### 用户
- 修改个人信息 [用户信息]
- 普通用户列表

### 资金
- 充值 [amount|单号]
- 提现 [amount|captcha]
- 交易记录，列表、查询

### 商家
- 添加店铺 [store_type|store_url|store_name|store_account]
- 删除店铺 [store_id]
- 添加商品 [store_id|goods_url|goods_keywords]
- 删除商品 [goods_id]
- 修改商品 [store_id|goods_url|goods_keywords]

### 任务
- 发布任务 [goods_id goods_keyword total platform]
- 删除任务 ？？ 能修改么 、怎么删除
- 任务列表
- 申请任务 [task_id]
- 指定任务 [task_id|user_id]
- 审核任务 [task_id]
- 确认任务 [task_id|store_account]

## 前端页面
### 账号
- 注册页，普通用户和商家共用一个页面，属性参考API字段
- 登录页
- 忘记密码，重置密码

### 主页面
- 首页，包含：logo、轮播、公告、基础统计信息、推荐商品列表、底部信息

### 个人中心
个人中心页面，包含普通人和商家所有信息，包含栏目：

- 个人资料
> - 基本信息修改
> - 修改密码

- 我的账户
> - 资金明细：账户总额、可用余额、冻结资金、累计支出、提现中、交易记录
    交易记录包含：提现、充值、支出、收入
> - 账户信息：用户账户信息

- 任务中心
> - 我的任务
> - 我发布任务

- 我的店铺
> - 我的店铺
> - 我的商品

## 管理后台

### 用户管理
- 列表
- 审核商家

### 资金管理
- 审核提现
- 审核充值
- 交易记录

### 商家管理
- 审核店铺

## 关键点
- 如何成为普通用户？ 绑定账号、绑定银行卡
- 如何成为商家？ 绑定银行卡、充值、绑定店铺
- 如何发布任务？绑定店铺、添加商品

## 其它事项
- 部署环境
- 短信服务购买
- 七牛服务购买（个人账号即可）
