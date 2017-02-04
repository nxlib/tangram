<?php
class AutoRouterMap
{
    private static $map = [
        'GET' => [
            '/' => [
                'namespace' => 'NxLib\User\Controller',
                'class' => 'RegisterController',
                'function' => 'index'
            ],
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