<?php

define('APP_PATH', __DIR__ . '/../application/');

require __DIR__ . '/../thinkphp/base.php';

\think\Route::bind('\app\admin\command\Install', 'controller');

\think\App::route(true);


\think\Url::root('');

\think\App::run()->send();
