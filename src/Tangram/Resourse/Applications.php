<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 22/03/2018
 * Time: 22:40
 */

namespace Tangram\Resourse;


use Tangram\Config\ProjectConfig;
use Tangram\Util\Folder;

class Applications {
    private static $allApplications;

    public static function all(){
        if(is_null(static::$allApplications)){
            $projectConfig = new ProjectConfig();
            $list = Folder::scan(getcwd().DIRECTORY_SEPARATOR.$projectConfig->getApplicationPath());
            static::$allApplications = array_keys($list);
        }
        return static::$allApplications;
    }
    public static function applicationExist(string $applicationName):bool
    {
        return in_array($applicationName,static::$allApplications);
    }
}