<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/14
 * Time: 16:08
 */

namespace Tangram\Handler\Data;


use Nette\Reflection\ClassType;
use Tangram\Utils\Dir;

class ClassMap
{
    private $classMap = [];
    private $namespaces = [];
    private $uriList = [];
    private $authMap = [];

    public function __construct(array $modulesScan,PathData $pathData)
    {
        $classMap = [];
        $namespaces = [];
        $modules = [];
        if(!empty($modulesScan)){
            foreach ($modulesScan as $key => $value) {
                if($value == "tangram.json"){
                    $modules[] = $key;
                    continue;
                }
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if($v == "tangram.json"){
                            $modules[] = $key;
                            continue;
                        }
                        if(is_array($v)){
                            foreach ($v as $itemKey => $item){
                                if($item == "tangram.json"){
                                    $modules[] = $key.DIRECTORY_SEPARATOR.$k;
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!empty($modules)) {
            $trueModulePath = $pathData->getAbsolutePath();
            $modulePath = $pathData->getPath();
            foreach ($modules as $value) {
                $tangramData = new TangramData($trueModulePath . DIRECTORY_SEPARATOR . $value . DIRECTORY_SEPARATOR . "tangram.json");
                foreach ($tangramData->getAutoloadPsr4() as $key => $psr4) {
                    $namespaces[] = [
                        'ns' => $key,
                        'path' => $trueModulePath . "/" . $value,
                        'name' => $tangramData->getModuleName(),
                        'uri-prefix' => $tangramData->getUriPrefix(),
                        'tangramData' => $tangramData
                    ];
                    $tmp = str_replace('\\', '\\\\', $key);
                    if (empty($psr4)) {
                        $psr4 = $modulePath . '/' . $value;
                    }else{
                        $psr4 = $modulePath . '/' . $value."/".$psr4;
                    }
                    $classMap[$tmp] = str_replace("\\","/",$psr4);
                }
            }
            $this->classMap = $classMap;
            $this->namespaces = $namespaces;
        }
    }

    public function getClassMap()
    {
        return $this->classMap;
    }

    public function getNamespace()
    {
        return $this->namespaces;
    }

    public function getUriList()
    {
        $this->uriMapHandler();
        return $this->uriList;
    }

    public function getAuthMap()
    {
        $this->uriMapHandler();
        return $this->authMap;
    }

    private function uriMapHandler()
    {
        if (!empty($this->uriList)) {
            return;
        }
        $uriList = [];
        foreach ($this->namespaces as $module) {
            $path = $module['path'] . DIRECTORY_SEPARATOR . 'controller';
            $namespace = $module['ns'];
            $modulePath = $module['name'];
            if (file_exists($path)) {
                $files = Dir::scan($path);
                if (empty($files)) {
                    continue;
                }
                foreach ($files as $file) {
                    include $path . DIRECTORY_SEPARATOR . $file;
                    $controller = str_replace('.php', '', $file);
                    $reflect = new ClassType($namespace . '\\' . $controller);
                    $isAuth = $reflect->getAnnotation("Auth");
                    $isRestController = $reflect->getAnnotation("RestController");
                    $requestMapping = $reflect->getAnnotation("RequestMapping");
                    $mainPermission = $reflect->getAnnotation("Permission");
                    $requestPath = str_replace($reflect->getNamespaceName() . '\\', '', $reflect->getName());
                    $requestPath = '/' .$module['uri-prefix'].'/'. strtolower($modulePath) . '/' . strtolower(str_replace('Controller', '', $requestPath));
                    $moduleName = "";

                    if (!empty($requestMapping)) {
                        if (is_string($requestMapping)) {
                            $requestPath = rtrim($requestMapping, "/**");
                        }
                        if (isset($requestMapping->path)) {
                            $requestPath = rtrim($requestMapping->path, "/**");
                        }
                    }
                    if (isset($mainPermission->module)) {
                        $moduleName = $mainPermission->module;
                    }
                    //auth-handler
                    if (!is_null($isAuth) && boolval($isAuth)) {
                        $this->authMap[$requestPath . "/**"] = true;
                    }
                    $methods = $reflect->getMethods();
                    if (!empty($methods)) {
                        foreach ($methods as $method) {
                            if ($method->isPublic()) {
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
                                $uri = $requestPath . "/";
                                $uri = str_replace("//","/",$uri);
                                $uri = str_replace("\\","/",$uri);
                                $requestMethod = "GET";
                                if (empty($methodRequestMapping)) {
                                    //
                                    $uri .= $method->name;
                                } else {
                                    if (is_string($methodRequestMapping)) {
                                        if (empty($requestMapping)) {
                                            $uri = $methodRequestMapping;
                                        } else {
                                            $uri .= $methodRequestMapping;
                                        }
                                    }
                                    if (isset($methodRequestMapping->path)) {
                                        if (empty($requestMapping)) {
                                            $uri = $methodRequestMapping->path;
                                        } else {
                                            $uri .= $methodRequestMapping->path;
                                        }
                                    }

                                    $requestMethod = isset($methodRequestMapping->method) ? $methodRequestMapping->method : $requestMethod;
                                    $requestMethod = strtoupper($requestMethod);
                                }
                                if (!empty($viewPermission)) {
                                    //页面权限优先级高于功能权限
                                    $permission['module'] = isset($methodPermission->module) ? $methodPermission->module : $permission['module'];
                                    $permission['nav'] = isset($methodPermission->nav) ? $methodPermission->nav : $permission['nav'];
                                    $permission['menu'] = isset($methodPermission->menu) ? $methodPermission->menu : $permission['menu'];
                                } else {
                                    //
                                    $permission['module'] = isset($methodPermission->module) ? $methodPermission->module : $permission['module'];
                                    $permission['name'] = isset($methodPermission->name) ? $methodPermission->name : $permission['name'];
                                }
                                $uriList[] = [
                                    'uri' => str_replace(DIRECTORY_SEPARATOR,"/",$uri),
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
                                if (!is_null($methodAuth)) {
                                    $authKey = $requestMethod . '#' . $uri;
                                    $this->authMap[$authKey] = boolval($methodAuth);
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->uriList = $uriList;
    }
}