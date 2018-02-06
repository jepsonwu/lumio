#使用说明：

$app->routeMiddleware(
   [
       'jiuyan.api.auth' => \Jiuyan\SSO\Api\ApiAuthMiddleware::class,
       'jiuyan.sso.auth' => \Jiuyan\SSO\SSO\AdminMiddleware::class,
   ]
);


$app->register(\Jiuyan\SSO\AuthServiceProvider::class);
