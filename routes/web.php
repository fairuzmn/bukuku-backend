<?php

use App\Utils\ResponseUtils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return ResponseUtils::baseResponse(200, 'it works');
});

Route::get('/info', function () {
    return phpinfo();
});
