<?php

Route::version('v1', [
    'prefix' => '/api/common',
    'namespace' => 'Modules\Common\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/upload-token', 'UploadController@token');
});

