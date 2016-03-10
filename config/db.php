<?php
return [
    'driver'    => 'mysql',
    'database'  => 'cellular',
    'prefix'    => 'cellular_',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'strict'    => false,
    //主库信息
    'master'    => [
        'host'      => 'localhost',
        'port'      => '3306',
        'username'  => 'root',
        'password'  => 'root',
    ],
    //从库信息
    'slave'     => [
        's1' => [
            'host'      => 'localhost',
            'port'      => '3306',
            'username'  => 'root',
            'password'  => 'root',
        ],
        's2' => [
            'host'      => 'localhost',
            'port'      => '3306',
            'username'  => 'root',
            'password'  => 'root',
        ]
    ]
]
?>
