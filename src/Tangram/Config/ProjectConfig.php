<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 09/02/2018
 * Time: 15:37
 */

namespace Tangram\Config;


class ProjectConfig {
    private $projectRoot;
    private $require;
    private $namespace;
    private $modulePath;
    private $applicationPath;
    private $webControllerSign;
    private $restControllerSign;

    public function __construct() {
        $this->projectRoot = getcwd();
        $configFile = $this->projectRoot.DIRECTORY_SEPARATOR."tangram.json";
        $data = json_decode(file_get_contents($configFile),1);
        $this->require = $data['require'];
        $this->namespace = $data['namespace'];
        $this->modulePath = $data['path']['modules'] ?? 'modules';
        $this->applicationPath = $data['path']['applications'] ?? 'applications';
        $this->webControllerSign = $data['controller-sign']['web'] ?? 'web-page';
        $this->restControllerSign = $data['controller-sign']['restful'] ?? 'restful';
    }

    /**
     * @return mixed
     */
    public function getRequire() {
        return $this->require;
    }

    /**
     * @return mixed
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getModulePath() {
        return $this->modulePath;
    }

    /**
     * @return mixed
     */
    public function getApplicationPath() {
        return $this->applicationPath;
    }

    /**
     * @return mixed
     */
    public function getWebControllerSign() {
        return $this->webControllerSign;
    }

    /**
     * @return mixed
     */
    public function getRestControllerSign() {
        return $this->restControllerSign;
    }

    /**
     * @return mixed
     */
    public function getProjectRoot() {
        return $this->projectRoot;
    }
}