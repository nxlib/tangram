<?php

namespace Tangram\Console;

use Tangram\Handler\Data\ModuleMap;
use Tangram\Handler\Data\TangramData;
use Tangram\Handler\Data\UriMap;
use Tangram\Handler\File\AuthMapFile;
use Tangram\Handler\File\AutoLoadFile;
use Tangram\Handler\File\ClassMapFile;
use Tangram\Handler\File\PermissionMapFile;
use Tangram\Handler\File\RealFile;
use Tangram\Handler\File\RouterMapFile;
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

        $trueModulePath = TangramData::getTrueModulePath();
        $pathInfo = Dir::scan($trueModulePath, 3);
        $modules = [];
        foreach ($pathInfo as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $modules[] = $key . DIRECTORY_SEPARATOR . $k;
                }
            }
        }

        $moduleMap = new ModuleMap($modules);
        ClassMapFile::generate($moduleMap->getClassMap());
        AuthMapFile::generate($moduleMap->getAuthMap());

        $uriMap = new UriMap($moduleMap->getUriList());
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
}
