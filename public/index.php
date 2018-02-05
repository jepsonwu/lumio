<?php
$app = require __DIR__.'/../bootstrap/app.php';

Profiler::start();
$app->run();
Profiler::save();
