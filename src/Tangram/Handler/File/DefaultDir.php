<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:43
 */

namespace Tangram\Handler\File;


class DefaultDir
{
    const TANGRAM_MODULE = 'tangram-modules';
    const AUTO_TANGRAM_FOLDER = self::TANGRAM_MODULE . DIRECTORY_SEPARATOR . 'auto-tangram';

    public static function init()
    {
        Dir::create(self::TANGRAM_MODULE);
        Dir::create(self::AUTO_TANGRAM_FOLDER);
    }
}