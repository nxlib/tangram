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

    private static $restfulPath;
    private static $trueRestfulPath;

    private static $webPagePath;
    private static $trueWebPagePath;

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
        $restfulPath = 'restful';
        $webPagePath = 'web-page';


        if (isset($tangramData['modules-path']) && !empty($tangramData['modules-path'])) {
            $modulePath = $tangramData['modules-path'];
        }
        if (isset($tangramData['restful-path']) && !empty($tangramData['restful-path'])) {
            $restfulPath = $tangramData['restful-path'];
        }
        if (isset($tangramData['web-page-path']) && !empty($tangramData['web-page-path'])) {
            $webPagePath = $tangramData['web-page-path'];
        }
        $trueModulePath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $modulePath;
        $trueRestfulPath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $restfulPath;
        $trueWebPagePath = TG_RUN_PATH . DIRECTORY_SEPARATOR . $webPagePath;

        if (!file_exists($trueModulePath)) {
            exit("Error: module path not found");
        }
        static::$data = $tangramData;
        static::$modulePath = $modulePath;
        static::$trueModulePath = $trueModulePath;
        if (file_exists($trueRestfulPath)) {
            static::$restfulPath = $restfulPath;
            static::$trueRestfulPath = $trueRestfulPath;
        }
        if (file_exists($trueWebPagePath)) {
            static::$webPagePath = $webPagePath;
            static::$trueWebPagePath = $trueWebPagePath;
        }
    }

    /**
     * @return mixed
     */
    public static function getRestfulPath()
    {
        self::init();
        return self::$restfulPath;
    }

    /**
     * @return mixed
     */
    public static function getTrueRestfulPath()
    {
        self::init();
        return self::$trueRestfulPath;
    }

    /**
     * @return mixed
     */
    public static function getWebPagePath()
    {
        self::init();
        return self::$webPagePath;
    }

    /**
     * @return mixed
     */
    public static function getTrueWebPagePath()
    {
        self::init();
        return self::$trueWebPagePath;
    }
}