<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 09/02/2018
 * Time: 19:47
 */

namespace Tangram\Resourse;


use Tangram\Config\ProjectConfig;
use Tangram\Util\Folder;

class Modules {

    private static $allModules;

    public static function all(){
        if(is_null(static::$allModules)){
            $projectConfig = new ProjectConfig();
            $list = Folder::scan(getcwd().DIRECTORY_SEPARATOR.$projectConfig->getModulePath());
            static::$allModules = array_keys($list);
            return static::$allModules;
        }
        return [];
    }
    public static function moduleExist(string $moduleName):bool
    {
        return in_array($moduleName,static::$allModules);
    }
}