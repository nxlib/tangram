<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:43
 */

namespace Tangram\AutoFile;


use Tangram\Utils\Dir;

class DefaultDir
{
    const TANGRAM_MODULE = 'tangram-modules';
    const AUTO_TANGRAM_FOLDER = self::TANGRAM_MODULE . DIRECTORY_SEPARATOR . 'auto-tangram';
    private static $tangramSavePath;
    private static $autoTangramSavePath;

    public static function init($applicationPath)
    {
        self::$tangramSavePath = $applicationPath.DIRECTORY_SEPARATOR.self::TANGRAM_MODULE;
        self::$autoTangramSavePath = $applicationPath.DIRECTORY_SEPARATOR.self::AUTO_TANGRAM_FOLDER;

        Dir::create(self::$tangramSavePath);
        Dir::create(self::$autoTangramSavePath);
    }
    public static function tangramSavePath(){
        return self::$tangramSavePath;
    }
    public static function autoTangramSavePath(){
        return self::$autoTangramSavePath;
    }
}