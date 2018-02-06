### 基础说明

- 功能概况：
    - 短信
    - 语音
    - 邮件
    
- 介绍：
    - 邮件：目前采用系统内置的邮件发送模块，因此启用邮件验证码之前，要进行一些响应的配置。
    > 1、相关的配置引入，相关的服务注册进来
    > 
    >   $app->configure('mail');
    >
    >   $app->register(Illuminate\Mail\MailServiceProvider::class);
    >
    > 2、不同的业务要对应不同的邮件模板，目前邮件模板只能定义到系统目录下：resources/views/emails
    