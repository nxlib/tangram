<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:21
 */

namespace Tangram\Handler\Data;


class TangramData
{
    private $name;
    private $moduleName;
    private $uriPrefix;
    private $require;
    private $autoloadPsr4;
    private $data;
    private $viewsPath;

    public function __construct($filePath)
    {
        $json = json_decode(file_get_contents($filePath),1);
        if(!isset($json['name']) || !isset($json['module']) || !isset($json['require']) || !isset($json['autoload']['psr-4'])){
            console("ERROR:");
            console("FILE:{$filePath}");
            exit("MESSAGE: this is not a tangram json");
        }
        $this->name = $json['name'];
        $this->viewsPath = isset($json['views-path']) ? $json['views-path'] : "views";
        $this->moduleName = $json['module'];
        $this->require = $json['require'];
        $this->autoloadPsr4 = $json['autoload']['psr-4'];
        if(isset($json['uri-prefix'])){
            $this->uriPrefix = $json['uri-prefix'];
        }
        $this->data = $json;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @return mixed
     */
    public function getUriPrefix()
    {
        return $this->uriPrefix;
    }

    /**
     * @return mixed
     */
    public function getRequire()
    {
        return $this->require;
    }

    /**
     * @return mixed
     */
    public function getAutoloadPsr4()
    {
        return $this->autoloadPsr4;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getViewsPath(): string
    {
        return $this->viewsPath;
    }
}