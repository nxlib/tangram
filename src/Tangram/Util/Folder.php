<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 22/03/2018
 * Time: 22:37
 */

namespace Tangram\Util;

class Folder {
    public static function scan($path, $deep = 1, $ignore = [])
    {
        if (!file_exists($path)) {
            return [];
        }
        if ($deep === 0 || empty($path)) {
            return [];
        }
        $_ignore = array_merge(['.', '..', '.DS_Store', '.gitkeep', '.svn'], $ignore);
        $dir = scandir($path);
        $rs = [];
        foreach ($dir as $key => $v) {
            if (in_array($v, $_ignore)) {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $v)) {
                $rs[$v] = self::scan($path . DIRECTORY_SEPARATOR . $v, $deep - 1);
            } else {
                $rs[] = $v;
            }
        }
        return $rs;
    }
}