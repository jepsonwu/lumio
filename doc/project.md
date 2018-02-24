## 表结构
### 用户 user
- id username gender qq email mobile password token invite_code invited_user_id
  role[normal|seller|buyer] level[1|2] open_status taobao_account jd_account created_at updated_at

### 用户账户 user_fund_account
- id user_id real_name id_card bank_card bank bankfiliale account_status created_at updated_at

### 用户资金 user_fund
- user_id amount locked total_earn total_pay total_withdraw total_recharge created_at updated_at

### 用户资金记录 user_fund_record
- id user_id amount actual_amount commission record_type[withdraw|recharge|pay|earn] 
  record_status[verifying|done|failed] remarks created_at updated_at

### 店铺 store
- id user_id store_type[1-taobao,2-jd] store_url store_name store_account verify_status[0|1|2]    store_status created_at updated_at

### 商品 store_goods
- id user_id store_id goods_name goods_url goods_image goods_price goods_keywords[多个]   
  created_at goods_status updated_at

### 任务 task
- id user_id store_id goods_id goods_name goods_price goods_image goods_keyword 
  total_order_number finished_order_number doing_order_number platform[1-pc,2-mobile] task_status[1-waiting，2-doing，3-done，4-close] created_at updated_at

### 任务订单 task_order
- id user_id task_id task_user_id order_id price order_status[1-waiting,2-doing,3-done,4-close] created_at   updated_at

### 统计 user_stat
- user_id 

## API
### 账号
- 注册 [mobile|password|confirm_password|invite_code|captcha]
- 登录 [mobile|password]
- 登出
- 修改密码 [password|new_password|new_confirm_password]
- 重置密码 [mobile|password|confirm_password|captcha]
- 发送验证码 [mobile]

### 用户
- 修改个人信息 [username|gender|qq|email|open_status|taobao_account|jd_account]
- 普通用户列表

### 资金
- 我的账户
- 添加账户 [real_name|id_card|bank_card|bank|bankfiliale]
- 修改账户 [real_name|id_card|bank_card|bank|bankfiliale]
- 删除账户
- 充值 [amount|单号]
- 修改充值记录
- 删除充值记录
- 提现 [amount|captcha]
- 删除提现记录
- 交易记录，列表、查询

### 商家
- 添加店铺 [store_type|store_url|store_name|store_account]
- 删除店铺 [store_id]
- 修改店铺 [store_id|store_url|store_name|store_account]
- 添加商品 [store_id|goods_url|goods_keywords]
- 删除商品 [goods_id]
- 修改商品 [store_id|goods_url|goods_keywords]

### 任务
- 是否允许发布任务
- 发布任务 [goods_id|goods_keyword|total_order_number|platform]
- 关闭任务 [task_id]
- 修改任务 [task_id|total_order_number]
- 任务列表

### 任务订单
- 是否允许申请任务
- 申请任务 [task_id]
- 指定任务 [task_id|user_id]
- 确认任务信息 [task_id|store_account]
- 执行任务 [task_id|order_id]
- 完成执行任务 [task_id]
- 取消执行任务 [task_id]

## 前端页面  
字段属性参考API
### 账号
- 注册页
- 登录页
- 忘记密码

### 主页面
- 首页 [logo、轮播、公告、基础统计信息、推荐商品列表、底部信息]

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
> - 我的任务 [列表|做任务]
> - 我发布任务 [列表|发布任务|指定任务]

- 我的店铺
> - 我的店铺 [列表|添加店铺]
> - 我的商品 [列表|添加商品]

## 管理后台

### 用户管理
- 用户列表
- 审核商家

### 资金管理
- 审核提现
- 审核充值
- 交易记录

### 商家管理
- 审核店铺

## 关键点
- 如何成为普通用户？ 绑定账号、绑定银行卡
- 如何成为商家？ 绑定银行卡、充值
- 如何发布任务？绑定店铺、添加商品

## 其它事项
- 部署环境
- 短信服务购买
- 七牛服务购买（个人账号即可）
