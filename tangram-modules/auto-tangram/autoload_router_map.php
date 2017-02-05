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
            ],
            '/tangram/demo' => [
                'namespace' => 'Tangram\Demo\Controller',
                'class' => 'DemoController',
                'function' => 'getDemo'
            ]
        ],
        'POST' => [
            '/tangram/demo' => [
                'namespace' => 'Tangram\Demo\Controller',
                'class' => 'DemoController',
                'function' => 'postDemo'
            ]
        ],
        'PUT' => [
            '/tangram/demo' => [
                'namespace' => 'Tangram\Demo\Controller',
                'class' => 'DemoController',
                'function' => 'putDemo'
            ]
        ],
        'DELETE' => [
            '/tangram/demo' => [
                'namespace' => 'Tangram\Demo\Controller',
                'class' => 'DemoController',
                'function' => 'deleteDemo'
            ]
        ]
    ];
    public static function getMap()
    {
        return static::$map;
    }
}