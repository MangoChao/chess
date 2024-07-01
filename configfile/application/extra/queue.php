<?php
use think\Config;
return [
    'connector' => 'Redis',
    'expire' => null,
    'default' => 'general',
    'host' => Config::get("redis.host"),
    'port' => Config::get("redis.port"),
    'password' => Config::get("redis.password"),
    'select' => 0,
    'timeout' => 0,
    'persistent' => false,
];
