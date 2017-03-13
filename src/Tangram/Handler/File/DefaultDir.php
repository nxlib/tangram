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
    const AUTO_TANGRAM_FOLDER = TANGRAM_MODULE . DIRECTORY_SEPARATOR . 'auto-tangram';

    public static function init()
    {
        Dir::create(TANGRAM_MODULE);
        Dir::create(AUTO_TANGRAM_FOLDER);
    }
}