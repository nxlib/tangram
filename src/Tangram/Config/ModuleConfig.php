<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 09/02/2018
 * Time: 15:37
 */

namespace Tangram\Config;


class ModuleConfig {
    private $name;
    private $module;
    private $description;
    private $keywords;
    private $type;
    private $license;
    private $authors;
    private $require;
    private $requireModule;
    private $minimumStability;
    private $psr4Autoload;

    public function __construct(string $module) {
        $projectConfig = new ProjectConfig();
        $modulePath = $projectConfig->getProjectRoot().DIRECTORY_SEPARATOR.$projectConfig->getModulePath();
        $data = json_decode($modulePath.DIRECTORY_SEPARATOR."tangram.json",1);
        $this->name = $data['name'];
        $this->module = $data['module'];
        $this->description = $data['description'];
        $this->keywords = $data['keywords'];
        $this->type = $data['type'];
        $this->license = $data['license'];
        $this->authors = $data['authors'];
        $this->require = $data['require'];
        $this->requireModule = $data['require-module'] ?? [];
        $this->psr4Autoload = $data['autoload']['psr-4'] ?? [];
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLicense() {
        return $this->license;
    }

    /**
     * @return mixed
     */
    public function getAuthors() {
        return $this->authors;
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
    public function getRequireModule() {
        return $this->requireModule;
    }

    /**
     * @return mixed
     */
    public function getMinimumStability() {
        return $this->minimumStability;
    }

    /**
     * @return array
     */
    public function getPsr4Autoload(): array {
        return $this->psr4Autoload;
    }

}