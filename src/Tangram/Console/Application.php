<?php
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
            exit("Error: {$tangramJsonFile} not found");
        }
        $tangramData = json_decode(file_get_contents($tangramJsonFile),1);
        $modulePath = 'modules';
        $tangramModule = 'tangram-modules';
        $autoTrangram = 'tangram-modules'.DIRECTORY_SEPARATOR.'auto-tangram';

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
        $authMap = [];
        $namespaces = [];
        foreach ($modules as $value){
            $json = $trueModulePath.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR."tangram.json";
            if(file_exists($json)){
                $json = json_decode(file_get_contents($json),1);
                if(!isset($json['name'])){
                    die("ERROR:\"{$json}\" do not have \"name\" key");
                }
                if(substr_count($json['name'],"/") != 1){
                    die("ERROR:\"{$json}\" => \"name\" unavailable");
                }
                if(isset($json['autoload']['psr-4']) && !empty($json['autoload']['psr-4'])){
                    $nameExplode = explode("/",$value);
                    foreach ($json['autoload']['psr-4'] as $key => $psr4){
                        $namespaces[] = [
                            'ns' => $key,
                            'path' => $trueModulePath.DIRECTORY_SEPARATOR.$value,
                            'name' => end($nameExplode)
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
            $path = $module['path'].DIRECTORY_SEPARATOR.'controller';
            $namespace = $module['ns'].'Controller';
            $moduledPath = $module['name'];
            if(file_exists($path)){
                $files = Dir::scan($path);
                if(empty($files)){
                    continue;
                }
                foreach ($files as $file){
                    include $path.DIRECTORY_SEPARATOR.$file;
                    $controller = str_replace('.php','',$file);
                    $reflect = new ClassType($namespace.'\\'.$controller);
                    $isAuth = $reflect->getAnnotation("Auth");
                    $isRestController = $reflect->getAnnotation("RestController");
                    $requestMapping = $reflect->getAnnotation("RequestMapping");
                    $mainPermission = $reflect->getAnnotation("Permission");
                    $requestPath = str_replace($reflect->getNamespaceName().'\\','',$reflect->getName());
                    $requestPath = '/'.strtolower($moduledPath).'/'.strtolower(str_replace('Controller','',$requestPath));
                    $moduleName = "";

                    if(!empty($requestMapping)){
                        if(is_string($requestMapping)){
                            $requestPath = rtrim($requestMapping,"/**");
                        }
                        if(isset($requestMapping->path)){
                            $requestPath = rtrim($requestMapping->path,"/**");
                        }
                    }
                    if(isset($mainPermission->module)){
                        $moduleName = $mainPermission->module;
                    }
                    //auth-handler
                    if(!is_null($isAuth) && boolval($isAuth)){
                        $authMap[$requestPath."/**"] = true;
                    }
                    $methods = $reflect->getMethods();
                    if(!empty($methods)){
                        foreach ($methods as $method){
                            if($method->isPublic()){
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
                                $uri = $requestPath."/";
                                $requestMethod = "get";
                                if(empty($methodRequestMapping)){
                                    //
                                    $uri .= $method->name;
                                }else{
                                    if(is_string($methodRequestMapping)){
                                        if(empty($requestMapping)){
                                            $uri = $methodRequestMapping;
                                        }else{
                                            $uri .= $methodRequestMapping;
                                        }
                                    }
                                    if(isset($methodRequestMapping->path)){
                                        if(empty($requestMapping)){
                                            $uri = $methodRequestMapping->path;
                                        }else{
                                            $uri .= $methodRequestMapping->path;
                                        }
                                    }

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
                                    'rest' => $isRestController,
                                    'namespace' => $reflect->getNamespaceName(),
                                    'class' => $reflect->name,
                                    'function' => $method->name
                                ];
                                $methodAuth = $method->getAnnotation('Auth');
                                if(!is_null($methodAuth)){
                                    $authKey = strtolower($requestMethod).'#'.$uri;
                                    $authMap[$authKey] = boolval($methodAuth);
                                }
                            }
                        }
                    }
                }
            }
        }
        if(!empty($uriList)){
            foreach ($uriList as $item){
                $key = strtoupper($item['method']).'#'.$item['uri'];
                if(isset($routerMap[$key])){
                    console($routerMap);
                    die("ERROR:存在相同的URI:\r\n path:{$item['uri']}\r\n method:{$item['method']}\r\n");
                }
                $routerMap[$key] = [
                    'namespace' => $item['namespace'],
                    'class' => $item['class'],
                    'function' => $item['function']
                ];
                if(!empty($item['module']) || !empty($item['nav']) || !empty($item['menu']) || !empty($item['name'])){
                    $permissionMap[$key] = [
                        'module' => $item['module'],
                        'nav' => $item['nav'],
                        'menu' => $item['menu'],
                        'name' => $item['name'],
                        'rest' => $item['rest']
                    ];
                }
            }
        }
        $authMapFileData = [];
        foreach ($authMap as $key => $value){
            if($value){
                $authMapFileData[] = "        '{$key}' => true";
            }else{
                $authMapFileData[] = "        '{$key}' => false";
            }

        }
        File::create($autoTrangram.DIRECTORY_SEPARATOR.'autoload_auth_map.php',$this->authMapFile($authMapFileData));
        $permissionMapFileData = [];
        foreach ($permissionMap as $key => $value){
            $tmp = "";
            foreach ($value as $k => $v){
                if($k == 'rest'){
                    if($v){
                        $tmp .= "'{$k}' => true, ";
                    }else{
                        $tmp .= "'{$k}' => false, ";
                    }

                }else{
                    $tmp .= "'{$k}' => '{$v}', ";
                }
            }
            $tmp = rtrim($tmp,' ,');
            $permissionMapFileData[] = "        '{$key}' => [{$tmp}]";
        }
        File::create($autoTrangram.DIRECTORY_SEPARATOR.'autoload_permission_map.php',$this->permissionMapFile($permissionMapFileData));

        $routerMapFileData = [];
        foreach ($routerMap as $key => $value){
            $tmp = "";
            foreach ($value as $k => $v){
                $tmp .= "'{$k}' => '{$v}', ";
            }
            $tmp = rtrim($tmp,' ,');
            $routerMapFileData[] = "        '{$key}' => [{$tmp}]";
        }
        File::create($autoTrangram.DIRECTORY_SEPARATOR.'autoload_real.php',$this->realFile($md5));

        File::create($tangramModule.DIRECTORY_SEPARATOR.'autoload.php',$this->autoloadFile($md5));

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
        asort($data);
        $str = implode(",\r\n",$data);
        return <<<"EOF"
<?php
class AutoRouterMap
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
    private function authMapFile($data){
        $str = implode(",\r\n",$data);
        return <<<"EOF"
<?php
class AutoAuthMap
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
    private function realFile($md5){
        return <<<"EOF"
<?php
include "autoload_router_map.php";
include "autoload_permission_map.php";
include "autoload_classmap.php";
include "autoload_auth_map.php";

class TangramAutoloaderInit{$md5} {
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
