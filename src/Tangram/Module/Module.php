<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 25/03/2018
 * Time: 18:58
 */

namespace Tangram\Module;


use Tangram\Config\Exception\ModuleConfigException;
use Tangram\Config\ModuleConfig;
use Tangram\IO\IOInterface;
use Tangram\Tangram;

class Module {

    /**
     * @var \Tangram\Config\ModuleConfig
     */
    private $config;

    /**
     * @var string
     */
    private $absolutePath;

    /**
     * @return \Tangram\Config\ModuleConfig
     */
    public function getConfig(): \Tangram\Config\ModuleConfig
    {
        return $this->config;
    }

    public function __construct(string $moduleName,string $absoluteModulePath,IOInterface $io)
    {
        try{
            $this->config = new ModuleConfig($moduleName,$absoluteModulePath);
            $this->absolutePath = $absoluteModulePath.DIRECTORY_SEPARATOR.$moduleName;
        }catch (ModuleConfigException $e) {
            $io->writeError($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }
}