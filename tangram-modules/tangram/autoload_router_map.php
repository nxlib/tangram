<?php
class AutoRouterMap
{
    private static $map = [
        'uri/uri1' => [
            'namespace' => 'abc',
            'class' => 'menu',
            'function' => 'aaa'
        ]
    ];
    public static function getMap()
    {
        return static::$map;
    }
}