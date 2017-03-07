<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tangram\Console;

use Symfony\Component\Console\Output\ConsoleOutput;
use Tangram\Tangram;
use Tangram\Utils\Dir;
use Tangram\Utils\File;

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
    public function run(){
        if(TG_COMMAND == ""){
            $this->info();
            echo $this->getHelp();
            exit;
        }
        //command
        if(TG_COMMAND != 'build'){
            exit("command \"".TG_COMMAND." \" not found!");
        }
        $tangramJsonFile = TG_RUN_PATH.DIRECTORY_SEPARATOR."tangram.json";
        if(!file_exists($tangramJsonFile)){
            exit("Error: tangram.js not found");
        }
        $tangramData = json_decode(file_get_contents($tangramJsonFile),1);
        $modulePath = 'modules';
        $tangramModule = $modulePath.DIRECTORY_SEPARATOR.'tangram-modules';
        $autoTrangram = $modulePath.DIRECTORY_SEPARATOR.'tangram-modules'.DIRECTORY_SEPARATOR.'auto-tangram';

        if(isset($tangramData['modules-path']) && !empty($tangramData['modules-path'])){
            $modulePath = $tangramData['modules-path'];
        }
        $trueModulePath = TG_RUN_PATH.DIRECTORY_SEPARATOR.$modulePath;
        if(!file_exists($trueModulePath)){
            exit("Error: module path not found");
        }
        $pathInfo = Dir::scan($trueModulePath,3);
        $modules = [];
        foreach ($pathInfo as $key => $value){
            if(is_array($value)){
                foreach ($value as $k => $v){
                    $modules[] = $key.DIRECTORY_SEPARATOR.$k;
                }
            }
        }
        Dir::create($tangramModule);
        Dir::create($autoTrangram);

        $classMap = [];
        $routerMap = [];
        $permissionMap = [];
        $namespaces = [];
        foreach ($modules as $value){
            $json = $trueModulePath.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR."tangram.json";
            if(file_exists($json)){
                $json = json_decode(file_get_contents($json),1);
                if(isset($json['autoload']['psr-4']) && !empty($json['autoload']['psr-4'])){
                    foreach ($json['autoload']['psr-4'] as $key => $psr4){
                        $namespaces[] = [
                            'ns' => $key,
                            'path' => $trueModulePath.DIRECTORY_SEPARATOR.$value
                        ];
                        $tmp = str_replace('\\','\\\\',$key);
                        if(empty($psr4)){
                            $psr4 = $modulePath.'/'.$value;
                        }
                        $classMap[] = "        '{$tmp}' => ['{$psr4}']";
                    }
                }

            }
        }
//        console($classMap);
//        console($this->classMapFile($classMap));
        File::create($autoTrangram.DIRECTORY_SEPARATOR.'autoload_classmap.php',$this->classMapFile($classMap));
        $md5 = md5(time());
        //todo permission-map
        //todo rest-permission-map
        //todo router-map
        foreach ($namespaces as $module){
            console($module);
            $path = $module['path'].DIRECTORY_SEPARATOR.'controller';
            $namespace = $module['ns'].'Controller';
            if(file_exists($path)){
                $files = Dir::scan($path);
                if(empty($files)){
                    continue;
                }
                foreach ($files as $file){
                    include $path.DIRECTORY_SEPARATOR.$file;
                    $controller = str_replace('.php','',$file);
                    $reflect = new \ReflectionClass($namespace.'\\'.$controller);
                    console($reflect->getMethods());
                    console($reflect->getDocComment());
                }


            }
        }

    }
    private function info(){
        console(self::$logo);
        $this->getVersion();
        $this->getLine();
    }
    private function getLine(){
        console(self::$line);
    }
    private function getVersion(){
        console("@version:".Tangram::VERSION);
    }
    private function getHelp(){
        return <<<'EOF'
command
    build [module] [--option]
    option:--router --permission
    eg: build tangram/demo --router
EOF;
    }
    private function classMapFile($data){
        $str = implode(",\r\n",$data);
        return <<<"EOF"
<?php
class AutoLoadClassMap{
    private static \$map = [
{$str}
    ];
    public static function getMap()
    {
        return static::\$map;
    }
}
EOF;
    }
    private function routerMapFile($data){
        $get = implode(",\r\n",$data);
        $post = implode(",\r\n",$data);
        $put = implode(",\r\n",$data);
        $del = implode(",\r\n",$data);
        return <<<"EOF"
<?php
class AutoRouterMap
{
    private static \$map = [
        'GET' => [
{$get}
        ],
        'POST' => [
{$post}
        ],
        'PUT' => [
{$put}
        ],
        'DELETE' => [
{$del}
        ]
    ];
    public static function getMap()
    {
        return static::\$map;
    }
}
EOF;
    }
    private function permissionMapFile($data){
        $str = implode(",\r\n",$data);
        return <<<"EOF"
<?php
class AutoPermissionMap
{
    private static \$map = [
{$str}
    ];
    public static function getMap()
    {
        return static::\$map;
    }
}
EOF;
    }
    private function readFile($md5){
        return <<<"EOF"
<?php
include "autoload_router_map.php";
include "autoload_permission_map.php";
include "autoload_classmap.php";

class TangramAutoloaderInit{$md5}(){
    public static function getLoader(){
        return;
    }
}
EOF;
    }
    private function autoloadFile($md5){
        return <<<"EOF"
<?php

// autoload.php @generated by Tangram

require_once __DIR__ . '/auto-tangram' . '/autoload_real.php';

return TangramAutoloaderInit{$md5}::getLoader();
EOF;
    }
    private function initFolder($root){
        if(!file_exists($root.DIRECTORY_SEPARATOR.'tangram-modules')){
            mkdir($root.DIRECTORY_SEPARATOR.'tangram-modules');
        }
        if(!file_exists($root.DIRECTORY_SEPARATOR.'tangram-modules'.DIRECTORY_SEPARATOR.'auto-tangram')){
            mkdir($root.DIRECTORY_SEPARATOR.'tangram-modules');
        }
    }

}
