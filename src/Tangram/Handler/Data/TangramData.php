<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:21
 */

namespace Tangram\Handler\Data;


class TangramData
{
    const TANGRAM_FILE = TG_RUN_PATH . DIRECTORY_SEPARATOR . "tangram.json";

    private static $data;

    private static $modulePath;
    private static $trueModulePath;

    public static function projectInfo()
    {
        self::init();
        return self::$data;
    }

    public static function getTrueModulePath()
    {
        self::init();
        return self::$trueModulePath;
    }

    public static function getModulePath()
    {
        self::init();
        return self::$modulePath;
    }

    private static function init()
    {
        if (!empty(self::$data)) {
            return;
        }
        if (!file_exists(self::TANGRAM_FILE)) {
            exit("Error: {TANGRAM_FILE} not found");
        }
        $tangramData = json_decode(file_get_contents(self::TANGRAM_FILE), 1);
        $modulePath = 'modules';


        if (isset($tangramData['modules-path']) && !empty($tangramData['modules-path'])) {
            $modulePath = $tangramData['modules-path'];
        }
        $trueModulePath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $modulePath;

        if (!file_exists($trueModulePath)) {
            exit("Error: module path not found");
        }
        static::$data = $tangramData;
        static::$modulePath = $modulePath;
        static::$trueModulePath = $trueModulePath;
    }
}