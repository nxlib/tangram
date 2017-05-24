<?php

namespace Tangram\Console;

use Tangram\Command\Build\Build;
use Tangram\Command\Create\Create;
use Tangram\Handler\Data\Applications;
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

    private static $commandList = ['build','create'];

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
        if (!in_array(TG_COMMAND,static::$commandList)) {
            exit("command \"" . TG_COMMAND . " \" not found!");
        }
        $customVendor = TG_RUN_PATH.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
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
        $applicationPath = "applications";
        $restfulSign = "restful";
        $webPageSign = "web-page";


        $projectTangramData = json_decode(file_get_contents($projectTangramFile),1);

        if(isset($projectTangramData["path"]['modules'])){
            $modulePath = $projectTangramData["path"]['modules'];
        }
        if(isset($projectTangramData["path"]['applications'])){
            $applicationPath = $projectTangramData["path"]['applications'];
        }
        if(isset($projectTangramData["controller-sign"]['web'])){
            $webPageSign = $projectTangramData["controller-sign"]['web'];
        }
        if(isset($projectTangramData["controller-sign"]['restful'])){
            $restfulSign = $projectTangramData["controller-sign"]['restful'];
        }

        $applications = Applications::scan(TG_RUN_PATH.DIRECTORY_SEPARATOR.$applicationPath);
        if(empty($applications)){
            exit("folder `applications` not found!");
        }

        if(TG_COMMAND == 'build'){
            foreach ($applications as $value){
                $restful = $applicationPath.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.$restfulSign;
                $web = $applicationPath.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.$webPageSign;
                Build::run($modulePath,$restful,$web);
            }

        }
        if(TG_COMMAND == 'create'){
            Create::run($modulePath);
        }
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

}
