<?php

return [
    'app_init'     => [],
    'app_begin'    => [],
    'app_dispatch' => [
        'app\\common\\behavior\\Common',
    ],
    'module_init'  => [
        'app\\common\\behavior\\Common',
    ],
    'addon_begin'  => [
        'app\\common\\behavior\\Common',
    ],
    'action_begin' => [],
    'view_filter'  => [],
    'log_write'    => [],
    'app_end'      => [],
];
