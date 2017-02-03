<?php
class AutoLoadClassMap{
    private static $map = [
        'NxLib\\User\\' => ['modules/nxlib/user']
    ];
    public static function getMap()
    {
        return static::$map;
    }
}
