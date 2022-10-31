<?php

use think\Env;

return [
    'type'            => Env::get('database.type', 'mysql'),
    'hostname'        => Env::get('database.hostname', '127.0.0.1'),
    'database'        => Env::get('database.database', 'calendar'),
    'username'        => Env::get('database.username', 'root'),
    'password'        => Env::get('database.password', ''),
    'hostport'        => Env::get('database.hostport', '3306'),
    'dsn'             => '',
    'params'          => [],
    'charset'         => Env::get('database.charset', 'utf8mb4'),
    'prefix'          => Env::get('database.prefix', ''),
    'debug'           => Env::get('database.debug', false),
    'deploy'          => 0,
    'rw_separate'     => false,
    'master_num'      => 1,
    'slave_no'        => '',
    'fields_strict'   => true,
    'resultset_type'  => 'array',
    'auto_timestamp'  => false,
    'datetime_format' => false,
    'sql_explain'     => false,
];
