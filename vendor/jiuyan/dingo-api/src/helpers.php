<?php


if (!function_exists('version')) {
    /**
     * Set the version to generate API URLs to.
     *
     * @param string $version
     *
     * @return \Dingo\Api\Routing\UrlGenerator
     */
    function version($version)
    {
        return app('api.url')->version($version);
    }
}

if (!function_exists('resource')) {
    function resource(\Laravel\Lumen\Application $application, $uri, $action)
    {
        $application->get($uri, $action . '@index');
        $application->get($uri . '/create', $action . '@create');
        $application->get($uri . '/{id}', $action . '@show');
        $application->get($uri . '/edit/{id}', $action . '@edit');
        $application->post($uri, $action . '@store');
        $application->delete($uri . '/{id}', $action . '@destroy');
    }
}
