<?php
/**
 * Author: garming
 * Date: 2018/4/7
 * Time: 12:46
 */

namespace Tangram\Reflection;


use Nette\Reflection\ClassType;
use Tangram\Application\Application;
use Tangram\Exception\ClassExistException;
use Tangram\Util\Folder;

class AnnotationReflection {
    private static $annotationMap = [];
    private static function analyze(Application $application)
    {
        if(!isset(static::$annotationMap[$application->getConfig()->getName()])){
            $applicationName = $application->getConfig()->getName();
            //todo
//            include '/Users/garming/workbench/my-open-source/tangram/bin/applications/admin/public/index.php';

            $config = $application->getConfig();
            $controllers = [];
            $controllers[] = $application->getAbsolutePath().DIRECTORY_SEPARATOR.$config->getWeb().DIRECTORY_SEPARATOR."controller";
            $controllers[] = $application->getAbsolutePath().DIRECTORY_SEPARATOR.$config->getRestful().DIRECTORY_SEPARATOR."controller";
            foreach ($controllers as $path){
                $scan = Folder::scan($path);
                if(!empty($scan)){
                    foreach ($scan as $file){
                        // get the file content and save to tmp file
                        $clazz = str_replace(".php","",$file);
                        try{
                            list($fileContent,$namespace,$shamNamespace) = static::fileContentHandler($path.DIRECTORY_SEPARATOR.$file);
                        }catch (ClassExistException $ce){
                            $application->getIo()->writeError("<error>".$ce->getMessage()."<error>");
                            return;
                        }
                        $tmpFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.md5($fileContent).".php";
                        file_put_contents($tmpFile,$fileContent);
                        // get the file content and save to tmp file
                        include $tmpFile;

                        $reflect = new ClassType($shamNamespace."\\".$clazz);
                        $isAuth = $reflect->getAnnotation("Auth");
                        $isRestController = $reflect->getAnnotation("RestController");
                        $requestMapping = $reflect->getAnnotation("RequestMapping");
                        $mainPermission = $reflect->getAnnotation("Permission");
//                        pr($mainPermission);
//                        $requestPath = str_replace($reflect->getNamespaceName() . '\\', '', $reflect->getName());
//                        $requestPath = '/' . $module['uri-prefix'] . '/' . strtolower($modulePath) . '/' . strtolower(str_replace('Controller', '', $requestPath));
//                        $requestPath = str_replace("//", "/", $requestPath);
//                        $requestPath = str_replace("\\", "/", $requestPath);
                        $PermissionModuleName = "";
                        $requestPath = "";
                        if (!empty($requestMapping)) {
                            if (is_string($requestMapping)) {
                                $requestPath = rtrim($requestMapping, "/**");
                            }
                            if (isset($requestMapping->path)) {
                                $requestPath = rtrim($requestMapping->path, "/**");
                            }
                        }
                        if (isset($mainPermission->module)) {
                            $PermissionModuleName = $mainPermission->module;
                        }
                        //auth-handler
                        if (!is_null($isAuth) && boolval($isAuth)) {
                            static::$annotationMap[$applicationName]["auth"][$requestPath . "/**"] = true;
                        }
                        $methods = $reflect->getMethods();
                        if (!empty($methods)) {
                            foreach ($methods as $method) {
                                if ($method->isPublic()) {
                                    //公有方法才能做权限点
                                    $permission = [
                                        'module' => $PermissionModuleName,//主权限点
                                        'nav' => '',//导航权限点
                                        'menu' => '',//菜单权限点
                                        'name' => ''//权限名
                                    ];
                                    //读取默认约定
                                    $main_uri = $requestPath . "/";
                                    $main_uri = str_replace("//", "/", $main_uri);
                                    $main_uri = str_replace("\\", "/", $main_uri);

                                    $requestMethod = "GET";
                                    $uri = $method->name;

                                    //忽略魔术方法
                                    if(strpos($uri,"__") === 0){
                                        continue;
                                    }
                                    //fix bug:在phar执行情况下，如果第一个方法没有任何comment,会默认继承class的comment
                                    if ($method->getDocComment()) {
                                        //有method的comment
                                        $methodRequestMapping = $method->getAnnotation('RequestMapping');
                                        $viewPermission = $method->getAnnotation('ViewPermission');
                                        $methodPermission = $method->getAnnotation('Permission');

                                        if (!empty($methodRequestMapping)) {
                                            $uri = "";
                                            if (is_string($methodRequestMapping)) {
                                                if (empty($requestMapping)) {
                                                    $uri = $methodRequestMapping;
                                                } else {
                                                    $uri = $main_uri . $methodRequestMapping;
                                                }
                                            }
                                            if (isset($methodRequestMapping->path)) {
                                                if (empty($requestMapping)) {
                                                    $uri = $methodRequestMapping->path;
                                                } else {
                                                    $uri = $main_uri . $methodRequestMapping->path;
                                                }
                                            }
                                        } else {
                                            $uri = $main_uri . $uri;
                                        }

                                        $requestMethod = isset($methodRequestMapping->method) ? $methodRequestMapping->method : $requestMethod;
                                        $requestMethod = strtoupper($requestMethod);

                                        if (!empty($viewPermission)) {
                                            //页面权限优先级高于功能权限
                                            $permission['module'] = isset($methodPermission->module) ? $methodPermission->module : $permission['module'];
                                            $permission['nav'] = isset($methodPermission->nav) ? $methodPermission->nav : $permission['nav'];
                                            $permission['menu'] = isset($methodPermission->menu) ? $methodPermission->menu : $permission['menu'];
                                        } else {
                                            //功能权限
                                            $permission['module'] = isset($methodPermission->module) ? $methodPermission->module : $permission['module'];
                                            $permission['name'] = isset($methodPermission->name) ? $methodPermission->name : $permission['name'];
                                        }

                                        $methodAuth = $method->getAnnotation('Auth');
                                        if (!is_null($methodAuth)) {
                                            $authKey = $requestMethod . '#' . $uri;
                                            static::$annotationMap[$applicationName]["auth"][$authKey] = boolval($methodAuth);
                                        }
                                    } else {
                                        $uri = $main_uri . $uri;
                                    }

                                    $rs = [
                                        'uri' => str_replace(DIRECTORY_SEPARATOR, "/", $uri),
                                        'method' => strtolower($requestMethod),
                                        'module' => $permission['module'], //模块名
                                        'nav' => $permission['nav'], //
                                        'menu' => $permission['menu'], //菜单名
                                        'name' => $permission['name'], //权限点名称
                                        'rest' => $isRestController,//rest controller will return json
//                                        'namespace' => $reflect->getNamespaceName(),
                                        'namespace' => $namespace,//
                                        'class' => str_replace($reflect->getNamespaceName(),$namespace,$reflect->name),
                                        'function' => $method->name
                                    ];
                                    static::$annotationMap[$applicationName]["router"][] = $rs;
                                    static::$annotationMap[$applicationName]["permission"][] = $rs;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public static function getAnnotations(Application $application,string $annotationName = null)
    {
        static::analyze($application);
        if(!isset(static::$annotationMap[$application->getConfig()->getName()])){
            return [];
        }
        $annotations = static::$annotationMap[$application->getConfig()->getName()];
        if(is_null($annotationName)){
            return $annotations;
        }
        if(!isset($annotations[$annotationName])){
            return [];
        }
        return $annotations[$annotationName];
    }
    private static function fileContentHandler($filePath){
        $file = fopen($filePath, "r");
        $data = "";
        $namespace = "";
        $shamNamespace = "";
        $clazzName = "";
        while (!feof($file)){
            $row = fgets($file);
            if(strpos($row,"namespace") === 0){
                $namespace = explode(" ",$row)[1];
                $namespace = str_replace(";","",trim($namespace));
                $shamNamespace = "N".md5($filePath.$row);
                $data .= "namespace $shamNamespace ;";
                continue;
            }
            if(strpos($row,"class") === 0){
                $clazzName = explode(" ",$row)[1];
                if(strpos($row,"{}") != 0){
                    $data .= "class {$clazzName} {}\r\n";
                }elseif (strpos($row,"{") != 0){
                    $data .= "class {$clazzName} {\r\n";
                }else{
                    $data .= "class {$clazzName}\r\n";
                }
            }else{
                $data .= $row;
            }
            if(!empty($shamNamespace) && $clazzName){
                if(class_exists($shamNamespace."\\".$clazzName)){
                    throw new ClassExistException($namespace."\\".$clazzName." exist");
                }
            }
        }
        fclose($file);
        return [$data,$namespace,$shamNamespace];
    }
}