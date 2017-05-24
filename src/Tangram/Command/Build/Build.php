<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 03/03/2017
 * Time: 01:39
 */

namespace Tangram\Command\Build;


use Tangram\Handler\Data\ClassMap;
use Tangram\Handler\Data\PathData;
use Tangram\Handler\Data\UriMap;
use Tangram\Handler\File\AuthMapFile;
use Tangram\Handler\File\AutoLoadFile;
use Tangram\Handler\File\ClassMapFile;
use Tangram\Handler\File\DefaultDir;
use Tangram\Handler\File\PermissionMapFile;
use Tangram\Handler\File\RealFile;
use Tangram\Handler\File\RouterMapFile;
use Tangram\Handler\File\ViewsPathFile;
use Tangram\Utils\Dir;

class Build
{
    private static $classMapFlag = [];

    public static function run($modulePath,$restfulPath,$webPagePath){
        $modulePath = new PathData($modulePath);
        $restfulPath = new PathData($restfulPath);
        $webPagePath = new PathData($webPagePath);

        $modulesScan = Dir::scan($modulePath->getAbsolutePath(),3);
        $restfulScan = Dir::scan($restfulPath->getAbsolutePath(),3);
        $webPageScan = Dir::scan($webPagePath->getAbsolutePath(),3);

        DefaultDir::init(dirname($restfulPath->getPath()));

        $moduleMap = new ClassMap($modulesScan,$modulePath);
        $restfulMap = new ClassMap($restfulScan,$restfulPath);
        $webPageMap = new ClassMap($webPageScan,$webPagePath);

        self::checkClassMapExist($moduleMap->getClassMap());
        self::checkClassMapExist($restfulMap->getClassMap());
        self::checkClassMapExist($webPageMap->getClassMap());
        $mergeClassMap = array_merge($moduleMap->getClassMap(),$restfulMap->getClassMap(),$webPageMap->getClassMap());
//        console($moduleMap->getClassMap());
//        console($restfulMap->getClassMap());
//        console($webPageMap->getClassMap());
//        console($mergeClassMap);
//        exit;
        ClassMapFile::generate($mergeClassMap);
        ViewsPathFile::generate($webPageMap->getViewsPathMap());

//        console($restfulMap->getAuthMap());
//        console($webPageMap->getAuthMap());

        $mergeAuthMap = array_merge($restfulMap->getAuthMap(),$webPageMap->getAuthMap());
//        console($mergeAuthMap);
        AuthMapFile::generate($mergeAuthMap);

        $mergeUriMap = array_merge($restfulMap->getUriList(),$webPageMap->getUriList());
        $uriMap = new UriMap($mergeUriMap);

        PermissionMapFile::generate($uriMap->getPermissionMap());
        RouterMapFile::generate($uriMap->getRouterMap());

        $md5 = md5(time());
        RealFile::generate($md5);
        AutoLoadFile::generate($md5);
    }
    private static function checkClassMapExist($classMap){
        if(!empty($classMap) && is_array($classMap)){
            foreach ($classMap as $key => $value){
                $flag = str_replace("\\","",$key);
                if(in_array($flag,self::$classMapFlag)){
                    console("ERROR:");
                    console("Namespace Exist => {$key}");
                    exit;
                }
                self::$classMapFlag[] = $flag;
            }
        }
    }

}