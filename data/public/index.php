<?php

define('APP_PATH', __DIR__ . '/../application/');


if (!is_file(APP_PATH . 'admin/command/Install/install.lock')) {
    //header("location:./install.php");
    //exit;
}

require __DIR__ . '/../thinkphp/start.php';
