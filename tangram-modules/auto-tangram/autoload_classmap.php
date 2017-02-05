<?php
class AutoLoadClassMap{
    private static $map = [
        'NxLib\\User\\' => ['modules/nxlib/user'],
        'NxLib\\Permission\\' => ['modules/nxlib/permission'],
        'Tangram\\Demo\\' => ['modules/tangram/demo'],
    ];
    public static function getMap()
    {
        return static::$map;
    }
}
