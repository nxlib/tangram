<?php

namespace Tangram\Console;

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
use Tangram\Tangram;
use Tangram\Utils\Dir;

class Application
{
    private static $logo = '   ________
  /__   __/__     ___   _____  _____ ___     ____ ___
    / / / __ `/  / __ \/ __  \/ ___/ __ `/  / __ `__ \ 
   / / / /__| \ / / / / /_ / / /  / /__| \ / / / / / / 
  /_/  \____\ \/_/ /_/\___/ /_/   \____\ \/_/ /_/ /_/ 
             `-`      __ / /            `-`
                     /____/   
';
    private static $line = '- - - - - - - - - - - - - - - - - - - - - - - - - - -';

    private static $classMapFlag = [];

    public function __construct()
    {
        //todo
    }

    public function run()
    {
        if (TG_COMMAND == "") {
            $this->info();
            echo $this->getHelp();
            exit;
        }
        //command
        if (TG_COMMAND != 'build') {
            exit("command \"" . TG_COMMAND . " \" not found!");
        }
        $customVendor = TG_RUN_PATH.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
        console($customVendor);
        if(file_exists($customVendor)){
            include $customVendor;
            if(class_exists("\\NxLib\\Core\\MVC")){
                \NxLib\Core\MVC::init(TG_RUN_PATH);
            }
        }
        $projectTangramFile = TG_RUN_PATH.DIRECTORY_SEPARATOR."tangram.json";
        if(!file_exists($projectTangramFile)){
            exit("project tangram.json not found!");
        }
        //init path
        $modulePath = "modules";
        $restfulPath = "restful";
        $webPagePath = "web-page";

        $projectTangramData = json_decode(file_get_contents($projectTangramFile),1);
        if(isset($projectTangramData['modules-path'])){
            $modulePath = $projectTangramData['modules-path'];
        }
        if(isset($projectTangramData['restful-path'])){
            $restfulPath = $projectTangramData['restful-path'];
        }
        if(isset($projectTangramData['web-page-path'])){
            $webPagePath = $projectTangramData['web-page-path'];
        }
        $modulePath = new PathData($modulePath);
        $restfulPath = new PathData($restfulPath);
        $webPagePath = new PathData($webPagePath);

        $modulesScan = Dir::scan($modulePath->getAbsolutePath(),3);
        $restfulScan = Dir::scan($restfulPath->getAbsolutePath(),3);
        $webPageScan = Dir::scan($webPagePath->getAbsolutePath(),3);

        DefaultDir::init();
        $moduleMap = new ClassMap($modulesScan,$modulePath);
        $restfulMap = new ClassMap($restfulScan,$restfulPath);
        $webPageMap = new ClassMap($webPageScan,$webPagePath);

        $this->checkClassMapExist($moduleMap->getClassMap());
        $this->checkClassMapExist($restfulMap->getClassMap());
        $this->checkClassMapExist($webPageMap->getClassMap());
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

    private function info()
    {
        console(self::$logo);
        $this->getVersion();
        $this->getLine();
    }

    private function getLine()
    {
        console(self::$line);
    }

    private function getVersion()
    {
        console("@version:" . Tangram::VERSION);
    }

    private function getHelp()
    {
        return <<<'EOF'
command
    build [module] [--option]
    option:--router --permission
    eg: build tangram/demo --router
EOF;
    }
    private function checkClassMapExist($classMap){
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
