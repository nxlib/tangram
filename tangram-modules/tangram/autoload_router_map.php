<?php
class AutoRouterMap
{
    private static $map = [
        'GET' => [
            '/reg' => [
                'namespace' => 'NxLib\User\Controller',
                'class' => 'RegisterController',
                'function' => 'index'
            ],
            '/reg/index' => [
                'namespace' => 'NxLib\User\Controller',
                'class' => 'RegisterController',
                'function' => 'index'
            ]
        ],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    public static function getMap()
    {
        return static::$map;
    }
}