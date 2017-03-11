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

use Nette\Reflection\ClassType;
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
        //read file
        $uriList = [];
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
                    $reflect = new ClassType($namespace.'\\'.$controller);
                    console($reflect->getName());
                    $isAuth = $reflect->getAnnotation("Auth");
                    $isRestController = $reflect->getAnnotation("RestController");
                    console($reflect->getAnnotation("RequestMapping"));
                    console($reflect->getAnnotation("Permission"));
                    $requestMapping = $reflect->getAnnotation("RequestMapping");
                    $mainPermission = $reflect->getAnnotation("Permission");
                    $path = "";
                    $moduleName = "";

                    if(!empty($requestMapping)){
                        if(is_string($requestMapping)){
                            $path = rtrim($requestMapping,"/**");
                        }
                        if(isset($requestMapping->path)){
                            $path = rtrim($requestMapping->path,"/**");
                        }
                    }
                    if(isset($mainPermission->module)){
                        $moduleName = $mainPermission->module;
                    }

                    $methods = $reflect->getMethods();
                    if(!empty($methods)){
                        console($methods);
                        foreach ($methods as $method){
                            if($method->public){
                                //共有方法才能做权限点
                                $permission = [
                                    'module' => $moduleName,
                                    'nav' => '',
                                    'menu' => '',
                                    'name' => ''
                                ];
                                $methodRequestMapping = $method->getAnnotation('RequestMapping');
                                $viewPermission = $method->getAnnotation('ViewPermission');
                                $methodPermission = $method->getAnnotation('Permission');
                                console($viewPermission);
                                console($methodPermission);
                                $uri = $path."/";
                                var_dump($path);
                                $requestMethod = "get";
                                if(empty($methodRequestMapping)){
                                    //
                                    $uri .= $method->name;
                                }else{
                                    $uri .= isset($methodRequestMapping->path) ? $methodRequestMapping->path:"";
                                    $requestMethod = isset($methodRequestMapping->method) ? $methodRequestMapping->method:$requestMethod;
                                }
                                if(!empty($viewPermission)){
                                    //页面权限优先级高于功能权限
                                    $permission['module'] = isset($methodPermission->module) ? $methodPermission->module : $permission['module'];
                                    $permission['nav'] = isset($methodPermission->nav) ? $methodPermission->nav : $permission['nav'];
                                    $permission['menu'] = isset($methodPermission->menu) ? $methodPermission->menu : $permission['menu'];
                                }else{
                                    //
                                    $permission['module'] = isset($methodPermission->module) ? $methodPermission->module : $permission['module'];
                                    $permission['name'] = isset($methodPermission->name) ? $methodPermission->name : $permission['name'];
                                }
                                $uriList[] = [
                                    'uri' => $uri,
                                    'method' => strtolower($requestMethod),
                                    'module' => $permission['module'],
                                    'nav' => $permission['nav'],
                                    'menu' => $permission['menu'],
                                    'name' => $permission['name'],
                                    'auth' => $isAuth,
                                    'rest' => $isRestController,
                                    'namespace' => $reflect->getNamespaceName(),
                                    'class' => $reflect->name,
                                    'function' => $method->name
                                ];
                            }
                        }
                    }
                }
                console($uriList);
                if(!empty($uriList)){
                    foreach ($uriList as $item){
                        $key = strtoupper($item['method']).'#'.$item['uri'];
                        if(isset($routerMap[$key])){
                            die("ERROR:存在相同的URI:\r\n path:{$item['uri']}\r\n method:{$item['method']}\r\n");
                        }
                        $routerMap[$key] = [
                            'namespace' => $item['namespace'],
                            'class' => $item['class'],
                            'function' => $item['function']
                        ];
                        //todo
                    }
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
        $get = implode(",\r\n",$data['get']);
        $post = implode(",\r\n",$data['post']);
        $put = implode(",\r\n",$data['put']);
        $del = implode(",\r\n",$data['del']);
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
