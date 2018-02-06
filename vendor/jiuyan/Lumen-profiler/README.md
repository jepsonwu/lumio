# Install
`composer require Jiuyan/Lumen-profiler`

# Add Service Provider

first you should register provider in app.php file use regiser method

`$app->register(Jiuyan\Profiler\LumenProfilerServiceProvider::class)`

if you want to add Facade way, use withFacades method

```
$userAliases = [
    'Jiuyan\Profiler\ProfilerFacade' => 'Facade',
];

$app->withFacades(true, $userAliases);

```

# start to profile

`Facade::start()`


# save to profiler data

`Facade:save()`
